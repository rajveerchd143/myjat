<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/environment.php';
require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/modules.php';
require_once __DIR__ . '/assets.php';
require_once __DIR__ . '/routes.php';
require_once __DIR__ . '/renderer.php';

if (!defined('MYJAT_VIP_READY')) {
    define('MYJAT_VIP_READY', true);
}