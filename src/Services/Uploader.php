<?php

namespace App\Services;


class Uploader
{

    protected string $destination;

    // Constructor to set the upload destination
    public function __construct(?string $destination = null)
    {
        $this->destination = $destination
            ?? $_ENV['UPLOAD_PATH']
            ?? '/var/www/html/uploads';

        if (empty($this->destination)) {
            throw new \InvalidArgumentException("Upload destination path is not defined.");
        }
    }

    // Method to get the upload destination
    public function getDestination(): string
    {
        // Return the destination
        return $this->destination;
    }

    // Method to check if the destination folder is ready
    public function isReady(): bool
    {
        // 1. Strict check: Does it exist?
        if (!is_dir($this->destination)) {
            return false;
        }

        // 2. Permission check: Can we write?
        if (!is_writable($this->destination)) {
            return false;
        }

        return true;
    }

    // Method to move the uploaded file to the destination
    public function upload(string $tempPath, string $originalName): bool
    {
        if (!$this->isReady()) {
            return false;
        }

        $targetPath = $this->destination . '/' . $originalName;
        $res = $this->moveFile($tempPath, $targetPath);

        return $res;
    }

    protected function moveFile(string $from, string $to): bool
    {
        $appEnv = getenv('APP_ENV') ?: 'production';

        // In 'dev' environment, simulate move_uploaded_file
        if ($appEnv === 'dev') {
            if (!file_exists($from)) {
                return false;
            }

            // In dev mode, use copy + unlink to simulate move_uploaded_file
            $success = @copy($from, $to);

            // Check if copy was successful
            if (!$success) {
                return false;
            }

            // Remove the original file
            unlink($from);

            return true;
        }

        return move_uploaded_file($from, $to);
    }
}