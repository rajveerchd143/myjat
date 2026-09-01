<?php

declare(strict_types=1);

if (defined('MYJAT_VIP_DATABASE_LOADED')) {
    return;
}

define('MYJAT_VIP_DATABASE_LOADED', true);

$db_host = $_ENV['DB_HOST']
    ?? getenv('DB_HOST')
    ?? '127.0.0.1';

$db_name = $_ENV['DB_NAME']
    ?? getenv('DB_NAME')
    ?? '';

$db_user = $_ENV['DB_USER']
    ?? getenv('DB_USER')
    ?? '';

$db_pass = $_ENV['DB_PASS']
    ?? getenv('DB_PASS')
    ?? '';

$db_charset = 'utf8mb4';

if ($db_name === '') {
    throw new RuntimeException(
        'MYJAT database name is not configured.'
    );
}

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $db_host,
    $db_name,
    $db_charset
);

try {
    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    throw new RuntimeException(
        'MYJAT database connection failed.',
        0,
        $e
    );
}