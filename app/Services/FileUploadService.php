<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    protected string $disk;
    protected int $maxFileSize;
    protected array $allowedMimeTypes;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 'local');
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
        $this->allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
    }

    /**
     * Upload a file to the specified disk
     */
    public function upload(UploadedFile $file, string $directory = 'uploads'): array
    {
        $this->validateFile($file);

        $filename = $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, $this->disk);

        $url = null;
        if ($this->disk === 'public' || $this->disk === 'uploads') {
            $url = Storage::url($path);
        }

        return [
            'success' => true,
            'path' => $path,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'url' => $url,
        ];
    }

    /**
     * Store a file and return columns ready for the letters table.
     *
     * @return array{file_path: string, file_name: string, file_size: int, file_mime_type: string}
     */
    public function store(UploadedFile $file, string $directory = 'letters'): array
    {
        $result = $this->upload($file, $directory);

        return [
            'file_path' => $result['path'],
            'file_name' => $result['original_name'],
            'file_size' => $result['size'],
            'file_mime_type' => $result['mime_type'],
        ];
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(array $files, string $directory = 'uploads'): array
    {
        $results = [];
        $errors = [];

        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                try {
                    $results[] = $this->upload($file, $directory);
                } catch (\Exception $e) {
                    $errors[] = [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        return [
            'success' => count($errors) === 0,
            'uploaded' => $results,
            'errors' => $errors,
        ];
    }

    /**
     * Delete a file
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Get file URL
     */
    public function getUrl(string $path): ?string
    {
        if ($this->disk === 'public' || $this->disk === 'uploads') {
            return Storage::url($path);
        }
        return null;
    }

    /**
     * Validate file
     */
    protected function validateFile(UploadedFile $file): void
    {
        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('File size exceeds maximum allowed size of 10MB');
        }

        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('File type not allowed');
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);

        return "{$basename}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Set the disk to use
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Set max file size
     */
    public function setMaxFileSize(int $size): self
    {
        $this->maxFileSize = $size;
        return $this;
    }

    /**
     * Set allowed mime types
     */
    public function setAllowedMimeTypes(array $types): self
    {
        $this->allowedMimeTypes = $types;
        return $this;
    }
}
