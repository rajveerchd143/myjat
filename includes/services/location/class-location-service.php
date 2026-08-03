<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MyJat_Location_Service {

	public static function get_states() {

		global $wpdb;

		return $wpdb->get_col(
			"SELECT DISTINCT state_name
			 FROM {$wpdb->prefix}myjat_locations
			 ORDER BY state_name ASC"
		);

	}

	public static function get_districts( $state ) {

		global $wpdb;

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT district_name
				 FROM {$wpdb->prefix}myjat_locations
				 WHERE state_name=%s
				 ORDER BY district_name ASC",
				$state
			)
		);


	}


	public static function get_blocks( $district ) {

	global $wpdb;

	return $wpdb->get_col(

		$wpdb->prepare(

			"SELECT DISTINCT block_name
			FROM {$wpdb->prefix}myjat_locations
			WHERE district_name=%s
			AND block_name <> ''
			ORDER BY block_name ASC",

			$district

		)

	);

}


public static function get_villages( $state, $district, $block ) {

	global $wpdb;

	$table = $wpdb->prefix . 'myjat_locations';

	return $wpdb->get_col(

		$wpdb->prepare(

			"SELECT DISTINCT village_name

			FROM {$table}

			WHERE state_name = %s
			AND district_name = %s
			AND block_name = %s
			AND village_name IS NOT NULL
			AND village_name <> ''

			ORDER BY village_name ASC",

			$state,
			$district,
			$block

		)

	);

}




public static function get_pincode( $state, $district, $block, $village ) {

    global $wpdb;

    $table = $wpdb->prefix . 'myjat_locations';

    return $wpdb->get_var(

        $wpdb->prepare(

            "SELECT pincode
             FROM {$table}
             WHERE state_name=%s
             AND district_name=%s
             AND block_name=%s
             AND village_name=%s
             LIMIT 1",

            $state,
            $district,
            $block,
            $village

        )

    );

}



}