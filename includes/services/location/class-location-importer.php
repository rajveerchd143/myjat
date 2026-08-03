<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MyJat_Location_Importer {

	const BATCH_SIZE = 10000;

	public static function import_lgd() {

		global $wpdb;

		$table = $wpdb->prefix . 'myjat_locations';

		$file = __DIR__ . '/raw/lgd-villages.csv';

		if ( ! file_exists( $file ) ) {
			return array(
				'done' => true,
				'imported' => 0
			);
		}

		$offset = (int) get_option( 'myjat_location_import_offset', 0 );

		$handle = fopen( $file, 'r' );

		fgetcsv( $handle );

		$current = 0;
		$imported = 0;

		while ( ( $row = fgetcsv( $handle ) ) !== false ) {

			$current++;

			if ( $current <= $offset ) {
				continue;
			}

			$wpdb->insert(
				$table,
				array(
					'state_name'    => $row[1],
					'district_name' => $row[3],
					'block_name'    => $row[5],
					'village_name'  => $row[7]
				)
			);

			$imported++;

			if ( $imported >= self::BATCH_SIZE ) {
				break;
			}
		}

		fclose( $handle );

		update_option(
			'myjat_location_import_offset',
			$offset + $imported
		);

		return array(

			'done'      => $imported < self::BATCH_SIZE,

			'imported'  => $imported,

			'offset'    => $offset + $imported

		);

	}


	public static function import_pincode() {

	global $wpdb;

	$table = $wpdb->prefix . 'myjat_locations';

	$file = __DIR__ . '/raw/pincode-village.csv';

	if ( ! file_exists( $file ) ) {
		return array(
			'done' => true,
			'updated' => 0
		);
	}

	$offset = (int) get_option( 'myjat_pincode_offset', 0 );

	$handle = fopen( $file, 'r' );

	if ( ! $handle ) {
		return array(
			'done' => true,
			'updated' => 0
		);
	}

	/* Skip Header */

	fgetcsv( $handle );

	$current = 0;
	$updated = 0;

	while ( ( $row = fgetcsv( $handle ) ) !== false ) {

		$current++;

		if ( $current <= $offset ) {
			continue;
		}

		$wpdb->query(

			$wpdb->prepare(

				"UPDATE {$table}

				SET

					state_code=%d,
					district_code=%d,
					block_code=%d,
					village_code=%d,
					pincode=%s

				WHERE

					state_name=%s
				AND district_name=%s
				AND block_name=%s
				AND village_name=%s",

				$row[1], // State Code
				$row[3], // District Code
				$row[5], // Block Code
				$row[7], // Village Code
				$row[9], // Pincode

				$row[2], // State Name
				$row[4], // District Name
				$row[6], // Block Name
				$row[8]  // Village Name

			)

		);

		$updated++;

		if ( $updated >= self::BATCH_SIZE ) {
			break;
		}

	}

	fclose( $handle );

	update_option(
		'myjat_pincode_offset',
		$offset + $updated
	);

if ( $updated < self::BATCH_SIZE ) {

	update_option(
		'myjat_pincode_finished',
		1
	);

	return array(
		'done' => true,
		'updated' => $updated,
		'offset' => $offset + $updated
	);

}
	return array(

		'done' => false,

		'updated' => $updated,

		'offset' => $offset + $updated

	);

}
}