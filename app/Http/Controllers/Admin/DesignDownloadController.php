<?php

namespace App\Http\Controllers\Admin;

use App\Models\Design;
use App\Models\DesignAsset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DesignDownloadController extends Controller
{
    public function preview(Design $design): StreamedResponse
    {
        abort_unless($design->preview_image_path, 404);
        abort_unless(Storage::disk('public')->exists($design->preview_image_path), 404);

        return Storage::disk('public')->download(
            $design->preview_image_path,
            "design-{$design->id}-preview.png",
        );
    }

    public function print(Design $design): StreamedResponse
    {
        abort_unless($design->print_image_path, 404);
        abort_unless(Storage::disk('public')->exists($design->print_image_path), 404);

        return Storage::disk('public')->download(
            $design->print_image_path,
            "design-{$design->id}-print-only.png",
        );
    }

    public function asset(DesignAsset $asset): StreamedResponse
    {
        abort_unless($asset->original_file_path, 404);
        abort_unless(Storage::disk('public')->exists($asset->original_file_path), 404);

        return Storage::disk('public')->download(
            $asset->original_file_path,
            $asset->file_name ?: basename($asset->original_file_path),
        );
    }
}
