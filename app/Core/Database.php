<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
  private static ?PDO $connection = null;

  public static function connection(): PDO
  {
    if (self::$connection instanceof PDO) {
      return self::$connection;
    }

    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if (DB_AUTO_CREATE) {
      $serverDsn = sprintf('mysql:host=%s;port=%d;charset=%s', DB_HOST, DB_PORT, DB_CHARSET);
      $server = new PDO($serverDsn, DB_USERNAME, DB_PASSWORD, $options);
      $databaseName = str_replace('`', '', DB_DATABASE);
      $server->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s_unicode_ci', $databaseName, DB_CHARSET, DB_CHARSET));
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_DATABASE, DB_CHARSET);
    self::$connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);

    return self::$connection;
  }
}
