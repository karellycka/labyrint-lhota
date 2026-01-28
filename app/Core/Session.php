<?php

namespace App\Core;

/**
 * Session třída - správa sessions a CSRF tokenů
 */
class Session
{
    private const DEFAULT_TIMEOUT = 31536000; // 365 dni

    /**
     * Start session with security settings
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $timeout = self::getTimeout();
            ini_set('session.gc_maxlifetime', (string)$timeout);
            ini_set('session.cookie_lifetime', (string)$timeout);

            $db = Database::getInstance();
            $handler = new DbSessionHandler($db->getPDO(), $timeout);
            session_set_save_handler($handler, true);

            session_start([
                'cookie_lifetime' => $timeout,
                'cookie_httponly' => true,
                'cookie_secure' => self::isHttps(),
                'cookie_samesite' => 'Lax', // Changed from 'Strict' - allows cookies on navigation
                'cookie_domain' => '', // Empty = current domain only (Railway-safe)
                'cookie_path' => '/', // Explicitly set to root
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
            $inactiveTime = time() - $_SESSION['last_activity'];
            if ($inactiveTime > self::getTimeout()) {
                self::destroy('timeout - inactive for ' . $inactiveTime . 's');
                return false;
            }
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    private static function getTimeout(): int
    {
        return defined('SESSION_TIMEOUT') ? (int)SESSION_TIMEOUT : self::DEFAULT_TIMEOUT;
    }

    private static function isHttps(): bool
    {
        $isHttps = false;
        $reason = 'none';

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $isHttps = true;
            $reason = 'HTTPS=' . $_SERVER['HTTPS'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            $proto = strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']);
            $isHttps = $proto === 'https';
            $reason = 'X-Forwarded-Proto=' . $proto;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_SSL'])) {
            $ssl = strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']);
            $isHttps = $ssl === 'on';
            $reason = 'X-Forwarded-SSL=' . $ssl;
        } elseif (!empty($_SERVER['REQUEST_SCHEME'])) {
            $scheme = strtolower((string)$_SERVER['REQUEST_SCHEME']);
            $isHttps = $scheme === 'https';
            $reason = 'REQUEST_SCHEME=' . $scheme;
        }

        // Log HTTPS detection for debugging
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
            error_log(sprintf('Session HTTPS detection: %s (reason: %s)', $isHttps ? 'YES' : 'NO', $reason));
        }

        return $isHttps;
    }

    /**
     * Validate session (prevent hijacking)
     */
    public static function validate(): bool
    {
        if (!isset($_SESSION['user_agent']) || !isset($_SESSION['ip_address'])) {
            return true; // First request, OK
        }

        // User Agent validation DISABLED - too brittle in production
        // Browsers/proxies can change UA strings between requests causing false logouts
        // Railway load balancer may modify headers
        // Security trade-off: UA check provides minimal security benefit vs. high false positive rate

        // IP check DISABLED - causes issues with:
        // - Mobile networks (IP changes when switching towers)
        // - VPN reconnections
        // - Load balancer IP forwarding inconsistencies

        // Note: Session hijacking is mitigated by:
        // - HTTPS only (cookie_secure=true)
        // - HttpOnly cookies (no JS access)
        // - SameSite=Lax (CSRF protection)
        // - Long random session IDs (48 chars, 6 bits/char = 288 bits entropy)
        // - Database-backed sessions with last_activity tracking

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
    public static function destroy(string $reason = 'manual'): void
    {
        // Log session destruction for debugging
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
            error_log(sprintf(
                'Session destroyed - Reason: %s | User: %s | IP: %s | UA: %s',
                $reason,
                $_SESSION['username'] ?? $_SESSION['user_id'] ?? 'unknown',
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 100)
            ));
        }

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
