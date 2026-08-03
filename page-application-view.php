<?php
/*
Template Name: Application View
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();
	the_content();
}

echo do_shortcode( '[jat_application_view]' );

get_footer();