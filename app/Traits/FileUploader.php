<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploader
{
    /**
     * Upload a file to the specified directory.
     *
     * @param string $folder
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $pathType
     * @return string
     */
    public function uploadFile($folder, UploadedFile $file, $pathType = 'storage')
    {
        $filename = $this->generateUniqueFilename($file);
        $storagePath = $this->getStoragePath($folder, $pathType);

        $file->move($storagePath, $filename);

        return "{$folder}/{$filename}";
    }

    /**
     * Generate a unique filename for the uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    protected function generateUniqueFilename(UploadedFile $file)
    {
        return Str::random(32) . '_' . Str::slug(substr($file->getClientOriginalName(), 0, -3)) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Determine the storage path based on the specified path type.
     *
     * @param string $folder
     * @param string $pathType
     * @return string
     */
    protected function getStoragePath($folder, $pathType)
    {
        return $pathType === 'public' ? public_path("storage/{$folder}") : storage_path("app/public/{$folder}");
    }



    /**
     * Get File Specific File.
     *
     * @param string $column
     * @return void
     */
    public function getFilePath($column)
    {
        $file = $column;
        if (empty($file) || $file == "faker.png") {
            return $this->noImage();
        }
        return asset("storage/$file");
    }

    /**
     * File Column is blank.
     */
    protected function noImage(): string
    {
        return asset("app-assets/no-image.jpg");
    }

    /**
     * Get File Specific File.
     *
     * @param string $column
     * @return void
     */
    public function getProfile($column)
    {
        $file = $this->$column;
        if (empty($file) || $file == "faker.png") {
            return $this->noProfilePhoto();
        }
        return asset("storage/$file");
    }

    /**
     * File Column is blank.
     */
    protected function noProfilePhoto(): string
    {
        return asset("app-assets/no-image.jpg");
    }

    /**
     * Remove file from storage.
     */
    public function delete_file($file)
    {
        if (Storage::disk('public')->exists($file)) {
            Storage::disk('public')->delete($file);
        } else {
            return 'asas';
        }
    }
}
