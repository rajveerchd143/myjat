<?php

// =========================================================
// Membership Handler
// Standalone PHP Version
// =========================================================

function myjat_handle_membership_form_submission($pdo)
{
    if (empty($_POST['myjat_membership_submit'])) {
        return;
    }

    $data = [

        'full_name' => trim($_POST['full_name'] ?? ''),
        'father_husband_name' => trim($_POST['father_husband_name'] ?? ''),
        'mobile_no' => trim($_POST['mobile_no'] ?? ''),
        'phone_no' => trim($_POST['phone_no'] ?? ''),

        'email' => filter_var(
            $_POST['email'] ?? '',
            FILTER_SANITIZE_EMAIL
        ),

        'google_email' => '',
        'google_id' => '',
        'user_id' => 0,

        'dob' => trim($_POST['dob'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'education' => trim($_POST['education'] ?? ''),
        'profession' => trim($_POST['profession'] ?? ''),
        'membership_type' => trim($_POST['membership_type'] ?? ''),

        'current_address' => trim($_POST['current_address'] ?? ''),
        'permanent_address' => trim($_POST['permanent_address'] ?? ''),

        'village_panchayat' => trim($_POST['village_panchayat'] ?? ''),
        'block_name' => trim($_POST['block_name'] ?? ''),
        'vidhansabha' => trim($_POST['vidhansabha'] ?? ''),
        'loksabha' => trim($_POST['loksabha'] ?? ''),
        'district' => trim($_POST['district'] ?? ''),
        'state_name' => trim($_POST['state_name'] ?? ''),
        'pincode' => trim($_POST['pincode'] ?? ''),

        'aadhaar_no' => trim($_POST['aadhaar_no'] ?? ''),
        'family_details' => trim($_POST['family_details'] ?? ''),

        'recommender_name' => trim($_POST['recommender_name'] ?? ''),
        'recommender_membership_no' => trim(
            $_POST['recommender_membership_no'] ?? ''
        ),
        'recommender_mobile' => trim(
            $_POST['recommender_mobile'] ?? ''
        )
    ];


    // =====================================================
    // Duplicate Application Check
    // =====================================================

    if (!empty($data['user_id'])) {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM membership_applications
             WHERE user_id = ?
             LIMIT 1"
        );

        $stmt->execute([
            (int) $data['user_id']
        ]);

        if ($stmt->fetchColumn()) {

            header(
                'Location: ' .
                ($_SERVER['HTTP_REFERER'] ?? '/')
                . '?application=exists'
            );

            exit;
        }
    }


    // =====================================================
    // Membership Number
    // =====================================================

    $data['membership_no'] =
        myjat_generate_membership_number($pdo);


    // =====================================================
    // Application Status
    // =====================================================

    $data['status'] = 'pending';

    $data['application_date'] = date('Y-m-d');

    $data['created_at'] = date('Y-m-d H:i:s');


    // =====================================================
    // Member Photo
    // =====================================================

    $data['photo_url'] =
        myjat_upload_member_photo();


    // =====================================================
    // Save Application
    // =====================================================

    $id = myjat_save_membership_application(
        $pdo,
        $data
    );


    // =====================================================
    // Redirect To Success
    // =====================================================

    $redirect_url =
        ($_SERVER['HTTP_REFERER'] ?? '/')
        . '?membership=submitted'
        . '&member_no='
        . urlencode($data['membership_no']);

    header(
        'Location: ' . $redirect_url
    );

    exit;
}