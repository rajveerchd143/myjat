<?php

declare(strict_types=1);

if (defined('MYJAT_VIP_MODULES_LOADED')) {
    return;
}

define('MYJAT_VIP_MODULES_LOADED', true);


/*
|--------------------------------------------------------------------------
| MEMBERSHIP CORE
|--------------------------------------------------------------------------
*/

$myjat_vip_core_modules = [
    'membership-config.php',
    'membership-database.php',
    'membership-utils.php',
    'membership-auth.php',
    'membership-number.php',
];


/*
|--------------------------------------------------------------------------
| MEMBERSHIP BACKEND
|--------------------------------------------------------------------------
*/

$myjat_vip_backend_modules = [
    'membership-roles.php',
    'membership-applications.php',
    'membership-handler.php',
    'membership-admin.php',
    'membership-export.php',
    'membership-upload.php',
];


/*
|--------------------------------------------------------------------------
| MEMBERSHIP FRONTEND
|--------------------------------------------------------------------------
*/

$myjat_vip_frontend_modules = [
    'membership-status.php',
    'membership-registration.php',
    'membership-member-dashboard.php',
    'membership-pvc.php',
    'membership-verification.php',
    'membership-view.php',
    'membership-google-login.php',
    'register.php',
];


/*
|--------------------------------------------------------------------------
| LOAD MEMBERSHIP CORE
|--------------------------------------------------------------------------
*/

foreach ($myjat_vip_core_modules as $module) {

    $file = MYJAT_INCLUDES . '/core/' . $module;

    if (is_file($file)) {
        require_once $file;
    }
}


/*
|--------------------------------------------------------------------------
| LOAD MEMBERSHIP BACKEND
|--------------------------------------------------------------------------
*/

foreach ($myjat_vip_backend_modules as $module) {

    $file = MYJAT_INCLUDES . '/backend/' . $module;

    if (is_file($file)) {
        require_once $file;
    }
}


/*
|--------------------------------------------------------------------------
| LOAD MEMBERSHIP FRONTEND
|--------------------------------------------------------------------------
*/

foreach ($myjat_vip_frontend_modules as $module) {

    $file = MYJAT_INCLUDES . '/frontend/' . $module;

    if (is_file($file)) {
        require_once $file;
    }
}


/*
|--------------------------------------------------------------------------
| GREAT PAGES
|--------------------------------------------------------------------------
*/

$myjat_vip_great_modules = [
    'great-pages.php',
    'bhagat.php',
    'bigaji.php',
    'charan.php',
    'chhotu.php',
    'devilal.php',
    'dhanna.php',
    'gokula.php',
    'karmabai.php',
    'mahender.php',
    'nahar.php',
    'ranjit.php',
    'surjmal.php',
    'tejaji.php',
    'tiket.php',
];

foreach ($myjat_vip_great_modules as $module) {

    $file = MYJAT_INCLUDES . '/great/' . $module;

    if (is_file($file)) {
        require_once $file;
    }
}


/*
|--------------------------------------------------------------------------
| WEBSITE PAGES
|--------------------------------------------------------------------------
*/

$myjat_vip_page_modules = [
    'home.php',
    'about.php',
    'contact.php',
    'estab.php',
    'kalamse.php',
    'slider.php',
    'gallery.php',
];

foreach ($myjat_vip_page_modules as $module) {

    $file = MYJAT_INCLUDES . '/pages/' . $module;

    if (is_file($file)) {
        require_once $file;
    }
}

/* =========================================================
   GREAT / SPECIAL PAGES
========================================================= */

$sinhavlocan_file = MYJAT_INCLUDES . '/great/sinhavlocan.php';

if (is_file($sinhavlocan_file)) {
    require_once $sinhavlocan_file;
}

/*
|--------------------------------------------------------------------------
| LOCATION SERVICE
|--------------------------------------------------------------------------
*/

$location_loader =
    MYJAT_INCLUDES .
    '/services/location/location-loader.php';

if (is_file($location_loader)) {
    require_once $location_loader;
}