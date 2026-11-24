<?php

namespace App\Http\Controllers;

use App\Services\ReportImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanUploadController extends Controller
{
    public function store(Request $request, ReportImageUploadService $uploads): JsonResponse
    {
        abort_unless($uploads->supportsSignedUploads(), 404);

        $validated = $request->validate([
            'content_type' => ['required', 'in:image/jpeg,image/png,image/webp'],
            'size' => ['required', 'integer', 'min:1', 'max:'.ReportImageUploadService::MAX_FILE_BYTES],
        ]);

        return response()->json(
            $uploads->createSignedUpload($request->user(), $validated['content_type'], (int) $validated['size']),
            201,
        )->header('Cache-Control', 'private, no-store');
    }

    public function destroy(string $upload, Request $request, ReportImageUploadService $uploads)
    {
        $uploads->deleteUpload($request->user(), $upload);

        return response()->noContent();
    }
}
