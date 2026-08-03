<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Handler
//
// Description:
// Handles membership form submission,
// validates input,
// prepares application data,
// and stores applications.
//
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =========================================================
// Description:
// Handles membership form submission.
// =========================================================

add_action('init','myjat_handle_membership_form_submission');
function myjat_handle_membership_form_submission(){
 if(empty($_POST['myjat_membership_submit'])) return;
 if(!isset($_POST['myjat_membership_nonce'])||!wp_verify_nonce($_POST['myjat_membership_nonce'],'myjat_membership_form')) wp_die('Security verification failed.');
 
 $current_user=wp_get_current_user();

$google_email='';

$google_id='';

$wp_user_id=0;

if(is_user_logged_in()){

	$google_email=$current_user->user_email;

	$google_id=get_user_meta($current_user->ID,'google_id',true);

	$wp_user_id=$current_user->ID;

}

global $wpdb;

$table=$wpdb->prefix.'membership_applications';

if($wp_user_id){

	$existing=$wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$table} WHERE wp_user_id=%d LIMIT 1",
			$wp_user_id
		)
	);

	if($existing){

		wp_safe_redirect(
			add_query_arg(
				'application',
				'exists',
				wp_get_referer()
			)
		);

		exit;

	}

}


 
 $data=[
 'full_name'=>sanitize_text_field($_POST['full_name']??''),
 'father_husband_name'=>sanitize_text_field($_POST['father_husband_name']??''),
 'mobile_no'=>sanitize_text_field($_POST['mobile_no']??''),
 'phone_no'=>sanitize_text_field($_POST['phone_no']??''),


 'email'=>sanitize_email($_POST['email']??''),
'google_email'=>$google_email,
'google_id'=>$google_id,
'wp_user_id'=>$wp_user_id,

 'dob'=>sanitize_text_field($_POST['dob']??''),
 'gender'=>sanitize_text_field($_POST['gender']??''),
 'education'=>sanitize_text_field($_POST['education']??''),
 'profession'=>sanitize_text_field($_POST['profession']??''),
 'membership_type'=>sanitize_text_field($_POST['membership_type']??''),
 'current_address'=>sanitize_textarea_field($_POST['current_address']??''),
 'permanent_address'=>sanitize_textarea_field($_POST['permanent_address']??''),
 'village_panchayat'=>sanitize_text_field($_POST['village_panchayat']??''),
 'block_name'=>sanitize_text_field($_POST['block_name']??''),
 'vidhansabha'=>sanitize_text_field($_POST['vidhansabha']??''),
 'loksabha'=>sanitize_text_field($_POST['loksabha']??''),
 'district'=>sanitize_text_field($_POST['district']??''),
 'state_name'=>sanitize_text_field($_POST['state_name']??''),
 'pincode'=>sanitize_text_field($_POST['pincode']??''),
 'aadhaar_no'=>sanitize_text_field($_POST['aadhaar_no']??''),
 'family_details'=>sanitize_textarea_field($_POST['family_details']??''),
 'recommender_name'=>sanitize_text_field($_POST['recommender_name']??''),
 'recommender_membership_no'=>sanitize_text_field($_POST['recommender_membership_no']??''),
 'recommender_mobile'=>sanitize_text_field($_POST['recommender_mobile']??'')
 ];

 $data['membership_no'] = myjat_generate_membership_number();
 $data['status']='pending';
$data['application_date'] = current_time( 'Y-m-d' );
$data['created_at']       = current_time( 'mysql' );


 $data['photo_url']=myjat_upload_member_photo();
 
 
 // =========================================================
// Description:
// Prevent Duplicate Submission
// =========================================================

if ( headers_sent() ) {

    wp_die(
        'Headers already sent. Unable to redirect safely.'
    );

}
 $id=myjat_save_membership_application($data);
 
// =========================================================
// Description:
// Redirect To Success Page
// =========================================================

$redirect_url = add_query_arg(

    array(

        'membership' => 'submitted',

        'member_no'  => $data['membership_no'],

    ),

    wp_get_referer()

);

wp_safe_redirect( $redirect_url );

exit;

}
