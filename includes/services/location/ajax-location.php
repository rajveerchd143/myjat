<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_myjat_location', 'myjat_location_ajax' );
add_action( 'wp_ajax_nopriv_myjat_location', 'myjat_location_ajax' );

function myjat_location_ajax() {

	$type   = sanitize_key( $_POST['type'] ?? '' );
	$parent = sanitize_text_field( $_POST['parent'] ?? '' );

	switch ( $type ) {

		case 'district':

			$data = MyJat_Location_Service::get_districts( $parent );
			break;

            case 'block':
        	$data = MyJat_Location_Service::get_blocks( $parent );
            break;

			case 'village':
			$data = MyJat_Location_Service::get_villages(
			sanitize_text_field( $_POST['state'] ?? '' ),
			sanitize_text_field( $_POST['district'] ?? '' ),
			sanitize_text_field( $_POST['block'] ?? '' )
			);
			break;

			case 'pincode':
		    $data = MyJat_Location_Service::get_pincode(
			sanitize_text_field( $_POST['state'] ?? '' ),
			sanitize_text_field( $_POST['district'] ?? '' ),
			sanitize_text_field( $_POST['block'] ?? '' ),
			sanitize_text_field( $_POST['village'] ?? '' )

			);

			break;





            default:
			$data = array();

        

	}

	wp_send_json_success( $data );

}