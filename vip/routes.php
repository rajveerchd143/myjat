<?php

declare(strict_types=1);




$MYJAT_ROUTES = [

    '' => [
        'type' => 'function',
        'function' => 'myjat_home',
    ],

    'about' => [
        'type' => 'function',
        'function' => 'myjat_about_home',
    ],

    'contact' => [
        'type' => 'function',
        'function' => 'myjat_contact_home',
    ],

    'estab' => [
        'type' => 'function',
        'function' => 'myjat_estab_home',
    ],

    'kalamse' => [
        'type' => 'function',
        'function' => 'myjat_kalamse_home',
    ],

    'sinhavlocan' => [
        'type' => 'function',
        'function' => 'myjat_sinhavlocan_home',
    ],

    'register' => [
        'type' => 'function',
        'function' => 'myjat_membership_form',
    ],

    'login' => [
        'type'     => 'function',
        'function' => 'myjat_login_page',
    ],

    'logout' => [
        'type'     => 'function',
        'function' => 'myjat_logout',
    ],
    'google-callback.php' => [
        'type' => 'function',
        'function' => 'myjat_google_callback',
    ],

    'member-dashboard' => [
        'type' => 'function',
        'function' => 'myjat_member_dashboard',
    ],

    'application-status' => [
        'type' => 'function',
        'function' => 'myjat_application_status',
    ],




    // =====================================================
    // MEMBERSHIP PAYMENT VERIFICATION
    // =====================================================

    'membership-payment-verify' => [
        'type' => 'function',
        'function' => 'myjat_membership_payment_verify',
    ],


    // =====================================================
    // RAZORPAY PAYMENT WEBHOOK
    // =====================================================

    'membership-payment-webhook' => [
        'type' => 'function',
        'function' => 'myjat_membership_payment_webhook',
    ],


    

    // =====================================================
    // GREAT PAGES
    // =====================================================

    'bhagat' => [
        'type'     => 'function',
        'function' => 'myjat_bhagat_home',
    ],

    'bigaji' => [
        'type'     => 'function',
        'function' => 'myjat_bigaji_home',
    ],

    'charan' => [
        'type'     => 'function',
        'function' => 'myjat_charan_home',
    ],

    'chhotu' => [
        'type'     => 'function',
        'function' => 'myjat_chhotu_home',
    ],

    'devilal' => [
        'type'     => 'function',
        'function' => 'myjat_devilal_home',
    ],

    'dhanna' => [
        'type'     => 'function',
        'function' => 'myjat_dhanna_home',
    ],

    'gokula' => [
        'type'     => 'function',
        'function' => 'myjat_gokula_home',
    ],

    'karmabai' => [
        'type'     => 'function',
        'function' => 'myjat_karmabai_home',
    ],

    'mahender' => [
        'type'     => 'function',
        'function' => 'myjat_mahender_home',
    ],

    'nahar' => [
        'type'     => 'function',
        'function' => 'myjat_nahar_home',
    ],

    'ranjit' => [
        'type'     => 'function',
        'function' => 'myjat_ranjit_home',
    ],


    'surjmal' => [
        'type'     => 'function',
        'function' => 'myjat_surjmal_home',
    ],

    'tejaji' => [
        'type'     => 'function',
        'function' => 'myjat_tejaji_home',
    ],

    'tiket' => [
        'type'     => 'function',
        'function' => 'myjat_tiket_home',
    ],

    'member-card' => [
        'type' => 'function',
        'function' => 'myjat_member_card_page',
    ],

    'admin-member-card' => [
        'type' => 'function',
        'function' => 'myjat_admin_member_card_page',
    ],

    'member-verification' => [
        'type' => 'function',
        'function' => 'myjat_member_verify',
    ],

    'membership-admin' => [
        'type' => 'function',
        'function' => 'myjat_membership_admin_page',
    ],

    'application-view' => [
        'type' => 'function',
        'function' => 'myjat_render_application_view',
    ],

];


function myjat_vip_current_route(): string
{
    $path = parse_url(
        $_SERVER['REQUEST_URI'] ?? '/',
        PHP_URL_PATH
    );

    return trim(
        (string) $path,
        '/'
    );
}


function myjat_vip_find_route(?string $path = null): ?array
{
    global $MYJAT_ROUTES;

    if ($path === null) {
        $path = myjat_vip_current_route();
    }

    return $MYJAT_ROUTES[$path] ?? null;
}


function myjat_vip_route_exists(?string $path = null): bool
{
    return myjat_vip_find_route($path) !== null;
}
