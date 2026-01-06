<?php

use App\Services\Uploader;

// define a single source of truth for the path
const UPLOAD_PATH = '/var/www/html/uploads';

test('it can set and get the upload destination', function () {
    // 2. Act by creating an Uploader instance and setting the destination
    $uploader = new Uploader(UPLOAD_PATH);

    // 3. Assert that the destination is set correctly
    expect($uploader->getDestination())->toBe(UPLOAD_PATH);
});

test('it identifies if the destination folder is ready', function () {
    // create an Uploader instance with a known existing writable folder
    $uploader = new Uploader(UPLOAD_PATH);

    // Assert that the destination is set correctly
    expect($uploader->isReady())->toBeTrue();
});

test('it returns false if the destination folder does not exist', function () {
    // create an Uploader instance with a non-existing folder
    $wrongPath = UPLOAD_PATH . '/nonexistent_folder';
    $uploader = new Uploader($wrongPath);

    expect($uploader->isReady())->toBeFalse();
});

test('it moves an uploaded file using environment variables', function () {
    // 1. Arrange: get the path from getenv()
    if (!is_dir(UPLOAD_PATH)) {
        if (!mkdir(UPLOAD_PATH, 0777, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" could not be created', UPLOAD_PATH));
        }
    }

    // Ensure permissions are exactly what we need
    if (!chmod(UPLOAD_PATH, 0777)) {
        throw new \RuntimeException(sprintf('Permissions could not be set for "%s"', UPLOAD_PATH));
    }


    $uploader = new Uploader();

    $tempFile = tempnam(sys_get_temp_dir(), 'test_');
    file_put_contents($tempFile, 'test content');
    $fileName = 'env_test_image.jpg';

    // 2. Act
    expect($uploader->isReady())->toBeTrue();
    $result = $uploader->upload($tempFile, $fileName);

    // 3. Assert
    expect($result)->toBeTrue();

    // Check that the file exists in the expected location
    $expectedPath = UPLOAD_PATH . '/' . $fileName;
    expect(file_exists($expectedPath))->toBeTrue();

    // Cleanup
    if (file_exists($expectedPath)) {
        unlink($expectedPath);
    }
});

// To run the tests, use the following command:
// docker exec -it php_file_upload ./vendor/bin/pest