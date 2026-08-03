<?php

if(!defined('ABSPATH')){
	exit;
}

// =========================================================
// Block wp-admin for normal members
// =========================================================

add_action('admin_init','myjat_admin_security');

function myjat_admin_security(){

	if(!is_user_logged_in()){
		return;
	}

	if(current_user_can('administrator')){
		return;
	}

	if(current_user_can('myjat_access')){
		return;
	}

	if(wp_doing_ajax()){
		return;
	}

	wp_safe_redirect(home_url('/'));

	exit;

}


// =========================================================
// Hide Admin Bar
// =========================================================

add_action('after_setup_theme','myjat_hide_admin_bar');

function myjat_hide_admin_bar(){

	if(current_user_can('administrator')){
		return;
	}

	show_admin_bar(false);

}
// =========================================================
// Secretary Admin Menu Cleanup
// =========================================================

add_action('admin_menu','myjat_secretary_admin_menu_cleanup',999);

function myjat_secretary_admin_menu_cleanup(){

	if(current_user_can('administrator')){
		return;
	}

	if(!current_user_can('myjat_secretary')){
		return;
	}

	remove_menu_page('index.php');
	remove_menu_page('profile.php');
	remove_menu_page('edit.php');
	remove_menu_page('upload.php');
	remove_menu_page('edit-comments.php');
	remove_menu_page('themes.php');
	remove_menu_page('plugins.php');
	remove_menu_page('users.php');
	remove_menu_page('tools.php');
	remove_menu_page('options-general.php');
	remove_menu_page('separator1');
	remove_menu_page('separator2');
	remove_menu_page('separator-last');

}

// =========================================================
// Secretary Admin UI Cleanup
// =========================================================

add_action('admin_bar_menu','myjat_secretary_admin_bar_cleanup',999);

function myjat_secretary_admin_bar_cleanup($wp_admin_bar){

	if(current_user_can('administrator')){
		return;
	}

	if(!current_user_can('myjat_access')){
		return;
	}

	$wp_admin_bar->remove_node('wp-logo');
	$wp_admin_bar->remove_node('about');
	$wp_admin_bar->remove_node('comments');
	$wp_admin_bar->remove_node('updates');
	$wp_admin_bar->remove_node('new-content');
	$wp_admin_bar->remove_node('search');
	$wp_admin_bar->remove_node('customize');
	$wp_admin_bar->remove_node('themes');
	$wp_admin_bar->remove_node('site-name');

}

// =========================================================
// Hide Admin Bar Right Side
// =========================================================

add_action('wp_before_admin_bar_render',function(){

	if(current_user_can('administrator')){
		return;
	}

	if(!current_user_can('myjat_access')){
		return;
	}

	global $wp_admin_bar;

	$wp_admin_bar->remove_menu('search');
	$wp_admin_bar->remove_menu('my-account');

},999);