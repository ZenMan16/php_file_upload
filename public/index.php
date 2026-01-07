<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\Uploader;
use App\Services\Security;

// Configuration
$uploadPath = getenv('UPLOAD_PATH') ?: __DIR__ . '/../uploads';
$uploader = new Uploader($uploadPath);
$security = new Security();

$message = '';
$messageClass = '';

// Handling the "POST" Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // validate CSRF token
        $submittedToken = $_POST['csrf_token'] ?? null;
        if (!$security->validateCsrfToken($submittedToken)) {
            throw new Exception("Invalid CSRF token. Request denied.");
        }

        // handle the uploaded
        if (isset($_FILES['my_file'])) {
            $file = $_FILES['my_file'];

            // Validation: Check for PHP upload errors (like file too large)
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("PHP Upload Error Code: " . $file['error']);
            }

            if ($uploader->upload($file['tmp_name'], $file['name'])) {
                $message = "Success! File uploaded.";
                $messageClass = "success";
            }
        }
    } catch (\Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageClass = "error";
    }
}

// Generate a new CSRF token for the form
$token = $security->generateCsrfToken();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP File Uploader</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="card">
        <h2>Upload a File</h2>
        <?php if ($message): ?>
            <div class="message <?= $messageClass ?>"><?= $message ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            <input type="file" name="my_file" required>
            <button type="submit">Upload Now</button>
        </form>
    </div>
</body>

</html>