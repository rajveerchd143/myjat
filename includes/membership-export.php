<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Export
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//csv export
add_action('admin_init', 'myjat_export_members_csv');

function myjat_export_members_csv() {

    if (
        !current_user_can('manage_options') ||
        !isset($_GET['export_csv'])
    ) {
        return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'membership_applications';

    $members = $wpdb->get_results(
        "SELECT * FROM {$table} ORDER BY id DESC",
        ARRAY_A
    );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=myjat-members.csv');

    $output = fopen('php://output', 'w');

    if (!empty($members)) {

        fputcsv($output, array_keys($members[0]));

        foreach ($members as $member) {
            fputcsv($output, $member);
        }
    }

    fclose($output);
    exit;
}
//export_csv close
