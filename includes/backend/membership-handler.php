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


    // =====================================================
    // Google User Details
    // =====================================================

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
    // Membership Type
    // =====================================================

    // =====================================================
    // Membership Plan
    // =====================================================

    $membership_plan_id =
        (int) ($_POST['membership_plan_id'] ?? 0);

    if ($membership_plan_id <= 0) {

        $_SESSION['myjat_membership_flash'] = [

            'type' => 'error',

            'message' =>
            'कृपया सही सदस्यता योजना चुनें।'
        ];

        header('Location: /register/');
        exit;
    }


    $stmt = $pdo->prepare(
        "SELECT
        id,
        name,
        slug,
        amount,
        currency,
        validity_type,
        validity_value,
        min_age,
        max_age,
        requires_ordinary_membership,
        requires_sankalp
     FROM membership_plans
     WHERE id = ?
       AND is_active = 1
     LIMIT 1"
    );

    $stmt->execute([
        $membership_plan_id
    ]);

    $membership_plan =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$membership_plan) {

        $_SESSION['myjat_membership_flash'] = [

            'type' => 'error',

            'message' =>
            'चयनित सदस्यता योजना उपलब्ध नहीं है।'
        ];

        header('Location: /register/');
        exit;
    }



    // =====================================================
    // Membership Type
    // =====================================================

    $membership_type =
        trim(
            (string) $membership_plan['name']
        );
    // ===============================================  ======
    // Ordinary Membership Requirement
    // =====================================================

    if (
        (int) $membership_plan['requires_ordinary_membership'] === 1
    ) {

        $ordinary_conditions = [];
        $ordinary_params = [];

        /*
     * Build only the comparisons that actually have a value.
     * This avoids comparing bound strings with '' and prevents
     * MySQL collation conflicts on live hosting.
     */

        if ($google_id !== '') {

            $ordinary_conditions[] =
                "ma.google_id = ?";

            $ordinary_params[] =
                $google_id;
        }

        if ($email !== '') {

            $ordinary_conditions[] =
                "LOWER(ma.email) = LOWER(?)";

            $ordinary_params[] =
                $email;
        }

        if ($google_email !== '') {

            $ordinary_conditions[] =
                "LOWER(ma.google_email) = LOWER(?)";

            $ordinary_params[] =
                $google_email;
        }


        $has_ordinary_membership = false;


        if (!empty($ordinary_conditions)) {

            $ordinary_sql =
                "SELECT ma.id
             FROM membership_applications ma
             INNER JOIN membership_plans mp
                 ON mp.id = ma.membership_plan_id
             WHERE mp.slug = 'ordinary'
               AND LOWER(ma.status) = 'approved'
               AND (
                    " .
                implode(
                    " OR ",
                    $ordinary_conditions
                )
                . "
               )
             LIMIT 1";


            $stmt =
                $pdo->prepare(
                    $ordinary_sql
                );


            $stmt->execute(
                $ordinary_params
            );


            $has_ordinary_membership =
                (bool) $stmt->fetchColumn();
        }


        if (!$has_ordinary_membership) {

            $_SESSION['myjat_membership_flash'] = [

                'type' => 'error',

                'message' =>
                'सक्रिय सदस्यता के लिए पहले साधारण सदस्य होना आवश्यक है।'
            ];

            header('Location: /register/');
            exit;
        }
    }
    // =====================================================
    // Duplicate Application Check
    //
    // IMPORTANT:
    // Only already-created applications are checked here.
    // Temporary drafts are NOT applications.
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
    //
    // IMPORTANT:
    // This data is NOT inserted into membership_applications.
    // It remains a temporary server-side draft until payment
    // is successfully verified.
    // =====================================================

    // =====================================================
    // Membership Type & Amount
    // =====================================================

    $membership_type =
        trim(
            (string) $membership_plan['name']
        );


    $amount_inr =
        (float) $membership_plan['amount'];


    $amount_paise =
        (int) round(
            $amount_inr * 100
        );


    if (
        $amount_paise <= 0
        || strtoupper(
            (string) $membership_plan['currency']
        ) !== 'INR'
    ) {

        $_SESSION['myjat_membership_flash'] = [

            'type' => 'error',

            'message' =>
            'सदस्यता शुल्क की जानकारी अमान्य है।'
        ];

        header('Location: /register/');
        exit;
    }


    $draft = [

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
        $membership_type,

        'membership_plan_id' =>
        $membership_plan_id,

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
    // Temporary Member Photo
    // =====================================================

    $temp_photo =
        myjat_upload_member_photo_temp();



    if (!$temp_photo) {

        $_SESSION['myjat_membership_flash'] = [

            'type' => 'error',

            'message' =>
            'कृपया सही फोटो अपलोड करें।'
        ];

        header('Location: /register/');
        exit;
    }

    $draft['temp_photo'] =
        $temp_photo;
    // =====================================================
    // Temporary Draft
    // =====================================================

    $_SESSION['myjat_membership_draft'] = $draft;


    // =====================================================
    // Razorpay Service
    // =====================================================

    require_once
        MYJAT_ROOT . '/includes/services/payment/razorpay.php';


    // =====================================================
    // Razorpay Receipt
    // =====================================================

    $receipt =
        'MYJAT-' .
        date('YmdHis') .
        '-' .
        bin2hex(random_bytes(4));


    // =====================================================
    // Create Razorpay Order
    // =====================================================

    $order =
        myjat_razorpay_create_order(
            $amount_paise,
            $receipt,
            [
                'membership_type' =>
                $membership_type,

                'membership_plan_id' =>
                $membership_plan_id,

                'email' =>
                $email,

                'mobile' =>
                $draft['mobile_no']
            ]
        );




    if (
        empty($order['success'])
        || empty($order['order_id'])
    ) {

        $_SESSION['myjat_membership_flash'] = [

            'type' => 'error',

            'message' =>
            'Payment order create नहीं हो सका। कृपया पुनः प्रयास करें।'
        ];

        header('Location: /register/');
        exit;
    }


    // =====================================================
    // Save Payment Attempt
    //
    // application_id intentionally NULL.
    //
    // Application does NOT exist yet.
    // =====================================================

    $stmt = $pdo->prepare(
        "INSERT INTO membership_payments
(
    application_id,
    membership_plan_id,
    membership_type,
    amount,
    currency,
    payment_status,
    razorpay_order_id,
    created_at,
    updated_at
)
        VALUES
(
    NULL,
    ?,
    ?,
    ?,
    'INR',
    'created',
    ?,
    NOW(),
    NOW()
)
        
        "
    );


    $saved =
        $stmt->execute([
            $membership_plan_id,
            $membership_type,
            $amount_inr,
            $order['order_id']
        ]);


    if (!$saved) {

        $_SESSION['myjat_membership_flash'] = [

            'type' => 'error',

            'message' =>
            'Payment transaction save नहीं हो सका। कृपया पुनः प्रयास करें।'
        ];

        header('Location: /register/');
        exit;
    }


    // =====================================================
    // Store Checkout Data in Session
    // =====================================================

    $_SESSION['myjat_membership_payment'] = [

        'order_id' =>
        $order['order_id'],

        'amount' =>
        $order['amount'],

        'currency' =>
        $order['currency'],

        'membership_type' =>
        $membership_type,

        'membership_plan_id' =>
        $membership_plan_id,

        'amount_inr' =>
        $amount_inr
    ];

    // =====================================================
    // Redirect Back to Registration
    //
    // Next step will open Razorpay Checkout using the
    // session payment data.
    // =====================================================

    header(
        'Location: /register/'
    );

    exit;
}


