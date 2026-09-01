<?php

declare(strict_types=1);

if (!defined('MYJAT_ROOT')) {
    define('MYJAT_ROOT', dirname(__DIR__));
}

if (!defined('MYJAT_VIP')) {
    define('MYJAT_VIP', MYJAT_ROOT . '/vip');
}

if (!defined('MYJAT_INCLUDES')) {
    define('MYJAT_INCLUDES', MYJAT_ROOT . '/includes');
}

if (!defined('MYJAT_ASSETS')) {
    define('MYJAT_ASSETS', MYJAT_ROOT . '/assets');
}

if (!defined('MYJAT_BASE_URL')) {
    define('MYJAT_BASE_URL', '/');
}

if (!defined('MYJAT_ASSET_URL')) {
    define('MYJAT_ASSET_URL', MYJAT_BASE_URL . 'assets');
}

if (!defined('MYJAT_ENV')) {
    define('MYJAT_ENV', 'local');
}

if (!function_exists('myjat_path')) {
    function myjat_path(string $path = ''): string
    {
        return MYJAT_ROOT . '/' . ltrim($path, '/');
    }
}

if (!function_exists('myjat_asset_url')) {
    function myjat_asset_url(string $path = ''): string
    {
        return MYJAT_BASE_URL . ltrim($path, '/');
    }
}

if (!function_exists('myjat_include_path')) {
    function myjat_include_path(string $path = ''): string
    {
        return MYJAT_INCLUDES . '/' . ltrim($path, '/');
    }
}