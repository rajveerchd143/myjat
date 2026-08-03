<?php
/*
Template Name: Member Card
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

while ( have_posts() ) {
	the_post();
	the_content();
}

echo do_shortcode( '[jat_member_card]' );