// =========================================================
// MEMBERSHIP PAYMENT VERIFICATION
// =========================================================


function myjat_membership_payment_verify()
{
    global $pdo;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);

        exit;
    }


    // =====================================================
    // Session
    // =====================================================

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    header('Content-Type: application/json');


    // =====================================================
    // Logged-in User
    // =====================================================

    $current_user =
        $_SESSION['myjat_user'] ?? null;

    if (!$current_user) {

        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'कृपया पहले login करें।'
        ]);

        exit;
    }


    // =====================================================
    // Payment Session
    // =====================================================

    $payment_session =
        $_SESSION['myjat_membership_payment']
        ?? null;

    $draft =
        $_SESSION['myjat_membership_draft']
        ?? null;


    if (
        !is_array($payment_session)
        || !is_array($draft)
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment session समाप्त हो गया है। कृपया पुनः प्रयास करें।'
        ]);

        exit;
    }


    // =====================================================
    // Read JSON Request
    // =====================================================

    $raw_input =
        file_get_contents('php://input');

    $request =
        json_decode(
            $raw_input,
            true
        );


    if (!is_array($request)) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid payment response.'
        ]);

        exit;
    }


    $razorpay_payment_id =
        trim(
            (string) (
                $request['razorpay_payment_id']
                ?? ''
            )
        );

    $razorpay_order_id =
        trim(
            (string) (
                $request['razorpay_order_id']
                ?? ''
            )
        );

    $razorpay_signature =
        trim(
            (string) (
                $request['razorpay_signature']
                ?? ''
            )
        );


    if (
        $razorpay_payment_id === ''
        || $razorpay_order_id === ''
        || $razorpay_signature === ''
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment response अधूरा है।'
        ]);

        exit;
    }


    // =====================================================
    // Order ID Must Match Session
    // =====================================================

    if (
        !hash_equals(
            (string) $payment_session['order_id'],
            $razorpay_order_id
        )
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment order match नहीं हुआ।'
        ]);

        exit;
    }


    // =====================================================
    // Payment Transaction
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT
    id,
    application_id,
    membership_plan_id,
    membership_type,
    amount,
    currency,
    payment_status,
    razorpay_order_id
         FROM membership_payments
         WHERE razorpay_order_id = ?
         LIMIT 1"
    );

    $stmt->execute([
        $razorpay_order_id
    ]);


    $payment =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$payment) {

        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment transaction नहीं मिली।'
        ]);

        exit;
    }


    // =====================================================
    // Already Processed
    // =====================================================

    if ($payment['payment_status'] === 'paid') {

        /*
     * Payment has already been processed.
     * Never create another application for the same order.
     */

        if (!empty($payment['application_id'])) {


            echo json_encode([
                'success' => true,
                'redirect' => '/register/'
            ]);

            exit;
        }


        /*
     * A paid payment without an application is an
     * inconsistent state. Do not silently create another
     * transaction.
     */

        throw new RuntimeException(
            'Payment is already marked paid but application is missing.'
        );
    }




    if (
        (int) $payment['membership_plan_id']
        !==
        (int) $payment_session['membership_plan_id']
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Membership plan match नहीं हुआ।'
        ]);

        exit;
    }

    // =====================================================
    // Amount / Membership Type Verification
    // =====================================================

    if (
        (string) $payment['membership_type']
        !==
        (string) $draft['membership_type']
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Membership type match नहीं हुआ।'
        ]);

        exit;
    }


    $session_amount_paise =
        (int) (
            $payment_session['amount']
            ?? 0
        );

    $database_amount_paise =
        (int) round(
            ((float) $payment['amount']) * 100
        );


    if (
        $session_amount_paise <= 0
        || $database_amount_paise
        !==
        $session_amount_paise
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment amount match नहीं हुआ।'
        ]);

        exit;
    }


    // =====================================================
    // Razorpay Signature Verification
    // =====================================================

    require_once
        MYJAT_ROOT .
        '/includes/services/payment/razorpay.php';


    $signature_valid =
        myjat_razorpay_verify_signature(
            $razorpay_order_id,
            $razorpay_payment_id,
            $razorpay_signature
        );


    if (!$signature_valid) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment verification असफल हुआ।'
        ]);

        exit;
    }


    // =====================================================
    // Razorpay Payment Status Verification
    // =====================================================

    $payment_status_result =
        myjat_razorpay_fetch_payment(
            $razorpay_payment_id
        );


    if (
        empty($payment_status_result['success'])
    ) {

        http_response_code(502);

        echo json_encode([
            'success' => false,
            'message' =>
            'Razorpay payment status verify नहीं हो सका।'
        ]);

        exit;
    }


    $razorpay_payment =
        $payment_status_result['payment']
        ?? null;


    if (!is_array($razorpay_payment)) {

        http_response_code(502);

        echo json_encode([
            'success' => false,
            'message' =>
            'Razorpay payment details नहीं मिलीं।'
        ]);

        exit;
    }


    // =====================================================
    // Payment ID / Order ID Verification
    // =====================================================

    if (
        ($razorpay_payment['id'] ?? '')
        !==
        $razorpay_payment_id
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment ID match नहीं हुआ।'
        ]);

        exit;
    }


    if (
        ($razorpay_payment['order_id'] ?? '')
        !==
        $razorpay_order_id
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment order ID match नहीं हुआ।'
        ]);

        exit;
    }


    // =====================================================
    // Amount Verification
    // =====================================================

    if (
        (int) ($razorpay_payment['amount'] ?? 0)
        !==
        $session_amount_paise
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Razorpay payment amount match नहीं हुआ।'
        ]);

        exit;
    }


    // =====================================================
    // Currency Verification
    // =====================================================

    if (
        strtoupper(
            (string) (
                $razorpay_payment['currency']
                ?? ''
            )
        )
        !== 'INR'
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment currency invalid है।'
        ]);

        exit;
    }


    // =====================================================
    // Captured Payment Required
    // =====================================================

    if (
        ($razorpay_payment['status'] ?? '')
        !== 'captured'
    ) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' =>
            'Payment अभी captured नहीं हुआ है।'
        ]);

        exit;
    }


    // =====================================================
    // Transaction
    // =====================================================

    try {

        $pdo->beginTransaction();


        // =================================================
        // Re-check Payment Row
        // =================================================

        $stmt = $pdo->prepare(
            "SELECT
                id,
                application_id,
                 membership_plan_id,
                membership_type,
                amount,
                currency,
                payment_status
             FROM membership_payments
             WHERE razorpay_order_id = ?
             LIMIT 1
             FOR UPDATE"
        );

        $stmt->execute([
            $razorpay_order_id
        ]);


        $payment =
            $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$payment) {

            throw new RuntimeException(
                'Payment transaction not found.'
            );
        }


        if (
            $payment['payment_status'] === 'paid'
            && !empty($payment['application_id'])
        ) {

            $pdo->commit();

            echo json_encode([
                'success' => true,
                'redirect' => '/register/'
            ]);

            exit;
        }


        // =================================================
        // Generate Membership Number
        // =================================================

        $membership_no =
            myjat_generate_membership_number(
                $pdo
            );


        // =================================================
        // Application Data
        // =================================================

        $application_data = $draft;

        $application_data['membership_plan_id'] =
            (int) $payment_session['membership_plan_id'];

        $application_data['membership_no'] =
            $membership_no;

        $application_data['status'] =
            'Pending';

        $application_data['application_date'] =
            date('Y-m-d');

        $application_data['created_at'] =
            date('Y-m-d H:i:s');


        // =================================================
        // Photo Validation
        // =================================================

        $temp_photo =
            $draft['temp_photo']
            ?? '';


        if (
            $temp_photo === ''
            || !is_file($temp_photo)
        ) {

            throw new RuntimeException(
                'Temporary member photo not found.'
            );
        }


        unset(
            $application_data['temp_photo']
        );

        // =================================================
        // Save Application
        // =================================================

        $saved =
            myjat_save_membership_application(
                $pdo,
                $application_data
            );


        if (!$saved) {

            throw new RuntimeException(
                'Membership application could not be saved.'
            );
        }


        // =================================================
        // Move Photo After Application Creation
        // =================================================

        $final_photo =
            myjat_move_temp_member_photo(
                $temp_photo
            );


        if (!$final_photo) {

            throw new RuntimeException(
                'Member photo could not be finalized.'
            );
        }


        // =================================================
        // Update Application Photo URL
        // =================================================

        $stmt = $pdo->prepare(
            "UPDATE membership_applications
     SET photo_url = ?
     WHERE membership_no = ?
     LIMIT 1"
        );


        $stmt->execute([
            $final_photo,
            $membership_no
        ]);

        // =================================================
        // Find New Application ID
        // =================================================

        $stmt = $pdo->prepare(
            "SELECT id
             FROM membership_applications
             WHERE membership_no = ?
             LIMIT 1"
        );

        $stmt->execute([
            $membership_no
        ]);


        $application_id =
            $stmt->fetchColumn();


        if (!$application_id) {

            throw new RuntimeException(
                'Application ID could not be resolved.'
            );
        }


        // =================================================
        // Mark Payment Paid
        // =================================================

        $stmt = $pdo->prepare(
            "UPDATE membership_payments
             SET
                application_id = ?,
                payment_status = 'paid',
                razorpay_payment_id = ?,
                razorpay_signature = ?,
                payment_method = 'razorpay',
                payment_date = NOW(),
                updated_at = NOW()
             WHERE id = ?"
        );


        $stmt->execute([
            $application_id,
            $razorpay_payment_id,
            $razorpay_signature,
            $payment['id']
        ]);


        // =================================================
        // Clear Temporary Session
        // =================================================

        unset(
            $_SESSION['myjat_membership_draft'],
            $_SESSION['myjat_membership_payment']
        );


        // =================================================
        // Success Flash
        // =================================================

        $_SESSION['myjat_membership_flash'] = [

            'type' =>
            'success',

            'member_no' =>
            $membership_no
        ];


        $pdo->commit();


        // =================================================
        // Success Response
        // =================================================

        echo json_encode([

            'success' =>
            true,

            'redirect' =>
            '/register/'
        ]);

        exit;
    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        http_response_code(500);


        echo json_encode([

            'success' =>
            false,

            'message' =>
            'Payment verify होने के बाद application save नहीं हो सका।'
        ]);

        exit;
    }
}



