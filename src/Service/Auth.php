<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

final class Auth
{
    public function __construct(private PDO $pdo)
    {
    }

    public function attempt(string $username, string $password): bool
    {
        $statement = $this->pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => trim($username)]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        $_SESSION['admin_user'] = [
            'id' => (int) $user['id'],
            'username' => $user['username'],
        ];

        session_regenerate_id(true);

        return true;
    }

    public function check(): bool
    {
        return isset($_SESSION['admin_user']['id'], $_SESSION['admin_user']['username']);
    }

    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }

        return $_SESSION['admin_user'];
    }

    public function logout(): void
    {
        unset($_SESSION['admin_user']);
        session_regenerate_id(true);
    }
}
