<?php

// =========================================================
// Roles
// Standalone PHP Version
// =========================================================


// =========================================================
// Create MYJAT Secretary Role
// =========================================================

function myjat_create_secretary_role()
{
    if (isset($_SESSION['myjat_roles']['myjat_secretary'])) {
        return;
    }

    $_SESSION['myjat_roles']['myjat_secretary'] = [
        'name' => 'MYJAT Secretary',

        'capabilities' => [
            'myjat_access',
            'myjat_manage_applications',
            'myjat_view_application',
            'myjat_export_applications',
            'myjat_approve_application',
            'myjat_reject_application',
            'myjat_print_pvc',
            'myjat_mark_card_printed',
        ],
    ];
}


// =========================================================
// Check MYJAT Secretary Role
// =========================================================

function myjat_is_secretary()
{
    return !empty($_SESSION['myjat_roles']['myjat_secretary']);
}


// =========================================================
// Check MYJAT Capability
// =========================================================

function myjat_can($capability)
{
    if (empty($_SESSION['myjat_roles']['myjat_secretary']['capabilities'])) {
        return false;
    }

    return in_array(
        $capability,
        $_SESSION['myjat_roles']['myjat_secretary']['capabilities'],
        true
    );
}


// =========================================================
// Secretary Login Redirect
// =========================================================

function myjat_secretary_login_redirect($redirect_to = '/')
{
    if (!myjat_is_secretary()) {
        return $redirect_to;
    }

    return '/membership-applications/';
}


// =========================================================
// Secretary Admin Access
// =========================================================

function myjat_secretary_admin_access()
{
    return myjat_can('myjat_access');
}


// =========================================================
// Initialize Role System
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

myjat_create_secretary_role();