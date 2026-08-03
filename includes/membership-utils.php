<?php
// =========================================================
// MYJAT Membership Management System
// Module: Utilities
//
// Description:
// Common helper functions used by all modules.
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =========================================================
// Description:
// Get Membership Table Name
// =========================================================

function myjat_membership_table() {

	global $wpdb;

	return $wpdb->prefix . 'membership_applications';

}

// =========================================================
// Description:
// Check Admin Screen
// =========================================================

function myjat_is_admin_page() {

	if ( ! is_admin() ) {
		return false;
	}

	if ( empty( $_GET['page'] ) ) {
		return false;
	}

	return $_GET['page'] === 'myjat-membership-applications';

}

// =========================================================
// Description:
// Get Current Member
// =========================================================

function myjat_get_member( $id ) {

	global $wpdb;

	return $wpdb->get_row(

		$wpdb->prepare(

			"SELECT *
			FROM " . myjat_membership_table() . "
			WHERE id=%d",

			absint( $id )

		)

	);

}

/**
 * =========================================================
 * Description:
 * Get Application By Membership Number
 * =========================================================
 */
function myjat_get_member_by_number( $membership_number ) {

	global $wpdb;

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . myjat_membership_table() . " WHERE membership_number=%s",
			$membership_number
		)
	);
}

/**
 * =========================================================
 * Description:
 * Get Application By ID
 * =========================================================
 */
function myjat_get_membership_application( $id ) {

	global $wpdb;

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM " . myjat_membership_table() . " WHERE id=%d",
			$id
		)
	);

}


/**
 * =========================================================
 * Description:
 * Get All Applications
 * =========================================================
 */
function myjat_get_all_membership_applications( $order = 'DESC' ) {

	global $wpdb;

	$order = strtoupper( $order ) === 'ASC' ? 'ASC' : 'DESC';

	return $wpdb->get_results(
		"SELECT * FROM " . myjat_membership_table() . " ORDER BY id {$order}"
	);

}