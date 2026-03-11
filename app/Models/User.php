<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class User
{
  public static function migrate(): void
  {
    $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(30) NOT NULL DEFAULT 'editor',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL;

    Database::connection()->exec($sql);
  }

  public static function count(): int
  {
    self::migrate();
    $statement = Database::connection()->query('SELECT COUNT(*) FROM users');

    return (int) $statement->fetchColumn();
  }

  public static function findByEmail(string $email): ?array
  {
    self::migrate();
    $statement = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    return is_array($user) ? $user : null;
  }

  public static function findById(int $id): ?array
  {
    self::migrate();
    $statement = Database::connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    return is_array($user) ? $user : null;
  }

  public static function create(array $attributes): int
  {
    self::migrate();
    $statement = Database::connection()->prepare(
      'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
    );
    $statement->execute([
      'name' => $attributes['name'],
      'email' => $attributes['email'],
      'password' => $attributes['password'],
      'role' => $attributes['role'] ?? 'editor',
    ]);

    return (int) Database::connection()->lastInsertId();
  }
}
