<?php
// =========================================================
// MYJAT Membership Management System
// Module: Roles
//
// Description:
// Creates and manages MYJAT custom user roles.
//
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =========================================================
// Description:
// Create MYJAT Secretary Role
// =========================================================

function myjat_create_secretary_role() {

	// Don't recreate if role already exists.
	if ( get_role( 'myjat_secretary' ) ) {
		return;
	}

	add_role(
		'myjat_secretary',
		'MYJAT Secretary',
		array(

			'read' => true,

			// =====================================================
			// MYJAT Capabilities
			// =====================================================

			'myjat_access'                => true,
			'myjat_manage_applications'   => true,
			'myjat_view_application'      => true,
			'myjat_export_applications'   => true,
			'myjat_approve_application'   => true,
			'myjat_reject_application'    => true,
			'myjat_print_pvc'             => true,
			'myjat_mark_card_printed'     => true,

		)
	);

}

add_action(
	'init',
	'myjat_create_secretary_role'
);

// =========================================================
// Description:
// Redirect MYJAT Secretary After Login
// =========================================================

function myjat_secretary_login_redirect( $redirect_to, $requested_redirect_to, $user ) {

	if ( empty( $user ) || ! isset( $user->roles ) ) {
		return $redirect_to;
	}

	if ( in_array( 'myjat_secretary', (array) $user->roles, true ) ) {

		return admin_url( 'admin.php?page=myjat-membership-applications' );

	}

	return $redirect_to;

}

add_filter(
	'login_redirect',
	'myjat_secretary_login_redirect',
	10,
	3
);

// =========================================================
// Description:
// Hide WordPress Menu for MYJAT Secretary
// =========================================================

function myjat_secretary_admin_menu() {

	if ( ! current_user_can( 'myjat_access' ) ) {
		return;
	}

	if ( current_user_can( 'administrator' ) ) {
		return;
	}

	remove_menu_page( 'index.php' );                  // Dashboard
	remove_menu_page( 'edit.php' );                   // Posts
	remove_menu_page( 'upload.php' );                 // Media
	remove_menu_page( 'edit.php?post_type=page' );    // Pages
	remove_menu_page( 'edit-comments.php' );          // Comments
	remove_menu_page( 'themes.php' );                 // Appearance
	remove_menu_page( 'plugins.php' );                // Plugins
	remove_menu_page( 'users.php' );                  // Users
	remove_menu_page( 'tools.php' );                  // Tools
	remove_menu_page( 'options-general.php' );        // Settings

}

add_action(
	'admin_menu',
	'myjat_secretary_admin_menu',
	999
);

// =========================================================
// Description:
// Redirect MYJAT Secretary to Membership Applications
// when opening WordPress Admin.
// =========================================================

function myjat_secretary_admin_redirect() {

    if ( ! is_admin() ) {
        return;
    }

    if ( ! current_user_can( 'myjat_access' ) ) {
        return;
    }

    if ( current_user_can( 'administrator' ) ) {
        return;
    }

    global $pagenow;

    if ( 'index.php' !== $pagenow ) {
        return;
    }

    wp_safe_redirect(
        admin_url( 'admin.php?page=myjat-membership-applications' )
    );

    exit;
}

add_action(
    'admin_init',
    'myjat_secretary_admin_redirect'
);