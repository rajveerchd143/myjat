<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/

add_action('init','myjat_auth_session',1);

function myjat_auth_session(){

	if(session_status()===PHP_SESSION_NONE){

		session_start();

	}

}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function myjat_is_logged_in(){

	return is_user_logged_in();

}

function myjat_current_user(){

	return wp_get_current_user();

}

function myjat_current_user_id(){

	return get_current_user_id();

}

function myjat_is_admin(){

	return current_user_can('administrator');

}

function myjat_is_secretary(){

	return current_user_can('myjat_access');

}

function myjat_is_member(){

	return myjat_is_logged_in() && !myjat_is_admin() && !myjat_is_secretary();

}

/*
|--------------------------------------------------------------------------
| Redirect After Login
|--------------------------------------------------------------------------
*/

function myjat_login_redirect($user){

	if(user_can($user,'administrator')){

		return admin_url();

	}

	if(user_can($user,'myjat_access')){

		return admin_url('admin.php?page=myjat-membership-applications');

	}

	return home_url('/');

}/*
|--------------------------------------------------------------------------
| Native Login Engine
|--------------------------------------------------------------------------
*/

function myjat_login_user($user){

	if(!$user instanceof WP_User){

		return false;

	}

	wp_clear_auth_cookie();

	wp_set_current_user($user->ID);

	wp_set_auth_cookie($user->ID,true,is_ssl());

	do_action('wp_login',$user->user_login,$user);

	return true;

}

function myjat_login_credentials($username,$password,$remember=true){

	$creds=array(
		'user_login'=>sanitize_text_field($username),
		'user_password'=>$password,
		'remember'=>$remember
	);

	$user=wp_signon($creds,is_ssl());

	if(is_wp_error($user)){

		return $user;

	}

	myjat_login_user($user);

	return $user;

}

/*
|--------------------------------------------------------------------------
| AJAX Login
|--------------------------------------------------------------------------
*/

add_action('wp_ajax_nopriv_myjat_ajax_login','myjat_ajax_login');

function myjat_ajax_login(){

	if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'],'myjat_login')){

		wp_send_json_error(array(
			'message'=>'Security validation failed.'
		));

	}

	$username=isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';

	$password=isset($_POST['password']) ? $_POST['password'] : '';

	$remember=!empty($_POST['remember']);

	$user=myjat_login_credentials($username,$password,$remember);

	if(is_wp_error($user)){

		wp_send_json_error(array(
			'message'=>'Invalid username or password.'
		));

	}

	wp_send_json_success(array(
        
		'redirect'=>myjat_login_redirect($user)
	));

}

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

function myjat_logout(){

	wp_logout();

	wp_safe_redirect(home_url('/login'));

	exit;

}

add_action('admin_post_myjat_logout','myjat_logout');

add_action('admin_post_nopriv_myjat_logout','myjat_logout');

/*
|--------------------------------------------------------------------------
| Login Shortcode
|--------------------------------------------------------------------------
*/
wp_die('AUTH FILE LOADED');
add_shortcode('myjat_login','myjat_login_shortcode');

function myjat_login_shortcode(){

	ob_start();

	?>

	<div class="myjat-login-wrapper">

		<h2>MYJAT Login</h2>

<?php echo myjat_google_login_button(); ?>

<div class="myjat-social-login">

</div>

<div class="myjat-login-divider">

	<span>OR</span>

</div>

<form id="myjat-login-form">

			<?php wp_nonce_field('myjat_login','myjat_login_nonce'); ?>

			<p>
				<input type="text" name="username" placeholder="Email or Username" required>
			</p>

			<p>
				<input type="password" name="password" placeholder="Password" required>
			</p>

			<p>
				<label>
					<input type="checkbox" name="remember" value="1">
					Remember Me
				</label>
			</p>

			<p>
				<button type="submit">Login</button>
			</p>

			<div id="myjat-login-message"></div>

		</form>

	</div>

	<?php

	return ob_get_clean();

}

add_filter('logout_redirect','myjat_logout_redirect',10,3);

function myjat_logout_redirect($redirect_to,$requested_redirect_to,$user){

	return home_url('/login');

}