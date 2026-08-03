<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Photo Upload
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * =========================================================
 * Description:
 * Upload Member Photo
 * =========================================================
 */
function myjat_upload_member_photo() {

	if ( empty( $_FILES['photo']['name'] ) ) {
		return '';
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$allowed_types = array(
		'image/jpeg',
		'image/jpg',
		'image/png',
		'image/webp',
	);

	$file_type = wp_check_filetype( $_FILES['photo']['name'] );

	if ( ! in_array( $file_type['type'], $allowed_types, true ) ) {
		wp_die( 'Only JPG, PNG and WEBP images are allowed.' );
	}

	if ( $_FILES['photo']['size'] > 2 * 1024 * 1024 ) {
		wp_die( 'Maximum photo size is 2 MB.' );
	}

	$upload = wp_handle_upload(
		$_FILES['photo'],
		array(
			'test_form' => false,
		)
	);

	if ( isset( $upload['error'] ) ) {
		wp_die( esc_html( $upload['error'] ) );
	}

	return esc_url_raw( $upload['url'] );
}