// =========================================================
// RAZORPAY PAYMENT WEBHOOK
// =========================================================

function myjat_membership_payment_webhook()
{
    global $pdo;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);

        exit;
    }


    // =====================================================
    // Webhook Secret
    // =====================================================

    if (
        !defined('MYJAT_RAZORPAY_WEBHOOK_SECRET')
        || MYJAT_RAZORPAY_WEBHOOK_SECRET === ''
    ) {

        http_response_code(500);

        exit;
    }


    // =====================================================
    // Raw Request Body
    // =====================================================

    $raw_body =
        file_get_contents('php://input');


    if ($raw_body === false || $raw_body === '') {

        http_response_code(400);

        exit;
    }


    // =====================================================
    // Razorpay Webhook Signature
    // =====================================================

    $received_signature =
        trim(
            (string) (
                $_SERVER['HTTP_X_RAZORPAY_SIGNATURE']
                ?? ''
            )
        );


    if ($received_signature === '') {

        http_response_code(400);

        exit;
    }


    $expected_signature =
        hash_hmac(
            'sha256',
            $raw_body,
            MYJAT_RAZORPAY_WEBHOOK_SECRET
        );


    if (
        !hash_equals(
            $expected_signature,
            $received_signature
        )
    ) {

        http_response_code(400);

        exit;
    }


    // =====================================================
    // Decode Event
    // =====================================================

    $payload =
        json_decode(
            $raw_body,
            true
        );


    if (!is_array($payload)) {

        http_response_code(400);

        exit;
    }


    $event =
        (string) (
            $payload['event']
            ?? ''
        );


    // =====================================================
    // Payment Entity
    // =====================================================

    $payment_entity =
        $payload['payload']['payment']['entity']
        ?? null;


    if (!is_array($payment_entity)) {

        http_response_code(200);

        exit;
    }


    $razorpay_payment_id =
        trim(
            (string) (
                $payment_entity['id']
                ?? ''
            )
        );


    $razorpay_order_id =
        trim(
            (string) (
                $payment_entity['order_id']
                ?? ''
            )
        );


    if (
        $razorpay_payment_id === ''
        || $razorpay_order_id === ''
    ) {

        http_response_code(200);

        exit;
    }


    // =====================================================
    // Find Transaction
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT
            id,
            application_id,
            payment_status
         FROM membership_payments
         WHERE razorpay_order_id = ?
         LIMIT 1"
    );


    $stmt->execute([
        $razorpay_order_id
    ]);


    $payment =
        $stmt->fetch(PDO::FETCH_ASSOC);


    /*
     * If this order does not belong to MYJAT,
     * acknowledge the webhook without modifying anything.
     */

    if (!$payment) {

        http_response_code(200);

        exit;
    }


    // =====================================================
    // PAYMENT CAPTURED
    // =====================================================

    if ($event === 'payment.captured') {

        /*
         * Do not overwrite an existing application/payment.
         */

        if (
            $payment['payment_status'] === 'paid'
        ) {

            http_response_code(200);

            exit;
        }


        $stmt = $pdo->prepare(
            "UPDATE membership_payments
             SET
                payment_status = 'paid',
                razorpay_payment_id = ?,
                payment_method = 'razorpay',
                payment_date = NOW(),
                updated_at = NOW()
             WHERE id = ?
               AND payment_status <> 'paid'"
        );


        $stmt->execute([
            $razorpay_payment_id,
            $payment['id']
        ]);


        http_response_code(200);

        exit;
    }


    // =====================================================
    // PAYMENT FAILED
    // =====================================================

    if ($event === 'payment.failed') {

        /*
         * Never turn a paid transaction back into failed.
         */

        if (
            $payment['payment_status'] === 'paid'
        ) {

            http_response_code(200);

            exit;
        }


        $stmt = $pdo->prepare(
            "UPDATE membership_payments
             SET
                payment_status = 'failed',
                razorpay_payment_id = ?,
                payment_method = 'razorpay',
                updated_at = NOW()
             WHERE id = ?
               AND payment_status <> 'paid'"
        );


        $stmt->execute([
            $razorpay_payment_id,
            $payment['id']
        ]);


        http_response_code(200);

        exit;
    }


    // =====================================================
    // Other Events
    // =====================================================

    http_response_code(200);

    exit;
}
