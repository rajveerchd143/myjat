<?php

// =========================================================
// Google Login
// Standalone PHP Version
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================
// Google OAuth Configuration
// =========================================================

define(
    'MYJAT_GOOGLE_REDIRECT_URI',
    getenv('GOOGLE_REDIRECT_URI') ?: ''
);
// =========================================================
// OAuth State
// =========================================================

function myjat_google_create_state()
{
    $state = bin2hex(random_bytes(32));

    $_SESSION['myjat_google_state'] = [
        'value'   => $state,
        'expires' => time() + 600
    ];

    return $state;
}


function myjat_google_verify_state($state)
{
    if (empty($_SESSION['myjat_google_state'])) {
        return false;
    }

    $saved = $_SESSION['myjat_google_state'];

    unset($_SESSION['myjat_google_state']);

    if (time() > $saved['expires']) {
        return false;
    }

    return hash_equals(
        $saved['value'],
        $state
    );
}


// =========================================================
// Google Login URL
// =========================================================

function myjat_google_login_url()
{
    $params = [

        'client_id' => MYJAT_GOOGLE_CLIENT_ID,

        'redirect_uri' => MYJAT_GOOGLE_REDIRECT_URI,

        'response_type' => 'code',

        'scope' => 'openid email profile',

        'access_type' => 'online',

        'prompt' => 'select_account',

        'include_granted_scopes' => 'true',

        'state' => myjat_google_create_state()
    ];

    return
        'https://accounts.google.com/o/oauth2/v2/auth?'
        . http_build_query($params);
}


// =========================================================
// Google Login Button
// =========================================================

function myjat_google_login_button(
    $text = 'Continue with Google'
) {
    $url = htmlspecialchars(
        myjat_google_login_url(),
        ENT_QUOTES,
        'UTF-8'
    );

    $label = htmlspecialchars(
        $text,
        ENT_QUOTES,
        'UTF-8'
    );

    return '
    <a
        class="myjat-google-login"
        href="' . $url . '">

        <svg
            width="20"
            height="20"
            viewBox="0 0 48 48"
            aria-hidden="true">

            <path
                fill="#FFC107"
                d="M43.611 20.083H42V20H24v8h11.303C33.651 32.657 29.239 36 24 36c-6.627 0-12-5.373-12-12S17.373 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>

            <path
                fill="#FF3D00"
                d="M6.306 14.691l6.571 4.819C14.655 16.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4C16.318 4 9.656 8.337 6.306 14.691z"/>

            <path
                fill="#4CAF50"
                d="M24 44c5.167 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.153 35.091 26.715 36 24 36c-5.218 0-9.617-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>

            <path
                fill="#1976D2"
                d="M43.611 20.083H42V20H24v8h11.303c-1.056 3.09-3.256 5.508-6.084 6.57l6.19 5.238C38.973 36.511 44 30.779 44 24c0-1.341-.138-2.65-.389-3.917z"/>

        </svg>

        <span>' . $label . '</span>

    </a>';
}


// =========================================================
// Google Callback
// =========================================================

function myjat_google_callback()
{
    if (empty($_GET['code'])) {
        exit('Missing authorization code.');
    }

    if (empty($_GET['state'])) {
        exit('Invalid request.');
    }

    $state = trim($_GET['state']);

    if (!myjat_google_verify_state($state)) {
        exit('Security validation failed.');
    }


    // =====================================================
    // Exchange Authorization Code For Token
    // =====================================================

    $post_data = http_build_query([

        'code' => $_GET['code'],

        'client_id' => MYJAT_GOOGLE_CLIENT_ID,

        'client_secret' => MYJAT_GOOGLE_CLIENT_SECRET,

        'redirect_uri' => MYJAT_GOOGLE_REDIRECT_URI,

        'grant_type' => 'authorization_code'

    ]);


    $context = stream_context_create([

        'http' => [

            'method' => 'POST',

            'header' =>
            "Content-Type: application/x-www-form-urlencoded\r\n"
                . "Content-Length: "
                . strlen($post_data)
                . "\r\n",

            'content' => $post_data,

            'timeout' => 20
        ]

    ]);


    $response = file_get_contents(
        'https://oauth2.googleapis.com/token',
        false,
        $context
    );


    if ($response === false) {
        exit('Unable to contact Google.');
    }


    $token = json_decode(
        $response,
        true
    );


    if (empty($token['access_token'])) {
        exit('Google authentication failed.');
    }


    // =====================================================
    // Get Google Profile
    // =====================================================

    $context = stream_context_create([

        'http' => [

            'method' => 'GET',

            'header' =>
            "Authorization: Bearer "
                . $token['access_token']
                . "\r\n",

            'timeout' => 20

        ]

    ]);


    $response = file_get_contents(
        'https://www.googleapis.com/oauth2/v3/userinfo',
        false,
        $context
    );


    if ($response === false) {
        exit('Unable to fetch Google profile.');
    }


    $profile = json_decode(
        $response,
        true
    );


    if (empty($profile['email'])) {
        exit('Google email not found.');
    }


    $email = filter_var(
        $profile['email'],
        FILTER_SANITIZE_EMAIL
    );

    $name = !empty($profile['name'])
        ? trim($profile['name'])
        : '';

    $google_id = !empty($profile['sub'])
        ? trim($profile['sub'])
        : '';

    $picture = !empty($profile['picture'])
        ? trim($profile['picture'])
        : '';


    // =====================================================
    // Store Google User Session
    // =====================================================

    // =====================================================
    // Find Google User In Database
    // =====================================================


    global $pdo;

    if (!$pdo instanceof PDO) {
        exit('Database connection unavailable.');
    }


    /* =====================================================
   Find User By Google Email
   ===================================================== */

    $stmt = $pdo->prepare(
        "SELECT
        id,
        email,
        name,
        google_id,
        picture,
        role
     FROM membership_users
     WHERE LOWER(email) = LOWER(?)
     LIMIT 1"
    );

    $stmt->execute([
        $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        exit('Google account is not registered.');
    }


    /* =====================================================
   Update Google Profile
   ===================================================== */

    $update = $pdo->prepare(
        "UPDATE membership_users
     SET
        google_id = ?,
        name = ?,
        picture = ?,
        updated_at = NOW()
     WHERE id = ?"
    );

    $update->execute([
        $google_id,
        $name,
        $picture,
        $user['id']
    ]);


    /* =====================================================
   Refresh Session User
   ===================================================== */

    $user['google_id'] = $google_id;
    $user['name']      = $name;
    $user['picture']   = $picture;



    // =====================================================
    // Store User Session
    // =====================================================

    $_SESSION['myjat_user'] = $user;

    // =====================================================
    // Redirect
    // =====================================================
    if (($user['role'] ?? '') === 'admin') {
        header('Location: /membership-admin/');
        exit;
    }

    header('Location: /member-dashboard/');
    exit;
}
