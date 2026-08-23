<?php

$envFile = dirname(__DIR__) . '/.env';

if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Comments ignore
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Only KEY=VALUE lines
        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value);

        // Remove optional surrounding quotes
        if (
            strlen($value) >= 2 &&
            (
                ($value[0] === '"' && $value[-1] === '"') ||
                ($value[0] === "'" && $value[-1] === "'")
            )
        ) {
            $value = substr($value, 1, -1);
        }

        if ($key !== '') {
            putenv($key . '=' . $value);
        }
    }
}