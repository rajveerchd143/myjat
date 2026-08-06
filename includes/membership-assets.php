<?php

if (!defined('ABSPATH')) {
	exit;
}

// =========================================================
// MYJAT Membership Management System
// Module: Assets
// =========================================================

// =========================================================
// Master Design System CSS
// =========================================================
function myjat_enqueue_master_css()
{
	$file = get_stylesheet_directory() . '/assets/css/components/membership.css';

	if (file_exists($file)) {
		wp_enqueue_style(
			'myjat-membership',
			get_stylesheet_directory_uri() . '/assets/css/components/membership.css',
			array(),
			filemtime($file)
		);
	}
}


function myjat_enqueue_website_css()
{
	$file = get_stylesheet_directory() . '/assets/css/website.css';

	if (file_exists($file)) {
		wp_enqueue_style(
			'myjat-website',
			get_stylesheet_directory_uri() . '/assets/css/website.css',
			array('myjat-membership'),
			filemtime($file)
		);
	}

$home_js = get_stylesheet_directory() . '/assets/js/pages/home.js';

if (file_exists($home_js)) {
	wp_enqueue_script(
		'myjat-home',
		get_stylesheet_directory_uri() . '/assets/js/pages/home.js',
		array(),
		filemtime($home_js),
		true
	);
}

wp_enqueue_style(
    'myjat-tom-select',
    get_stylesheet_directory_uri() . '/assets/vendor/tom-select/tom-select.css',
    array(),
    '2.4.3'
);

wp_enqueue_script(
    'myjat-tom-select',
    get_stylesheet_directory_uri() . '/assets/vendor/tom-select/tom-select.complete.min.js',
    array(),
    '2.4.3',
    true
);

wp_enqueue_script(
    'myjat-location',
    get_stylesheet_directory_uri() . '/assets/js/location.js',
    array( 'myjat-tom-select' ),
    '1.0',
    true
);

wp_localize_script(
    'myjat-location',
    'myjatLocation',
    array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    )
);

$website_js = get_stylesheet_directory() . '/assets/js/website.js';

if (file_exists($website_js)) {
	wp_enqueue_script(
		'myjat-website',
		get_stylesheet_directory_uri() . '/assets/js/website.js',
		array(),
		filemtime($website_js),
		true
	);
}
}

// =========================================================
// Registration Assets
// =========================================================
function myjat_enqueue_registration_assets()
{
	myjat_enqueue_master_css();

	$file = get_stylesheet_directory() . '/assets/js/membership-form.js';

	if (file_exists($file)) {
		wp_enqueue_script(
			'myjat-membership-form',
			get_stylesheet_directory_uri() . '/assets/js/membership-form.js',
			array(),
			filemtime($file),
			true
		);
	}
}

// =========================================================
// View Assets
// =========================================================
function myjat_enqueue_view_assets()
{
	myjat_enqueue_master_css();
}

// =========================================================
// Applications Assets
// =========================================================
function myjat_enqueue_applications_assets()
{
	myjat_enqueue_master_css();
}

// =========================================================
// PVC Assets
// =========================================================
function myjat_enqueue_pvc_assets()
{
	myjat_enqueue_master_css();
}

// =========================================================
// Frontend Loader
// =========================================================
function myjat_membership_assets()
{

	myjat_enqueue_master_css();
	myjat_enqueue_website_css();

	myjat_enqueue_registration_assets();
	myjat_enqueue_applications_assets();
	myjat_enqueue_view_assets();
	myjat_enqueue_pvc_assets();
}
add_action('wp_enqueue_scripts', 'myjat_membership_assets', 999);

// =========================================================
// Admin Loader
// =========================================================
function myjat_membership_admin_assets($hook)
{
	if (strpos($hook, 'myjat-membership') === false) {
		return;
	}

	myjat_enqueue_master_css();
}
add_action('admin_enqueue_scripts', 'myjat_membership_admin_assets');