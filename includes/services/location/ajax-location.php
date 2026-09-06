<?php

declare(strict_types=1);


// =========================================================
// MYJAT LOCATION AJAX
// =========================================================

require_once dirname(__DIR__, 3) . '/vip/bootstrap.php';

require_once __DIR__ . '/class-location-service.php';


// =========================================================
// JSON Response
// =========================================================

header(
    'Content-Type: application/json; charset=UTF-8'
);


function myjat_location_ajax_response(
    bool $success,
    $data = []
): void {

    echo json_encode(
        [
            'success' => $success,
            'data'    => $data
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;
}


// =========================================================
// Database
// =========================================================

global $pdo;

if (!$pdo instanceof PDO) {

    myjat_location_ajax_response(
        false,
        [
            'message' =>
                'Database connection unavailable.'
        ]
    );

}


// =========================================================
// Request
// =========================================================

$type =
    trim(
        (string) (
            $_POST['type']
            ?? ''
        )
    );


$parent =
    trim(
        (string) (
            $_POST['parent']
            ?? ''
        )
    );


// =========================================================
// Location
// =========================================================

switch ($type) {


    // -----------------------------------------------------
    // STATE → DISTRICT
    // -----------------------------------------------------

    case 'district':

        if ($parent === '') {

            myjat_location_ajax_response(
                false,
                []
            );

        }

        $data =
            MyJat_Location_Service::get_districts(
                $pdo,
                $parent
            );

        break;


    // -----------------------------------------------------
    // DISTRICT → BLOCK
    // -----------------------------------------------------

    case 'block':

        if ($parent === '') {

            myjat_location_ajax_response(
                false,
                []
            );

        }

        $data =
            MyJat_Location_Service::get_blocks(
                $pdo,
                $parent
            );

        break;


    // -----------------------------------------------------
    // BLOCK → VILLAGE
    // -----------------------------------------------------

    case 'village':

        $state =
            trim(
                (string) (
                    $_POST['state']
                    ?? ''
                )
            );

        $district =
            trim(
                (string) (
                    $_POST['district']
                    ?? ''
                )
            );

        $block =
            trim(
                (string) (
                    $_POST['block']
                    ?? ''
                )
            );


        if (
            $state === ''
            || $district === ''
            || $block === ''
        ) {

            myjat_location_ajax_response(
                false,
                []
            );

        }


        $data =
            MyJat_Location_Service::get_villages(
                $pdo,
                $state,
                $district,
                $block
            );

        break;


    // -----------------------------------------------------
    // VILLAGE → PINCODE
    // -----------------------------------------------------

    case 'pincode':

        $state =
            trim(
                (string) (
                    $_POST['state']
                    ?? ''
                )
            );

        $district =
            trim(
                (string) (
                    $_POST['district']
                    ?? ''
                )
            );

        $block =
            trim(
                (string) (
                    $_POST['block']
                    ?? ''
                )
            );

        $village =
            trim(
                (string) (
                    $_POST['village']
                    ?? ''
                )
            );


        if (
            $state === ''
            || $district === ''
            || $block === ''
            || $village === ''
        ) {

            myjat_location_ajax_response(
                false,
                []
            );

        }


        $data =
            MyJat_Location_Service::get_pincode(
                $pdo,
                $state,
                $district,
                $block,
                $village
            );

        break;


    // -----------------------------------------------------
    // INVALID REQUEST
    // -----------------------------------------------------

    default:

        myjat_location_ajax_response(
            false,
            [
                'message' =>
                    'Invalid location request.'
            ]
        );
}


// =========================================================
// Response
// =========================================================

myjat_location_ajax_response(
    true,
    $data
);