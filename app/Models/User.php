<?php

namespace App\Models;

use App\Core\Model;

/**
 * User Model
 */
class User extends Model
{
    protected string $table = 'users';

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?object
    {
        return $this->whereFirst('username', $username);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?object
    {
        return $this->whereFirst('email', $email);
    }

    /**
     * Create new user
     */
    public function create(array $data): int
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_ARGON2ID);
            unset($data['password']);
        }

        return $this->insert($data);
    }

    /**
     * Verify password
     */
    public function verifyPassword(object $user, string $password): bool
    {
        return password_verify($password, $user->password_hash);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->execute(
            "UPDATE users SET last_login = NOW() WHERE id = ?",
            [$userId]
        );
    }

    /**
     * Check if user has role
     */
    public function hasRole(object $user, string $role): bool
    {
        return $user->role === $role || $user->role === 'admin';
    }

    /**
     * Get all admins
     */
    public function getAdmins(): array
    {
        return $this->where('role', 'admin');
    }

    /**
     * Get all editors
     */
    public function getEditors(): array
    {
        return $this->where('role', 'editor');
    }
}
