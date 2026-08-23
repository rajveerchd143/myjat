<?php

if (!defined('MYJAT_SYSTEM')) {
    define('MYJAT_SYSTEM', true);
}

class MyJat_Location_Service
{
    private static function table()
    {
        return 'myjat_locations';
    }


    public static function get_states($pdo)
    {
        $table = self::table();

        $stmt = $pdo->query(
            "SELECT DISTINCT state_name
             FROM {$table}
             WHERE state_name IS NOT NULL
             AND state_name <> ''
             ORDER BY state_name ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public static function get_districts($pdo, $state)
    {
        $table = self::table();

        $stmt = $pdo->prepare(
            "SELECT DISTINCT district_name
             FROM {$table}
             WHERE state_name = ?
             AND district_name IS NOT NULL
             AND district_name <> ''
             ORDER BY district_name ASC"
        );

        $stmt->execute([
            $state
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public static function get_blocks($pdo, $district)
    {
        $table = self::table();

        $stmt = $pdo->prepare(
            "SELECT DISTINCT block_name
             FROM {$table}
             WHERE district_name = ?
             AND block_name IS NOT NULL
             AND block_name <> ''
             ORDER BY block_name ASC"
        );

        $stmt->execute([
            $district
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public static function get_villages(
        $pdo,
        $state,
        $district,
        $block
    ) {
        $table = self::table();

        $stmt = $pdo->prepare(
            "SELECT DISTINCT village_name
             FROM {$table}
             WHERE state_name = ?
             AND district_name = ?
             AND block_name = ?
             AND village_name IS NOT NULL
             AND village_name <> ''
             ORDER BY village_name ASC"
        );

        $stmt->execute([
            $state,
            $district,
            $block
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public static function get_pincode(
        $pdo,
        $state,
        $district,
        $block,
        $village
    ) {
        $table = self::table();

        $stmt = $pdo->prepare(
            "SELECT pincode
             FROM {$table}
             WHERE state_name = ?
             AND district_name = ?
             AND block_name = ?
             AND village_name = ?
             LIMIT 1"
        );

        $stmt->execute([
            $state,
            $district,
            $block,
            $village
        ]);

        return $stmt->fetchColumn();
    }
}