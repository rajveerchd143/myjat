<?php

require_once __DIR__ . '/class-location-service.php';

header('Content-Type: application/json; charset=UTF-8');


function myjat_location_ajax_response($success, $data = [])
{
    echo json_encode(
        [
            'success' => $success,
            'data'    => $data
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


$type = $_POST['type'] ?? '';

$type = trim($type);


$pdo = $GLOBALS['pdo'] ?? null;

if (!$pdo instanceof PDO) {

    myjat_location_ajax_response(
        false,
        [
            'message' => 'Database connection unavailable.'
        ]
    );

}


$parent = $_POST['parent'] ?? '';

$parent = trim($parent);


switch ($type) {

    case 'district':

        $data =
            MyJat_Location_Service::get_districts(
                $pdo,
                $parent
            );

        break;


    case 'block':

        $data =
            MyJat_Location_Service::get_blocks(
                $pdo,
                $parent
            );

        break;


    case 'village':

        $state =
            trim($_POST['state'] ?? '');

        $district =
            trim($_POST['district'] ?? '');

        $block =
            trim($_POST['block'] ?? '');

        $data =
            MyJat_Location_Service::get_villages(
                $pdo,
                $state,
                $district,
                $block
            );

        break;


    case 'pincode':

        $state =
            trim($_POST['state'] ?? '');

        $district =
            trim($_POST['district'] ?? '');

        $block =
            trim($_POST['block'] ?? '');

        $village =
            trim($_POST['village'] ?? '');

        $data =
            MyJat_Location_Service::get_pincode(
                $pdo,
                $state,
                $district,
                $block,
                $village
            );

        break;


    default:

        myjat_location_ajax_response(
            false,
            [
                'message' => 'Invalid location request.'
            ]
        );
}


myjat_location_ajax_response(
    true,
    $data
);