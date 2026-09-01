<?php

declare(strict_types=1);

if (defined('MYJAT_VIP_ENVIRONMENT_LOADED')) {
    return;
}

define('MYJAT_VIP_ENVIRONMENT_LOADED', true);

$env_file = MYJAT_ROOT . '/.env';

if (!is_file($env_file)) {
    return;
}

$lines = file(
    $env_file,
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);

foreach ($lines as $line) {

    $line = trim($line);

    if (
        $line === ''
        || str_starts_with($line, '#')
        || !str_contains($line, '=')
    ) {
        continue;
    }

    [$key, $value] = explode('=', $line, 2);

    $key = trim($key);
    $value = trim($value);

    if ($key === '') {
        continue;
    }

    if (
        strlen($value) >= 2
        && (
            (
                $value[0] === '"'
                && $value[-1] === '"'
            )
            ||
            (
                $value[0] === "'"
                && $value[-1] === "'"
            )
        )
    ) {
        $value = substr($value, 1, -1);
    }

    if (!isset($_ENV[$key])) {
        $_ENV[$key] = $value;
    }

    if (!isset($_SERVER[$key])) {
        $_SERVER[$key] = $value;
    }

    if (getenv($key) === false) {
        putenv($key . '=' . $value);
    }
}