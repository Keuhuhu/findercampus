<?php

namespace App\Services;

use App\Models\FotoBarang;
use App\Models\Laporan;
use App\Models\ReportUpload;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ReportImageUploadService
{
    public const MAX_FILES = 5;

    public const MAX_FILE_BYTES = 5 * 1024 * 1024;

    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(private readonly AssetStorage $assetStorage) {}

    public function supportsSignedUploads(): bool
    {
        return $this->assetStorage->supportsSignedUploads();
    }

    /** @return array{id: string, url: string, headers: array<string, string>, expires_at: string} */
    public function createSignedUpload(User $user, string $mimeType, int $size): array
    {
        if (! $this->assetStorage->supportsSignedUploads()) {
            throw new RuntimeException('Unggahan langsung belum diaktifkan.');
        }

        if (! in_array($mimeType, self::ALLOWED_MIMES, true) || $size < 1 || $size > self::MAX_FILE_BYTES) {
            throw ValidationException::withMessages([
                'foto' => 'Pilih gambar JPG, PNG, atau WEBP dengan ukuran maksimal 5 MB.',
            ]);
        }

        $this->removeExpiredUploads();

        $id = (string) Str::uuid();
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        };
        $path = "report-upload-staging/{$user->id}/{$id}.{$extension}";
        $url = $this->assetStorage->createPrivateUploadUrl($path, $mimeType);
        $expiresAt = now()->addHours(2);

        ReportUpload::create([
            'id' => $id,
            'user_id' => $user->id,
            'file_path' => $path,
            'content_type' => $mimeType,
            'expires_at' => $expiresAt,
        ]);

        return [
            'id' => $id,
            'url' => $url,
            'headers' => ['Content-Type' => $mimeType],
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    /** @param array<int, string> $uploadIds
     *  @return array{public_paths: array<int, string>, upload_ids: array<int, string>}
     */
    public function attachToReport(Laporan $laporan, User $user, array $uploadIds): array
    {
        $uploadIds = array_values(array_unique($uploadIds));

        if ($uploadIds === []) {
            return ['public_paths' => [], 'upload_ids' => []];
        }

        if (count($uploadIds) > self::MAX_FILES) {
            throw ValidationException::withMessages(['foto' => 'Maksimal 5 foto yang diizinkan.']);
        }

        $uploads = ReportUpload::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $uploadIds)
            ->where('expires_at', '>', now())
            ->get();

        if ($uploads->count() !== count($uploadIds)) {
            throw ValidationException::withMessages([
                'foto' => 'Sesi unggah foto sudah tidak berlaku. Unggah kembali foto yang dipilih.',
            ]);
        }

        $publicPaths = [];

        try {
            foreach ($uploads as $upload) {
                if (! str_starts_with($upload->file_path, "report-upload-staging/{$user->id}/")) {
                    throw ValidationException::withMessages(['foto' => 'Berkas unggahan tidak valid.']);
                }

                $storedImage = $this->assetStorage->readPrivate($upload->file_path);
                $contents = $storedImage['contents'];
                $actualMime = $this->detectImageMime($contents);

                if (
                    strlen($contents) > self::MAX_FILE_BYTES
                    || ! in_array($actualMime, self::ALLOWED_MIMES, true)
                    || $actualMime !== $upload->content_type
                ) {
                    throw ValidationException::withMessages([
                        'foto' => 'Foto harus berupa JPG, PNG, atau WEBP dengan ukuran maksimal 5 MB.',
                    ]);
                }

                $extension = match ($actualMime) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                };
                $publicPath = 'images/barangs/'.Str::uuid().'.'.$extension;

                if (! $this->assetStorage->putPublic($publicPath, $contents, $actualMime)) {
                    throw new RuntimeException('Foto laporan gagal disimpan.');
                }
                $publicPaths[] = $publicPath;

                $isFirstPhoto = ! $laporan->fotos()->exists();
                FotoBarang::create([
                    'laporan_id' => $laporan->id,
                    'file_path' => $publicPath,
                    'is_primary' => $isFirstPhoto,
                ]);
            }
        } catch (Throwable $exception) {
            $this->assetStorage->deletePublic($publicPaths);

            throw $exception;
        }

        return ['public_paths' => $publicPaths, 'upload_ids' => $uploads->modelKeys()];
    }

    /** @param array<int, string> $uploadIds */
    public function deleteConsumedUploads(User $user, array $uploadIds): void
    {
        if ($uploadIds === []) {
            return;
        }

        $uploads = ReportUpload::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $uploadIds)
            ->get();

        $this->assetStorage->deletePrivate($uploads->pluck('file_path')->all());
        ReportUpload::query()->whereIn('id', $uploads->modelKeys())->delete();
    }

    public function deleteUpload(User $user, string $uploadId): void
    {
        $upload = ReportUpload::query()
            ->where('user_id', $user->id)
            ->findOrFail($uploadId);

        $this->assetStorage->deletePrivate($upload->file_path);
        $upload->delete();
    }

    private function removeExpiredUploads(): void
    {
        ReportUpload::query()
            ->where('expires_at', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($uploads): void {
                $this->assetStorage->deletePrivate($uploads->pluck('file_path')->all());
                ReportUpload::query()->whereIn('id', $uploads->modelKeys())->delete();
            });
    }

    private function detectImageMime(string $contents): ?string
    {
        $imageInfo = @getimagesizefromstring($contents);

        return is_array($imageInfo) ? ($imageInfo['mime'] ?? null) : null;
    }
}
