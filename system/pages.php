<?php

// =========================================================
// MYJAT — SYSTEM PAGE ENTRY
// Uses the same VIP renderer as all other pages
// =========================================================

require_once __DIR__ . '/../vip/bootstrap.php';


// =========================================================
// PAGE MAP
// =========================================================

$pages = [

    'estab' => 'myjat_estab_home',

    'kalamse' => 'myjat_kalamse_home',

    'sinhavlocan' => 'myjat_sinhavlocan_home',


    'about' => 'myjat_about_home',

    'contact' => 'myjat_contact_home',

    'membership' => 'myjat_membership_form',

];


// =========================================================
// GET SLUG
// =========================================================

$slug = $_GET['slug'] ?? '';


// =========================================================
// FIND FUNCTION
// =========================================================

$function = $pages[$slug] ?? null;


// =========================================================
// 404
// =========================================================

if (
    !$function
    ||
    !function_exists($function)
) {

    http_response_code(404);

    exit(
        'Page not found.'
    );
}


// =========================================================
// DATABASE
// =========================================================

$pdo = $GLOBALS['pdo'] ?? null;


// =========================================================
// GENERATE CONTENT
// =========================================================

$content =
    myjat_vip_render_function(
        $function,
        $pdo
    );


// =========================================================
// SAME GLOBAL RENDERER
// =========================================================

myjat_vip_render_page(
    $content
);