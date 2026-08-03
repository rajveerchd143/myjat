<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Number Generator
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * =========================================================
 * Description:
 * Generate Membership Number
 * Format:
 * ABJM-YYYY-000001
 * =========================================================
 */
function myjat_generate_membership_number() {

	global $wpdb;

	$table = $wpdb->prefix . 'membership_applications';

	$year = current_time( 'Y' );

	$last_number = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT membership_no
			FROM {$table}
			WHERE membership_no LIKE %s
			ORDER BY id DESC
			LIMIT 1",
			'ABJM-' . $year . '-%'
		)
	);


	if ( ! empty( $last_number ) ) {

		$parts = explode( '-', $last_number );

    $next = max( 1, absint( end( $parts ) ) + 1 );
	} else {

		$next = 1;

	}

	$membership_number = sprintf(
	'ABJM-%s-%06d',
	$year,
	$next
);

return apply_filters(
	'myjat_membership_number',
	$membership_number,
	$next,
	$year
);
}