<?php

// =========================================================
// MYJAT Membership Management System
// Production Version
//
// Website:
// https://myjat.in
//
// IMPORTANT:
//
// This file only loads project modules.
//
// Do not place large modules in this file.
//
// =========================================================
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('kleo-parent-style',get_template_directory_uri() . '/style.css');
});


// =========================================================
// MYJAT Membership System
// =========================================================

if ( file_exists( get_stylesheet_directory() . '/includes/membership-loader.php' ) ) {
	require_once get_stylesheet_directory() . '/includes/membership-loader.php';
}

