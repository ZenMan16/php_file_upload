<?php

namespace App\Services;

class Security
{
    // Ensure session is started
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Generate a CSRF token and store it in session
    public function generateCsrfToken(): string
    {
        if (empty($_SESSION["csrf_token"])) {
            // Generate a random token
            $token = bin2hex(random_bytes(32));

            // Store it in session
            $_SESSION['csrf_token'] = $token;
        }
        return $_SESSION['csrf_token'];
    }


    public function validateCsrfToken(?string $token): bool
    {
        // Check if token is provided and matches the one in session
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}