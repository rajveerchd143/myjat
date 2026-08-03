<?php
// =========================================================
// MYJAT Membership Management System
// Module: Database
// File: membership-database.php
//
// Description:
// Centralized database layer for Membership Management System.
// All database operations must be performed through this file.
//
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * =========================================================
 * Description:
 * Insert Membership Application
 * =========================================================
 */
/**
 * =========================================================
 * Description:
 * Insert Membership Application
 * =========================================================
 */


function myjat_save_membership_application( $data ) {

	global $wpdb;

	$table = myjat_membership_table();

	$columns = $wpdb->get_col( "DESC {$table}", 0 );

	$data = array_intersect_key(
		$data,
		array_flip( $columns )
	);

	

	$result = $wpdb->insert(
		$table,
		$data
	);
if ( false === $result ) {

    wp_die(
        '<pre>' .
        esc_html( $wpdb->last_error ) .
        "\n\nSQL:\n" .
        esc_html( $wpdb->last_query ) .
        '</pre>'
    );

}

}
/**
 * =========================================================
 * Description:
 * Update Membership Application
 * =========================================================
 */
function myjat_update_membership_application( $id, $data ) {

	global $wpdb;

	return $wpdb->update(
		myjat_membership_table(),
		$data,
		array(
			'id' => absint( $id ),
		)
	);

}



/**
 * =========================================================
 * Description:
 * Delete Application
 * =========================================================
 */
function myjat_delete_membership_application( $id ) {

	global $wpdb;

	return $wpdb->delete(
		myjat_membership_table(),
		array(
			'id' => absint( $id ),
		)
	);

}


/**
 * =========================================================
 * Description:
 * Search Applications
 * =========================================================
 */
function myjat_search_membership_applications( $keyword ) {

	global $wpdb;

	$like = '%' . $wpdb->esc_like( $keyword ) . '%';

	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT *
			FROM " . myjat_membership_table() . "
			WHERE
				full_name LIKE %s
				OR father_name LIKE %s
				OR membership_number LIKE %s
				OR district LIKE %s
			ORDER BY id DESC",
			$like,
			$like,
			$like,
			$like
		)
	);

}

/**
 * =========================================================
 * Description:
 * Count Applications
 * =========================================================
 */
function myjat_total_membership_applications() {

	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM " . myjat_membership_table()
	);

}

/**
 * =========================================================
 * Description:
 * Count Approved Members
 * =========================================================
 */
function myjat_total_approved_members() {

	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM " . myjat_membership_table() . " WHERE status='approved'"
	);

}

/**
 * =========================================================
 * Description:
 * Count Pending Members
 * =========================================================
 */
function myjat_total_pending_members() {

	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM " . myjat_membership_table() . " WHERE status='pending'"
	);

}

/**
 * =========================================================
 * Description:
 * Count Rejected Members
 * =========================================================
 */
function myjat_total_rejected_members() {

	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM " . myjat_membership_table() . " WHERE status='rejected'"
	);

}