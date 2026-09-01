<?php

// =========================================================
// MYJAT — SINGLE APPLICATION ENTRY POINT
// =========================================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// =========================================================
// VIP CORE
// =========================================================

require_once __DIR__ . '/vip/bootstrap.php';

// =========================================================
// VIP CORE
// =========================================================

require_once __DIR__ . '/vip/bootstrap.php';

$pdo = $GLOBALS['pdo'] ?? null;

// =========================================================
// POST — MEMBERSHIP FORM
// =========================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {

    if (
        function_exists(
            'myjat_handle_membership_form_submission'
        )
    ) {

        myjat_handle_membership_form_submission(
            $pdo
        );
    }
}


// =========================================================
// MEMBERSHIP ADMIN ACTIONS
// =========================================================

if (
    function_exists(
        'myjat_update_application_status'
    )
) {

    myjat_update_application_status(
        $pdo
    );
}


if (
    function_exists(
        'myjat_save_photo_url'
    )
) {

    myjat_save_photo_url(
        $pdo
    );
}


if (
    function_exists(
        'myjat_mark_card_printed'
    )
) {

    myjat_mark_card_printed(
        $pdo
    );
}


// =========================================================
// CSV EXPORT
// =========================================================

if (
    !empty(
        $_GET['export_csv']
    )
) {

    if (
        !function_exists('myjat_can')
        ||
        !myjat_can(
            'myjat_export_applications'
        )
    ) {

        http_response_code(403);

        exit(
            'Access denied.'
        );
    }


    myjat_export_members_csv(
        $pdo
    );

    exit;
}


// =========================================================
// CURRENT ROUTE
// =========================================================

$path =
    myjat_vip_current_route();


// =========================================================
// HOME
// =========================================================
//
// Root URL "/" produces an empty route.
// routes.php already maps '' → myjat_home.
// Therefore Home uses the SAME renderer as every
// other VIP function page.
//

if ($path === '') {

    $path = '';
}


// =========================================================
// FIND ROUTE
// =========================================================

$route =
    myjat_vip_find_route(
        $path
    );


// =========================================================
// 404
// =========================================================

if (!$route) {

    http_response_code(404);

    echo
        '<!DOCTYPE html>'
        . '<html lang="hi">'
        . '<head>'
        . '<meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
        . '<title>404 - MYJAT</title>'
        . '</head>'
        . '<body>'
        . '<h1>404</h1>'
        . '<p>Page not found.</p>'
        . '</body>'
        . '</html>';

    exit;
}


// =========================================================
// ROUTE TYPE
// =========================================================

if (
    ($route['type'] ?? '')
    !== 'function'
) {

    http_response_code(500);

    exit(
        'Invalid route configuration.'
    );
}


// =========================================================
// ROUTED FUNCTION
// =========================================================

$function =
    $route['function']
    ?? '';


if (
    !$function
    ||
    !function_exists($function)
) {

    http_response_code(500);

    exit(
        'Route function unavailable.'
    );
}


// =========================================================
// GENERATE PAGE CONTENT
// =========================================================

$content =
    myjat_vip_render_function(
        $function,
        $pdo
    );


// =========================================================
// FINAL PAGE RENDER
// =========================================================

myjat_vip_render_page(
    $content
);