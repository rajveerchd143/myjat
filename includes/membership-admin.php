<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Admin
//
// Description:
// Admin Menu & Membership Applications
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// wordpress menu
add_action('admin_menu', 'myjat_membership_admin_menu');

function myjat_membership_admin_menu() {

    add_menu_page(
    'सदस्यता आवेदन',
    'सदस्यता आवेदन',
    'myjat_access',
    'myjat-membership-applications',
    'myjat_membership_admin_page',
    'dashicons-groups',
    25
);
    add_submenu_page(
    'myjat-membership-applications',
    'Organization Settings',
    'Organization Settings',
    'manage_options',
    'myjat-settings',
    'myjat_settings_page'
);
}

// wordpress menu close






//update application status

add_action('admin_init', 'myjat_update_application_status');

function myjat_update_application_status() {

    if (
        !current_user_can('manage_options') ||
        !isset($_GET['myjat_action']) ||
        !isset($_GET['application_id'])
    ) {
        return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'membership_applications';

    $id = (int) $_GET['application_id'];

    $row = $wpdb->get_row(
    "SELECT membership_no
    FROM {$table}
    WHERE id = {$id}"
    );

    $membership_no = $row->membership_no ?? '';
    if ($_GET['myjat_action'] === 'approve') {

    $current_user = wp_get_current_user();

    $wpdb->update(
        $table,
        array(
            'status'         => 'Approved',
            'approval_date'  => current_time('Y-m-d'),
            'approved_by'    => $current_user->user_login,
            'member_since'   => date('Y'),
            'qr_code' => home_url('/member-verification/?id=' . $membership_no),
        ),
        array('id' => $id)
    );
    } elseif ($_GET['myjat_action'] === 'reject') {

        $wpdb->update(
            $table,
            array('status' => 'Rejected'),
            array('id' => $id)
        );
    }
}

//update application status close


add_action('admin_init', 'myjat_save_photo_url');

function myjat_save_photo_url() {

    if (
        !current_user_can('manage_options') ||
        empty($_POST['member_id']) ||
        !isset($_POST['photo_url'])
    ) {
        return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'membership_applications';

    $wpdb->update(
        $table,
        array(
            'photo_url' => esc_url_raw($_POST['photo_url'])
        ),
        array(
            'id' => intval($_POST['member_id'])
        )
    );
}


// mark card printed
add_action('admin_init', 'myjat_mark_card_printed');

function myjat_mark_card_printed() {

    if (
        !current_user_can('manage_options') ||
        !isset($_GET['mark_card_printed'])
    ) {
        return;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'membership_applications';

    $wpdb->update(
        $table,
        array(
            'card_printed' => 'Yes'
        ),
        array(
            'id' => intval($_GET['mark_card_printed'])
        )
    );
}

// mark card printed close


//admin head
add_action('admin_head', function () {
?>
<style>

@media print {

    #adminmenumain,
    #wpadminbar,
    .notice,
    .update-nag,
    .button,
    .button-primary,
    .wrap h1 + p {
        display:none !important;
    }

    #wpcontent,
    #wpbody,
    #wpbody-content {
        margin:0 !important;
        padding:0 !important;
    }

    table {
        width:100% !important;
        border-collapse:collapse !important;
    }

    table th,
    table td {
        border:1px solid #000 !important;
        padding:8px !important;
    }

    body {
        background:#fff !important;
    }

}

</style>
<?php
});
//admin head close









