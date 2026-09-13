<?php

// =========================================================
// MYJAT Razorpay Service
// =========================================================

if (defined('MYJAT_RAZORPAY_SERVICE_LOADED')) {
    return;
}

define('MYJAT_RAZORPAY_SERVICE_LOADED', true);


// =========================================================
// Create Razorpay Order
// =========================================================

function myjat_razorpay_create_order(
    int $amount,
    string $receipt,
    array $notes = []
): array {

    if (
        !defined('MYJAT_RAZORPAY_KEY_ID') ||
        !defined('MYJAT_RAZORPAY_KEY_SECRET') ||
        MYJAT_RAZORPAY_KEY_ID === '' ||
        MYJAT_RAZORPAY_KEY_SECRET === ''
    ) {
        return [
            'success' => false,
            'message' => 'Razorpay configuration is missing.'
        ];
    }


    if ($amount <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid payment amount.'
        ];
    }


    $payload = [
        'amount'   => $amount,
        'currency' => 'INR',
        'receipt'  => $receipt,
    ];


    if (!empty($notes)) {
        $payload['notes'] = $notes;
    }


    $ch = curl_init(
        'https://api.razorpay.com/v1/orders'
    );


    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS =>
        json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE
        ),

        CURLOPT_USERPWD =>
        MYJAT_RAZORPAY_KEY_ID
            . ':'
            . MYJAT_RAZORPAY_KEY_SECRET,

        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,

        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],

        CURLOPT_TIMEOUT => 30,

    ]);


    $response = curl_exec($ch);

    $curl_error = curl_error($ch);

    $http_code =
        (int) curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

    curl_close($ch);


    if ($response === false) {

        return [
            'success' => false,
            'message' =>
            'Unable to connect to Razorpay.'
        ];
    }


    if ($curl_error !== '') {

        return [
            'success' => false,
            'message' =>
            'Razorpay connection failed.'
        ];
    }


    $result =
        json_decode(
            $response,
            true
        );


    if (
        $http_code < 200 ||
        $http_code >= 300 ||
        !is_array($result) ||
        empty($result['id'])
    ) {

        return [
            'success' => false,
            'message' =>
            'Razorpay order could not be created.'
        ];
    }


    return [
        'success' => true,

        'order_id' =>
        $result['id'],

        'amount' =>
        (int) ($result['amount'] ?? $amount),

        'currency' =>
        $result['currency'] ?? 'INR',

        'receipt' =>
        $result['receipt'] ?? $receipt,
    ];
}


// =========================================================
// Verify Razorpay Payment Signature
// =========================================================

function myjat_razorpay_verify_signature(
    string $order_id,
    string $payment_id,
    string $signature
): bool {

    if (
        $order_id === '' ||
        $payment_id === '' ||
        $signature === ''
    ) {
        return false;
    }


    if (
        !defined('MYJAT_RAZORPAY_KEY_SECRET') ||
        MYJAT_RAZORPAY_KEY_SECRET === ''
    ) {
        return false;
    }


    $generated_signature =
        hash_hmac(
            'sha256',
            $order_id . '|' . $payment_id,
            MYJAT_RAZORPAY_KEY_SECRET
        );


    return hash_equals(
        $generated_signature,
        $signature
    );
}



// =========================================================
// FETCH RAZORPAY PAYMENT
// =========================================================

function myjat_razorpay_fetch_payment($payment_id)
{
    $payment_id =
        trim((string) $payment_id);

    if ($payment_id === '') {
        return [
            'success' => false,
            'message' => 'Invalid payment ID.'
        ];
    }

    if (
        !defined('MYJAT_RAZORPAY_KEY_ID')
        || !defined('MYJAT_RAZORPAY_KEY_SECRET')
        || MYJAT_RAZORPAY_KEY_ID === ''
        || MYJAT_RAZORPAY_KEY_SECRET === ''
    ) {
        return [
            'success' => false,
            'message' => 'Razorpay credentials are not configured.'
        ];
    }

    $url =
        'https://api.razorpay.com/v1/payments/' .
        rawurlencode($payment_id);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD =>
        MYJAT_RAZORPAY_KEY_ID .
            ':' .
            MYJAT_RAZORPAY_KEY_SECRET,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ],
    ]);

    $response =
        curl_exec($ch);

    $http_code =
        (int) curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

    $curl_error =
        curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        return [
            'success' => false,
            'message' =>
            'Razorpay payment status could not be fetched.',
            'error' =>
            $curl_error
        ];
    }

    $data =
        json_decode(
            $response,
            true
        );

    if (
        $http_code < 200
        || $http_code >= 300
        || !is_array($data)
    ) {
        return [
            'success' => false,
            'message' =>
            'Razorpay returned an invalid payment response.',
            'response' =>
            $data
        ];
    }

    return [
        'success' => true,
        'payment' => $data
    ];
}
