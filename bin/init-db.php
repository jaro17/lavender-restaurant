<?php

declare(strict_types=1);

$settings = require __DIR__ . '/../config/bootstrap.php';

$pdo = new PDO('sqlite:' . $settings['database_path']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

$columns = $pdo->query("PRAGMA table_info(admin_users)")->fetchAll(PDO::FETCH_ASSOC);
$columnNames = array_column($columns, 'name');

if ($columnNames !== [] && in_array('email', $columnNames, true) && !in_array('username', $columnNames, true)) {
    $pdo->exec('ALTER TABLE admin_users RENAME TO admin_users_old');
    $pdo->exec(
        'CREATE TABLE admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );
    $pdo->exec(
        'INSERT INTO admin_users (id, username, password_hash, created_at)
         SELECT id, email, password_hash, created_at FROM admin_users_old'
    );
    $pdo->exec('DROP TABLE admin_users_old');
}

$pdo->exec((string) file_get_contents(__DIR__ . '/../database/schema.sql'));

$legacyStatement = $pdo->prepare('SELECT id FROM admin_users WHERE username = :username LIMIT 1');
$legacyStatement->execute(['username' => 'admin@lavender.test']);
$legacyUserId = $legacyStatement->fetchColumn();

$adminStatement = $pdo->prepare('SELECT id FROM admin_users WHERE username = :username LIMIT 1');
$adminStatement->execute(['username' => $settings['default_admin_username']]);
$defaultAdminId = $adminStatement->fetchColumn();

if ($legacyUserId && !$defaultAdminId) {
    $rename = $pdo->prepare('UPDATE admin_users SET username = :new_username WHERE id = :id');
    $rename->execute([
        'new_username' => $settings['default_admin_username'],
        'id' => $legacyUserId,
    ]);
}

$cleanup = $pdo->prepare('DELETE FROM admin_users WHERE username = :username');
$cleanup->execute(['username' => 'admin@lavender.test']);

$statement = $pdo->prepare('SELECT COUNT(*) FROM admin_users WHERE username = :username');
$statement->execute(['username' => $settings['default_admin_username']]);

if ((int) $statement->fetchColumn() === 0) {
    $insert = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:username, :password_hash)');
    $insert->execute([
        'username' => $settings['default_admin_username'],
        'password_hash' => password_hash($settings['default_admin_password'], PASSWORD_DEFAULT),
    ]);
}

echo "Database initialized at {$settings['database_path']}" . PHP_EOL;
echo "Default admin: {$settings['default_admin_username']} / {$settings['default_admin_password']}" . PHP_EOL;
