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



add_action('admin_init', function () {

    if (!current_user_can('manage_options')) {
        return;
    }

    if (!isset($_GET['myjat_import'])) {
        return;
    }

    require_once get_stylesheet_directory() .
        '/includes/services/location/class-location-importer.php';

    $result = MyJat_Location_Importer::import_pincode();

    echo '<h2>MYJAT PINCODE IMPORT</h2>';

    echo '<p>Updated : <strong>' . $result['updated'] . '</strong></p>';

    echo '<p>Offset : <strong>' . $result['offset'] . '</strong></p>';

    if (!$result['done']) {

        echo '<meta http-equiv="refresh" content="0.3">';

        echo '<p>Importing next batch...</p>';

    } else {

        echo '<h2 style="color:green">✅ PINCODE IMPORT COMPLETE</h2>';

    }

    exit;

});