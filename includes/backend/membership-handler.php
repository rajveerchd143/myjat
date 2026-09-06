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


    // =====================================================
    // Session
    // =====================================================

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    // =====================================================
    // Logged-in User
    // =====================================================

    $current_user =
        $_SESSION['myjat_user'] ?? null;

    if (!$current_user) {

        header('Location: /login/');
        exit;
    }


    $google_email =
        trim(
            (string) (
                $current_user['email']
                ?? $current_user['user_email']
                ?? ''
            )
        );

    $google_id =
        trim(
            (string) (
                $current_user['google_id']
                ?? ''
            )
        );


    // =====================================================
    // Email
    // =====================================================

    $email =
        filter_var(
            $_POST['email'] ?? '',
            FILTER_SANITIZE_EMAIL
        );

    if ($email === '' && $google_email !== '') {
        $email = $google_email;
    }


    // =====================================================
    // Duplicate Application Check
    // =====================================================

    if ($email !== '') {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM membership_applications
             WHERE LOWER(email) = LOWER(?)
                OR LOWER(google_email) = LOWER(?)
             LIMIT 1"
        );

        $stmt->execute([
            $email,
            $email
        ]);

        if ($stmt->fetchColumn()) {

            $_SESSION['myjat_membership_flash'] = [
                'type' => 'exists'
            ];

            header('Location: /register/');
            exit;
        }
    }


    // =====================================================
    // Collect Form Data
    // =====================================================

    $data = [

        'full_name' =>
            trim($_POST['full_name'] ?? ''),

        'father_husband_name' =>
            trim($_POST['father_husband_name'] ?? ''),

        'mobile_no' =>
            trim($_POST['mobile_no'] ?? ''),

        'phone_no' =>
            trim($_POST['phone_no'] ?? ''),

        'email' =>
            $email,

        'google_email' =>
            $google_email,

        'google_id' =>
            $google_id,

        'dob' =>
            trim($_POST['dob'] ?? ''),

        'gender' =>
            trim($_POST['gender'] ?? ''),

        'education' =>
            trim($_POST['education'] ?? ''),

        'profession' =>
            trim($_POST['profession'] ?? ''),

        'membership_type' =>
            trim($_POST['membership_type'] ?? ''),

        'current_address' =>
            trim($_POST['current_address'] ?? ''),

        'permanent_address' =>
            trim($_POST['permanent_address'] ?? ''),

        'village_panchayat' =>
            trim($_POST['village_panchayat'] ?? ''),

        'block_name' =>
            trim($_POST['block_name'] ?? ''),

        'vidhansabha' =>
            trim($_POST['vidhansabha'] ?? ''),

        'loksabha' =>
            trim($_POST['loksabha'] ?? ''),

        'district' =>
            trim($_POST['district'] ?? ''),

        'state_name' =>
            trim($_POST['state_name'] ?? ''),

        'pincode' =>
            trim($_POST['pincode'] ?? ''),

        'aadhaar_no' =>
            trim($_POST['aadhaar_no'] ?? ''),

        'family_details' =>
            trim($_POST['family_details'] ?? ''),

        'recommender_name' =>
            trim($_POST['recommender_name'] ?? ''),

        'recommender_membership_no' =>
            trim(
                $_POST['recommender_membership_no'] ?? ''
            ),

        'recommender_mobile' =>
            trim(
                $_POST['recommender_mobile'] ?? ''
            )
    ];


    // =====================================================
    // Membership Number
    // =====================================================

    $data['membership_no'] =
        myjat_generate_membership_number($pdo);


    // =====================================================
    // Application Status
    // =====================================================

    $data['status'] =
        'Pending';

    $data['application_date'] =
        date('Y-m-d');

    $data['created_at'] =
        date('Y-m-d H:i:s');


    // =====================================================
    // Member Photo
    // =====================================================

    $data['photo_url'] =
        myjat_upload_member_photo();


    // =====================================================
    // Save Application
    // =====================================================

    $saved =
        myjat_save_membership_application(
            $pdo,
            $data
        );


    if (!$saved) {

        http_response_code(500);

        exit(
            'Membership application could not be saved.'
        );
    }


    // =====================================================
    // Success Flash
    // =====================================================

    $_SESSION['myjat_membership_flash'] = [

        'type' =>
            'success',

        'member_no' =>
            $data['membership_no']
    ];


    // =====================================================
    // Clean Redirect
    // =====================================================

    header(
        'Location: /register/'
    );

    exit;
}