<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

/**
 * Admin Auth Controller - Login/Logout
 */
class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // If already logged in, redirect to dashboard
        if (Session::isLoggedIn()) {
            redirect(adminUrl());
        }

        $this->view('admin/login', [], 'admin');
    }

    /**
     * Process login
     */
    public function login(): void
    {

        // Validate CSRF token
        if (!Session::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::flash('error', 'Invalid CSRF token');
            redirect(adminUrl('login'));
        }

        // Rate limiting check
        if ($this->isRateLimited($_POST['username'] ?? '')) {
            Session::flash('error', 'Too many login attempts. Please try again later.');
            redirect(adminUrl('login'));
        }

        // Validate input
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Session::flash('error', 'Please enter both username and password');
            redirect(adminUrl('login'));
        }

        // Find user
        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if (!$user || !$userModel->verifyPassword($user, $password)) {
            $this->recordFailedLogin($username);
            sleep(2); // Prevent brute force
            Session::flash('error', 'Invalid username or password');
            redirect(adminUrl('login'));
        }

        // Success - create session
        Session::login((int) $user->id, $user->role);
        Session::set('username', $user->username);
        Session::set('email', $user->email);

        // Update last login
        $userModel->updateLastLogin((int) $user->id);

        // Clear rate limiting
        $this->clearFailedLogins($username);

        redirect(adminUrl());
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        Session::logout();
        redirect(adminUrl('login'));
    }

    /**
     * Check if user is rate limited
     */
    private function isRateLimited(string $username): bool
    {
        $cacheKey = 'login_attempts_' . md5($username);
        $cacheFile = STORAGE_PATH . '/cache/' . $cacheKey . '.txt';

        if (!file_exists($cacheFile)) {
            return false;
        }

        $data = file_get_contents($cacheFile);
        [$attempts, $timestamp] = explode('|', $data);

        // Clear if older than 15 minutes
        if (time() - $timestamp > 900) {
            unlink($cacheFile);
            return false;
        }

        return (int) $attempts >= RATE_LIMIT_LOGIN;
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedLogin(string $username): void
    {
        $cacheKey = 'login_attempts_' . md5($username);
        $cacheFile = STORAGE_PATH . '/cache/' . $cacheKey . '.txt';

        $attempts = 1;
        if (file_exists($cacheFile)) {
            $data = file_get_contents($cacheFile);
            [$attempts, $timestamp] = explode('|', $data);

            // Reset if older than 15 minutes
            if (time() - $timestamp > 900) {
                $attempts = 1;
            } else {
                $attempts = (int) $attempts + 1;
            }
        }

        file_put_contents($cacheFile, $attempts . '|' . time());
    }

    /**
     * Clear failed login attempts
     */
    private function clearFailedLogins(string $username): void
    {
        $cacheKey = 'login_attempts_' . md5($username);
        $cacheFile = STORAGE_PATH . '/cache/' . $cacheKey . '.txt';

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
}
