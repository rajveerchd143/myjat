<?php

// =========================================================
// Authentication
// Standalone PHP Version
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =========================================================
// Helpers
// =========================================================

function myjat_is_logged_in()
{
    return !empty($_SESSION['myjat_user']);
}


function myjat_current_user()
{
    return $_SESSION['myjat_user'] ?? null;
}


function myjat_current_user_id()
{
    return $_SESSION['myjat_user']['id'] ?? 0;
}


function myjat_is_admin()
{
    return !empty($_SESSION['myjat_user']['role'])
        && $_SESSION['myjat_user']['role'] === 'administrator';
}


function myjat_is_secretary()
{
    return !empty($_SESSION['myjat_user']['role'])
        && $_SESSION['myjat_user']['role'] === 'myjat_secretary';
}


function myjat_is_member()
{
    return myjat_is_logged_in()
        && !myjat_is_admin()
        && !myjat_is_secretary();
}


// =========================================================
// Redirect After Login
// =========================================================

function myjat_login_redirect($user)
{
    $role = is_array($user)
        ? ($user['role'] ?? '')
        : '';

    if ($role === 'administrator') {
        return '/admin/';
    }

    if ($role === 'myjat_secretary') {
        return '/membership-applications/';
    }

    return '/';
}


// =========================================================
// Native Login Engine
// =========================================================

function myjat_login_user($user)
{
    if (empty($user)) {
        return false;
    }

    $_SESSION['myjat_user'] = [

        'id' => $user['id'] ?? 0,

        'username' => $user['username'] ?? '',

        'email' => $user['email'] ?? '',

        'name' => $user['name'] ?? '',

        'role' => $user['role'] ?? 'member'

    ];

    return true;
}


// =========================================================
// Login Credentials
// =========================================================

function myjat_login_credentials(
    $pdo,
    $username,
    $password,
    $remember = true
) {
    $username = trim($username);

    $stmt = $pdo->prepare(
        "SELECT *
         FROM users
         WHERE email = ?
            OR username = ?
         LIMIT 1"
    );

    $stmt->execute([
        $username,
        $username
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    if (
        empty($user['password']) ||
        !password_verify($password, $user['password'])
    ) {
        return false;
    }

    myjat_login_user($user);

    return $user;
}


// =========================================================
// AJAX Login
// =========================================================

function myjat_ajax_login($pdo)
{
    $username = $_POST['username'] ?? '';

    $password = $_POST['password'] ?? '';

    $remember = !empty($_POST['remember']);

    $user = myjat_login_credentials(
        $pdo,
        $username,
        $password,
        $remember
    );

    header('Content-Type: application/json');

    if (!$user) {

        echo json_encode([
            'success' => false,
            'message' => 'Invalid username or password.'
        ]);

        exit;
    }

    echo json_encode([
        'success' => true,
        'redirect' => myjat_login_redirect($user)
    ]);

    exit;
}


// =========================================================
// Logout
// =========================================================

function myjat_logout()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    header(
        'Location: /login'
    );

    exit;
}


// =========================================================
// Login Page
// =========================================================

function myjat_login_page()
{
    ob_start();
    ?>

    <div class="myjat-login-wrapper">

        <h2>MYJAT Login</h2>

        <?php if (function_exists('myjat_google_login_button')): ?>

            <?= myjat_google_login_button() ?>

        <?php endif; ?>


        <div class="myjat-social-login"></div>


        <div class="myjat-login-divider">
            <span>OR</span>
        </div>


        <form
            id="myjat-login-form"
            method="post">

            <p>

                <input
                    type="text"
                    name="username"
                    placeholder="Email or Username"
                    required>

            </p>


            <p>

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required>

            </p>


            <p>

                <label>

                    <input
                        type="checkbox"
                        name="remember"
                        value="1">

                    Remember Me

                </label>

            </p>


            <p>

                <button type="submit">
                    Login
                </button>

            </p>


            <div id="myjat-login-message"></div>

        </form>

    </div>

    <?php

    return ob_get_clean();
}