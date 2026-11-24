<?php

namespace App\Services;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class AssetStorage
{
    private ?S3Client $s3Client = null;

    public function storePublic(UploadedFile $file, string $directory): string
    {
        if (! $this->usesSupabase()) {
            $path = $file->store($directory, config('filesystems.default'));

            if (! is_string($path)) {
                throw new RuntimeException('Berkas unggahan tidak dapat disimpan.');
            }

            return $path;
        }

        $path = trim($directory, '/').'/'.$file->hashName();
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('Berkas unggahan tidak dapat dibaca.');
        }

        $this->uploadToSupabase(false, $path, $contents, $file->getMimeType() ?: 'application/octet-stream');

        return $path;
    }

    public function putPublic(string $path, string $contents, string $mimeType): bool
    {
        if (! $this->usesSupabase()) {
            return Storage::disk(config('filesystems.default'))->put($path, $contents);
        }

        $this->uploadToSupabase(false, $path, $contents, $mimeType);

        return true;
    }

    public function storePrivate(UploadedFile $file, string $directory): string
    {
        if (! $this->usesSupabase()) {
            $path = $file->store($directory, config('filesystems.private_upload_disk'));

            if (! is_string($path)) {
                throw new RuntimeException('Berkas unggahan tidak dapat disimpan.');
            }

            return $path;
        }

        $path = trim($directory, '/').'/'.$file->hashName();
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('Berkas unggahan tidak dapat dibaca.');
        }

        $this->uploadToSupabase(true, $path, $contents, $file->getMimeType() ?: 'application/octet-stream');

        return $path;
    }

    public function putPrivate(string $path, string $contents, string $mimeType): bool
    {
        if (! $this->usesSupabase()) {
            return Storage::disk(config('filesystems.private_upload_disk'))->put($path, $contents);
        }

        $this->uploadToSupabase(true, $path, $contents, $mimeType);

        return true;
    }

    public function deletePublic(array|string $paths): void
    {
        $paths = array_values(array_filter((array) $paths));

        if ($paths === []) {
            return;
        }

        if (! $this->usesSupabase()) {
            Storage::disk(config('filesystems.default'))->delete($paths);

            return;
        }

        $this->deleteFromSupabase(false, $paths);
    }

    public function deletePrivate(array|string $paths): void
    {
        $paths = array_values(array_filter((array) $paths));

        if ($paths === []) {
            return;
        }

        if (! $this->usesSupabase()) {
            Storage::disk(config('filesystems.private_upload_disk'))->delete($paths);

            return;
        }

        $this->deleteFromSupabase(true, $paths);
    }

    public function publicUrl(string $path): string
    {
        if (! $this->usesSupabase()) {
            return Storage::disk(config('filesystems.default'))->url($path);
        }

        $bucket = $this->bucketName(false);
        $baseUrl = rtrim((string) config('filesystems.disks.s3.url'), '/');

        if ($baseUrl !== '') {
            return $baseUrl.'/'.$this->encodePath($path);
        }

        $projectUrl = rtrim((string) config('services.supabase.url'), '/');

        if ($projectUrl === '') {
            throw new RuntimeException('AWS_URL atau SUPABASE_URL belum dikonfigurasi untuk media publik.');
        }

        return $projectUrl.'/storage/v1/object/public/'.rawurlencode($bucket).'/'.$this->encodePath($path);
    }

    public function downloadPublic(string $path, string $filename): Response
    {
        if (! $this->usesSupabase()) {
            return Storage::disk(config('filesystems.default'))->download($path, $filename);
        }

        $object = $this->getSupabaseObject(false, $path);

        return response((string) $object['Body'], LaravelResponse::HTTP_OK, [
            'Content-Type' => $object['ContentType'] ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.addslashes(basename($filename)).'"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function privateResponse(string $path): Response
    {
        if (! $this->usesSupabase()) {
            return Storage::disk(config('filesystems.private_upload_disk'))->response($path, null, [
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $object = $this->getSupabaseObject(true, $path);

        return response((string) $object['Body'], LaravelResponse::HTTP_OK, [
            'Content-Type' => $object['ContentType'] ?? 'application/octet-stream',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function uploadToSupabase(bool $private, string $path, string $contents, string $mimeType): void
    {
        $this->s3Client()->putObject([
            'Bucket' => $this->bucketName($private),
            'Key' => $path,
            'Body' => $contents,
            'ContentType' => $mimeType,
        ]);
    }

    private function deleteFromSupabase(bool $private, array $paths): void
    {
        $bucket = $this->bucketName($private);

        foreach ($paths as $path) {
            $this->s3Client()->deleteObject([
                'Bucket' => $bucket,
                'Key' => $path,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getSupabaseObject(bool $private, string $path): array
    {
        try {
            return $this->s3Client()->getObject([
                'Bucket' => $this->bucketName($private),
                'Key' => $path,
            ])->toArray();
        } catch (AwsException $exception) {
            if ($exception->getStatusCode() === LaravelResponse::HTTP_NOT_FOUND) {
                abort(404);
            }

            throw $exception;
        }
    }

    private function s3Client(): S3Client
    {
        if ($this->s3Client !== null) {
            return $this->s3Client;
        }

        $config = config('filesystems.disks.s3');
        $required = [
            'key' => $config['key'] ?? null,
            'secret' => $config['secret'] ?? null,
            'region' => $config['region'] ?? null,
            'endpoint' => $config['endpoint'] ?? null,
        ];

        if (in_array(null, $required, true) || in_array('', $required, true)) {
            throw new RuntimeException('Konfigurasi Supabase Storage S3 belum lengkap. Periksa AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, dan AWS_ENDPOINT.');
        }

        return $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $required['region'],
            'endpoint' => $required['endpoint'],
            'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
            'credentials' => [
                'key' => $required['key'],
                'secret' => $required['secret'],
            ],
        ]);
    }

    private function bucketName(bool $private): string
    {
        $bucket = (string) config($private
            ? 'services.supabase.private_bucket'
            : 'filesystems.disks.s3.bucket');

        if ($bucket === '') {
            throw new RuntimeException($private
                ? 'SUPABASE_STORAGE_PRIVATE_BUCKET belum dikonfigurasi.'
                : 'AWS_BUCKET belum dikonfigurasi untuk media publik.');
        }

        return $bucket;
    }

    private function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/'))));
    }

    private function usesSupabase(): bool
    {
        return config('filesystems.upload_driver') === 'supabase_s3';
    }
}
