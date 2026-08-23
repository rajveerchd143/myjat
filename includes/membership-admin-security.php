<?php

// =========================================================
// Admin Security
// Standalone PHP Version
// =========================================================


// =========================================================
// Block Admin Area For Normal Members
// =========================================================

function myjat_admin_security()
{
    if (!myjat_is_logged_in()) {
        return;
    }

    if (myjat_is_admin()) {
        return;
    }

    if (myjat_is_secretary()) {
        return;
    }

    header('Location: /');
    exit;
}


// =========================================================
// Hide Admin Bar
// =========================================================

function myjat_hide_admin_bar()
{
    if (myjat_is_admin()) {
        return true;
    }

    return false;
}


// =========================================================
// Secretary Admin Menu Cleanup
// =========================================================

function myjat_secretary_admin_menu_cleanup()
{
    if (myjat_is_admin()) {
        return;
    }

    if (!myjat_is_secretary()) {
        return;
    }

    return [
        'dashboard',
        'profile',
        'posts',
        'media',
        'comments',
        'themes',
        'plugins',
        'users',
        'tools',
        'settings'
    ];
}


// =========================================================
// Secretary Admin UI Cleanup
// =========================================================

function myjat_secretary_admin_bar_cleanup()
{
    if (myjat_is_admin()) {
        return [];
    }

    if (!myjat_is_secretary()) {
        return [];
    }

    return [
        'wp-logo',
        'about',
        'comments',
        'updates',
        'new-content',
        'search',
        'customize',
        'themes',
        'site-name'
    ];
}


// =========================================================
// Hide Admin Bar Right Side
// =========================================================

function myjat_secretary_admin_bar_right_cleanup()
{
    if (myjat_is_admin()) {
        return [];
    }

    if (!myjat_is_secretary()) {
        return [];
    }

    return [
        'search',
        'my-account'
    ];
}