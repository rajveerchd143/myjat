<?php

declare(strict_types=1);

/**
 * MYJAT VIP — Asset Center
 *
 * Central registry for CSS and JavaScript assets.
 */

if (defined('MYJAT_VIP_ASSETS_LOADED')) {
    return;
}

define('MYJAT_VIP_ASSETS_LOADED', true);


/*
|--------------------------------------------------------------------------
| CSS
|--------------------------------------------------------------------------
*/

$MYJAT_CSS = [

    // Core
    '/assets/css/core/style.css',

    // Components
    '/assets/css/components/00-pvc.css',
    '/assets/css/components/02-typography.css',
    '/assets/css/components/04-buttons.css',
    '/assets/css/components/05-forms.css',
    '/assets/css/components/06-cards.css',
    '/assets/css/components/11-search.css',
    '/assets/css/components/header.css',
    '/assets/css/components/footer.css',
    '/assets/css/components/website.css',

    // Pages
    '/assets/css/pages/home.css',
    '/assets/css/pages/membership-registration.css',
    '/assets/css/pages/membership-google-login.css',
    '/assets/css/pages/membership-verification.css',
    '/assets/css/pages/menu.css',
    '/assets/css/pages/slider.css',

    // Admin
    '/assets/css/admin/applications.css',
    '/assets/css/admin/dashboard.css',
    '/assets/css/admin/view.css',

];


/*
|--------------------------------------------------------------------------
| JavaScript
|--------------------------------------------------------------------------
*/

$MYJAT_JS = [

    // Core
    '/assets/script/core.js',

    // Website
    '/assets/script/website.js',

    // Header / Footer
    '/assets/script/header-footer.js',

    // Homepage
    '/assets/script/home.js',
    '/assets/script/home-slider.js',

    // Membership
    '/assets/script/membership-registration.js',

    // Location
    '/assets/script/location.js',

];


/*
|--------------------------------------------------------------------------
| Location AJAX
|--------------------------------------------------------------------------
*/

$MYJAT_LOCATION_AJAX_URL =
    '/includes/services/location/ajax-location.php';


/*
|--------------------------------------------------------------------------
| CSS Renderer
|--------------------------------------------------------------------------
*/

function myjat_vip_render_css(): void
{
    global $MYJAT_CSS;

    foreach ($MYJAT_CSS as $css) {

        echo '<link rel="stylesheet" href="'
            . htmlspecialchars(
                $css,
                ENT_QUOTES,
                'UTF-8'
            )
            . '">' . PHP_EOL;
    }
}


/*
|--------------------------------------------------------------------------
| JavaScript Renderer
|--------------------------------------------------------------------------
*/

function myjat_vip_render_js(): void
{
    global $MYJAT_JS;
    global $MYJAT_LOCATION_AJAX_URL;


    /*
    |--------------------------------------------------------------------------
    | Location Configuration
    |--------------------------------------------------------------------------
    */

    echo '<script>';

    echo 'window.myjatLocation = {';
    echo 'ajax_url: '
        . json_encode(
            $MYJAT_LOCATION_AJAX_URL,
            JSON_UNESCAPED_SLASHES
        );
    echo '};';

    echo '</script>' . PHP_EOL;


    /*
    |--------------------------------------------------------------------------
    | JavaScript Files
    |--------------------------------------------------------------------------
    */

    foreach ($MYJAT_JS as $js) {

        $isModule =
            basename($js) === 'core.js';

        echo '<script';

        if ($isModule) {
            echo ' type="module"';
        }

        echo ' src="'
            . htmlspecialchars(
                $js,
                ENT_QUOTES,
                'UTF-8'
            )
            . '"></script>' . PHP_EOL;
    }
}