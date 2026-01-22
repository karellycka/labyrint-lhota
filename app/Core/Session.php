<?php

namespace App\Core;

/**
 * Session třída - správa sessions a CSRF tokenů
 */
class Session
{
    private const TIMEOUT = 31536000; // 365 dní (pro localhost development)

    /**
     * Start session with security settings
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => self::TIMEOUT,
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'cookie_samesite' => 'Strict',
                'use_strict_mode' => true,
                'use_only_cookies' => true,
                'sid_length' => 48,
                'sid_bits_per_character' => 6
            ]);

            // Set session data on first start
            if (!isset($_SESSION['initialized'])) {
                $_SESSION['initialized'] = true;
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
                $_SESSION['last_activity'] = time();
            }
        }

        // Check timeout and validation
        self::checkTimeout();
        self::validate();
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check session timeout
     */
    public static function checkTimeout(): bool
    {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::TIMEOUT) {
                self::destroy();
                return false;
            }
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Validate session (prevent hijacking)
     */
    public static function validate(): bool
    {
        if (!isset($_SESSION['user_agent']) || !isset($_SESSION['ip_address'])) {
            return true; // First request, OK
        }

        // Check user agent
        if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            self::destroy();
            return false;
        }

        // Optional: Strict IP check (can cause issues with mobile networks)
        // if ($_SESSION['ip_address'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
        //     self::destroy();
        //     return false;
        // }

        return true;
    }

    /**
     * Set session value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session key
     */
    public static function remove(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Set flash message
     */
    public static function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get flash message and remove it
     */
    public static function getFlash(string $key): ?string
    {
        $message = $_SESSION['flash'][$key] ?? null;

        if ($message) {
            unset($_SESSION['flash'][$key]);
        }

        return $message;
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Regenerate session ID (use after login)
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get logged in user ID
     */
    public static function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get user role
     */
    public static function getUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Set user as logged in
     */
    public static function login(int $userId, string $role): void
    {
        self::regenerate();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        self::destroy();
    }
}
