<?php

// =========================================================
// MYJAT LOCATION IMPORTER
// Standalone PHP / PDO Version
// =========================================================

class MyJat_Location_Importer
{
    const BATCH_SIZE = 10000;


    // =====================================================
    // LGD VILLAGE IMPORT
    // =====================================================

    public static function import_lgd($pdo)
    {
        $table = 'myjat_locations';

        $file =
            __DIR__ . '/raw/lgd-villages.csv';


        // -------------------------------------------------
        // File Check
        // -------------------------------------------------

        if (!file_exists($file)) {

            return [
                'done'     => true,
                'imported' => 0,
                'offset'   => 0
            ];
        }


        // -------------------------------------------------
        // Get Current Offset
        // -------------------------------------------------

        $offset =
            self::get_import_offset(
                $pdo,
                'lgd'
            );


        // -------------------------------------------------
        // Open CSV
        // -------------------------------------------------

        $handle =
            fopen($file, 'r');


        if (!$handle) {

            return [
                'done'     => true,
                'imported' => 0,
                'offset'   => $offset
            ];
        }


        // -------------------------------------------------
        // Skip Header
        // -------------------------------------------------

        fgetcsv($handle);


        $current  = 0;
        $imported = 0;


        // -------------------------------------------------
        // Import
        // -------------------------------------------------

        while (
            ($row = fgetcsv($handle))
            !== false
        ) {

            $current++;


            if ($current <= $offset) {
                continue;
            }


            // Make sure required columns exist

            if (
                !isset($row[1]) ||
                !isset($row[3]) ||
                !isset($row[5]) ||
                !isset($row[7])
            ) {

                continue;
            }


            $stmt = $pdo->prepare(

                "INSERT INTO {$table}
                (
                    state_name,
                    district_name,
                    block_name,
                    village_name
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?
                )"

            );


            $stmt->execute([

                trim($row[1]),
                trim($row[3]),
                trim($row[5]),
                trim($row[7])

            ]);


            $imported++;


            if (
                $imported >=
                self::BATCH_SIZE
            ) {

                break;
            }
        }


        fclose($handle);


        // -------------------------------------------------
        // New Offset
        // -------------------------------------------------

        $new_offset =
            $offset + $imported;


        self::save_import_offset(
            $pdo,
            'lgd',
            $new_offset
        );


        return [

            'done' =>
                $imported < self::BATCH_SIZE,

            'imported' =>
                $imported,

            'offset' =>
                $new_offset

        ];
    }


    // =====================================================
    // PINCODE IMPORT
    // =====================================================

    public static function import_pincode($pdo)
    {
        $table =
            'myjat_locations';


        $file =
            __DIR__ . '/raw/pincode-village.csv';


        // -------------------------------------------------
        // File Check
        // -------------------------------------------------

        if (!file_exists($file)) {

            return [

                'done'    => true,
                'updated' => 0,
                'offset'  => 0

            ];
        }


        // -------------------------------------------------
        // Get Offset
        // -------------------------------------------------

        $offset =
            self::get_import_offset(
                $pdo,
                'pincode'
            );


        // -------------------------------------------------
        // Open CSV
        // -------------------------------------------------

        $handle =
            fopen($file, 'r');


        if (!$handle) {

            return [

                'done'    => true,
                'updated' => 0,
                'offset'  => $offset

            ];
        }


        // -------------------------------------------------
        // Skip Header
        // -------------------------------------------------

        fgetcsv($handle);


        $current = 0;
        $updated = 0;


        // -------------------------------------------------
        // Process CSV
        // -------------------------------------------------

        while (
            ($row = fgetcsv($handle))
            !== false
        ) {

            $current++;


            if ($current <= $offset) {
                continue;
            }


            // Required columns

            if (
                !isset($row[1]) ||
                !isset($row[2]) ||
                !isset($row[3]) ||
                !isset($row[4]) ||
                !isset($row[5]) ||
                !isset($row[6]) ||
                !isset($row[7]) ||
                !isset($row[8]) ||
                !isset($row[9])
            ) {

                continue;
            }


            // -------------------------------------------------
            // Update Location
            // -------------------------------------------------

            $stmt = $pdo->prepare(

                "UPDATE {$table}

                 SET

                    state_code = ?,
                    district_code = ?,
                    block_code = ?,
                    village_code = ?,
                    pincode = ?

                 WHERE

                    state_name = ?
                    AND district_name = ?
                    AND block_name = ?
                    AND village_name = ?"

            );


            $stmt->execute([

                $row[1], // State Code
                $row[3], // District Code
                $row[5], // Block Code
                $row[7], // Village Code
                $row[9], // Pincode

                trim($row[2]), // State Name
                trim($row[4]), // District Name
                trim($row[6]), // Block Name
                trim($row[8])  // Village Name

            ]);


            $updated++;


            if (
                $updated >=
                self::BATCH_SIZE
            ) {

                break;
            }
        }


        fclose($handle);


        // -------------------------------------------------
        // New Offset
        // -------------------------------------------------

        $new_offset =
            $offset + $updated;


        self::save_import_offset(
            $pdo,
            'pincode',
            $new_offset
        );


        // -------------------------------------------------
        // Finished
        // -------------------------------------------------

        if (
            $updated <
            self::BATCH_SIZE
        ) {

            self::save_import_finished(
                $pdo,
                'pincode'
            );


            return [

                'done'    => true,
                'updated' => $updated,
                'offset'  => $new_offset

            ];
        }


        return [

            'done'    => false,
            'updated' => $updated,
            'offset'  => $new_offset

        ];
    }


    // =====================================================
    // GET IMPORT OFFSET
    // =====================================================

    private static function get_import_offset(
        $pdo,
        $type
    ) {

        $key =
            'location_import_' . $type;


        $stmt = $pdo->prepare(

            "SELECT option_value
             FROM myjat_options
             WHERE option_name = ?
             LIMIT 1"

        );


        $stmt->execute([
            $key
        ]);


        $value =
            $stmt->fetchColumn();


        return
            $value !== false
                ? (int) $value
                : 0;
    }


    // =====================================================
    // SAVE IMPORT OFFSET
    // =====================================================

    private static function save_import_offset(
        $pdo,
        $type,
        $offset
    ) {

        $key =
            'location_import_' . $type;


        $stmt = $pdo->prepare(

            "INSERT INTO myjat_options
            (
                option_name,
                option_value
            )
            VALUES
            (
                ?,
                ?
            )
            ON DUPLICATE KEY UPDATE
                option_value = VALUES(option_value)"

        );


        $stmt->execute([

            $key,
            (string) $offset

        ]);
    }


    // =====================================================
    // MARK PINCODE IMPORT FINISHED
    // =====================================================

    private static function save_import_finished(
        $pdo,
        $type
    ) {

        $key =
            'location_import_' .
            $type .
            '_finished';


        $stmt = $pdo->prepare(

            "INSERT INTO myjat_options
            (
                option_name,
                option_value
            )
            VALUES
            (
                ?,
                '1'
            )
            ON DUPLICATE KEY UPDATE
                option_value = '1'"

        );


        $stmt->execute([
            $key
        ]);
    }
}