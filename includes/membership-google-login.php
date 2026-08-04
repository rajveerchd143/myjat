<?php
// =========================================================
// MYJAT Membership Management System
// Module: Google Login
// =========================================================
if (!defined('ABSPATH')) {
	exit;
}

/*
|--------------------------------------------------------------------------
| Google OAuth Configuration
|--------------------------------------------------------------------------
*/


require_once __DIR__ . '/membership-config.php';
/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
*/

if(!defined('MYJAT_GOOGLE_CLIENT_ID')){
    define('MYJAT_GOOGLE_CLIENT_ID','YOUR_GOOGLE_CLIENT_ID');
}
die(MYJAT_GOOGLE_CLIENT_ID);
if(!defined('MYJAT_GOOGLE_CLIENT_SECRET')){
    define('MYJAT_GOOGLE_CLIENT_SECRET','YOUR_GOOGLE_CLIENT_SECRET');
}

if(!defined('MYJAT_GOOGLE_REDIRECT_URI')){
    define('MYJAT_GOOGLE_REDIRECT_URI',admin_url('admin-ajax.php?action=myjat_google_callback'));
}

/*
|--------------------------------------------------------------------------
| OAuth State
|--------------------------------------------------------------------------
*/

function myjat_google_create_state(){

	$state=wp_generate_password(64,false,false);

	set_transient(
		'myjat_google_'.$state,
		time(),
		10*MINUTE_IN_SECONDS
	);

	return $state;

}

function myjat_google_verify_state($state){

	$key='myjat_google_'.$state;

	$value=get_transient($key);

	delete_transient($key);

	return !empty($value);

}

/*
|--------------------------------------------------------------------------
| Google Login URL
|--------------------------------------------------------------------------
*/

function myjat_google_login_url(){

	$params=array(

		'client_id'=>MYJAT_GOOGLE_CLIENT_ID,

		'redirect_uri'=>MYJAT_GOOGLE_REDIRECT_URI,

		'response_type'=>'code',

		'scope'=>'openid email profile',

		'access_type'=>'online',

		'prompt'=>'select_account',

		'include_granted_scopes'=>'true',

		'state'=>myjat_google_create_state()

	);

	return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query($params);
	wp_die($url);
}

function myjat_google_login_button($text='Continue with Google'){

	return '
	<a class="myjat-google-login" href="'.esc_url(myjat_google_login_url()).'">

	<svg width="20" height="20" viewBox="0 0 48 48" aria-hidden="true">
	<path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.651 32.657 29.239 36 24 36c-6.627 0-12-5.373-12-12S17.373 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
	<path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4C16.318 4 9.656 8.337 6.306 14.691z"/>
	<path fill="#4CAF50" d="M24 44c5.167 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.153 35.091 26.715 36 24 36c-5.218 0-9.617-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
	<path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-1.056 3.09-3.256 5.508-6.084 6.57l6.19 5.238C38.973 36.511 44 30.779 44 24c0-1.341-.138-2.65-.389-3.917z"/>
	</svg>

	<span>'.esc_html($text).'</span>

	</a>';

}
/*
|--------------------------------------------------------------------------
| Google Callback_Bhai hO gya 
|--------------------------------------------------------------------------
*/

add_action('wp_ajax_nopriv_myjat_google_callback','myjat_google_callback');
add_action('wp_ajax_myjat_google_callback','myjat_google_callback');

function myjat_google_callback(){
	if(empty($_GET['code'])){
		wp_die('Missing authorization code.');
	}

	if(empty($_GET['state'])){
		wp_die('Invalid request.');
	}

	$state=sanitize_text_field($_GET['state']);

	if(!myjat_google_verify_state($state)){
		wp_die('Security validation failed.');
	}

	$response=wp_remote_post(
		'https://oauth2.googleapis.com/token',
		array(
			'timeout'=>20,
			'body'=>array(
				'code'=>sanitize_text_field($_GET['code']),
				'client_id'=>MYJAT_GOOGLE_CLIENT_ID,
				'client_secret'=>MYJAT_GOOGLE_CLIENT_SECRET,
				'redirect_uri'=>MYJAT_GOOGLE_REDIRECT_URI,
				'grant_type'=>'authorization_code'
			)
		)
	);

	if(is_wp_error($response)){
		wp_die('Unable to contact Google.');
	}

	$token=json_decode(wp_remote_retrieve_body($response),true);

	if(empty($token['access_token'])){
		wp_die('Google authentication failed.');
	}

	$response=wp_remote_get(
		'https://www.googleapis.com/oauth2/v3/userinfo',
		array(
			'timeout'=>20,
			'headers'=>array(
				'Authorization'=>'Bearer '.$token['access_token']
			)
		)
	);

	if(is_wp_error($response)){
		wp_die('Unable to fetch Google profile.');
	}

	$profile=json_decode(wp_remote_retrieve_body($response),true);

	if(empty($profile['email'])){
		wp_die('Google email not found.');
	}

	$email=sanitize_email($profile['email']);



	$name=!empty($profile['name']) ? sanitize_text_field($profile['name']) : '';

	$google_id=!empty($profile['sub']) ? sanitize_text_field($profile['sub']) : '';

	$picture=!empty($profile['picture']) ? esc_url_raw($profile['picture']) : '';

    /*
|--------------------------------------------------------------------------
| Find or Create WordPress User
|--------------------------------------------------------------------------
*/

	$user=get_user_by('email',$email);

	if(!$user){

		$username=sanitize_user(current(explode('@',$email)),true);

		if(empty($username)){
			$username='member';
		}

		$base=$username;
		$i=1;

		while(username_exists($username)){
			$username=$base.$i;
			$i++;
		}

		$password=wp_generate_password(32,true,true);

		$user_id=wp_create_user(
			$username,
			$password,
			$email
		);

		if(is_wp_error($user_id)){
			wp_die($user_id->get_error_message());
		}

		wp_update_user(array(
			'ID'=>$user_id,
			'display_name'=>$name,
			'first_name'=>$name
		));

		update_user_meta($user_id,'google_id',$google_id);
		update_user_meta($user_id,'google_email',$email);
		update_user_meta($user_id,'google_picture',$picture);

		$user=get_user_by('id',$user_id);

	}

	if(!$user instanceof WP_User){
		wp_die('Unable to load WordPress user.');
	}

	myjat_login_user($user);

	wp_safe_redirect(myjat_login_redirect($user));

	exit;

}
