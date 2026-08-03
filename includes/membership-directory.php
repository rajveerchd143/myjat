<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Directory
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



add_shortcode('myjat_members_directory', function () {

    global $wpdb;

    $table = $wpdb->prefix . 'membership_applications';

    $members = $wpdb->get_results("
        SELECT *
        FROM {$table}
        WHERE status = 'Approved'
        ORDER BY membership_no ASC
    ");

    ob_start();

    echo '<table class="wp-list-table widefat striped">';
    echo '<thead>
            <tr>
                <th>ABJM No</th>
                <th>नाम</th>
                <th>जिला</th>
                <th>सदस्यता प्रकार</th>
                <th>सदस्य वर्ष</th>
            </tr>
          </thead>';

    echo '<tbody>';

    foreach ($members as $member) {

        echo '<tr>';

        echo '<td>' . esc_html($member->membership_no) . '</td>';
        echo '<td>' . esc_html($member->full_name) . '</td>';
        echo '<td>' . esc_html($member->district) . '</td>';
        echo '<td>' . esc_html($member->membership_type) . '</td>';
        echo '<td>' . esc_html($member->member_since) . '</td>';

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    return ob_get_clean();
});


