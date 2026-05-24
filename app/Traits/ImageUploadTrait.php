<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUploadTrait
{
    protected function imageUploadRule(bool $required): string
    {
        $requiredPart = $required ? 'required' : 'nullable';

        // Keep mimes aligned with the existing controller rules.
        return $requiredPart.'|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
    }

    protected function storeImageFromRequest(
        Request $request,
        string $field,
        string $directory,
        ?string $oldPath = null
    ): ?string {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);

        if (!$file instanceof UploadedFile) {
            return null;
        }

        return $this->storeImageFile($file, $directory, $oldPath);
    }

    protected function storeImageFile(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $originalBaseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($originalBaseName) ?: 'image';
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin';

        $filename = $safeBaseName.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(8)).'.'.$extension;

        return $file->storeAs($directory, $filename, 'public');
    }
}
