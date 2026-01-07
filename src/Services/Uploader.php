<?php

namespace App\Services;

// var_dump("UPLOADER CLASS LOADED"); // <--- Add this

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
            // We log/dump exactly what is missing so we can fix the infrastructure
            var_dump("INFRASTRUCTURE ERROR: Directory {$this->destination} does not exist.");
            return false;
        }

        // 2. Permission check: Can we write?
        if (!is_writable($this->destination)) {
            var_dump("PERMISSION ERROR: Directory {$this->destination} is not writable.");
            return false;
        }

        return true;
    }

    // Method to move the uploaded file to the destination
    public function upload(string $tempPath, string $originalName): bool
    {
        // var_dump("--- DEBUG START ---");
        // var_dump("Temp Path: " . $tempPath);
        // var_dump("Dest: " . $this->destination);

        if (!$this->isReady()) {
            // var_dump("RESULT: isReady failed");
            return false;
        }

        $targetPath = $this->destination . '/' . $originalName;
        $res = $this->moveFile($tempPath, $targetPath);

        // var_dump("RESULT: moveFile returned " . ($res ? 'TRUE' : 'FALSE'));
        return $res;
    }

    protected function moveFile(string $from, string $to): bool
    {
        // var_dump("CHECKPOINT 1: Checking " . $from);
        $appEnv = getenv('APP_ENV') ?: 'production';

        if ($appEnv === 'dev') {
            // var_dump("ENTERED TEST MODE LOGIC");

            if (!file_exists($from)) {
                // var_dump("CHECKPOINT 2: SOURCE MISSING");
                return false;
            }

            // var_dump("CHECKPOINT 3: Copying to " . $to);
            $success = @copy($from, $to);

            if (!$success) {
                // $err = error_get_last();
                // var_dump("CHECKPOINT 4: COPY FAILED! " . ($err['message'] ?? 'No PHP Error'));
                return false;
            }

            unlink($from);
            return true;
        }

        return move_uploaded_file($from, $to);
    }
}