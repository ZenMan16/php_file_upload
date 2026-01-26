<?php

declare(strict_types=1);

use App\Services\Security;

beforeEach(function () {
    // clear/reset the session array before each test
    $_SESSION = [];
});

test('it generates a unique csrf token and stores it in a session', function () {
    $security = new Security();

    $token = $security->generateCsrfToken();

    expect($token)->toBeString()
        ->and(strlen($token))->toBe(64) // 32 bytes in bin2hex = 64 characters
        ->and($_SESSION['csrf_token'])->toBe($token);
});

test('it validates a correct token', function () {
    $security = new Security();

    $token = $security->generateCsrfToken();

    expect($security->validateCsrfToken($token))->toBeTrue();
});

test('it rejects an incorrect or missing token', function () {
    $security = new Security();
    $security->generateCsrfToken();

    expect($security->validateCsrfToken('wrong-token'))->toBeFalse()
        ->and($security->validateCsrfToken(null))->toBeFalse();
});