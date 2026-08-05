<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_nopriv_myjat_google_debug', 'myjat_google_debug');
add_action('admin_post_myjat_google_debug', 'myjat_google_debug');

function myjat_google_debug(){

    header('Content-Type: text/plain; charset=utf-8');

    echo "====================================\n";
    echo "MYJAT GOOGLE OAUTH DEBUG\n";
    echo "====================================\n\n";

    echo "Config File Loaded : ";
    echo defined('MYJAT_GOOGLE_CLIENT_ID') ? "YES\n" : "NO\n";

    echo "Client ID:\n";
    echo defined('MYJAT_GOOGLE_CLIENT_ID') ? MYJAT_GOOGLE_CLIENT_ID : "NOT FOUND";
    echo "\n\n";

    echo "Client Secret:\n";
    echo defined('MYJAT_GOOGLE_CLIENT_SECRET') ? "LOADED" : "NOT FOUND";
    echo "\n\n";

    $redirect = admin_url('admin-ajax.php?action=myjat_google_callback');

    echo "Redirect URI:\n";
    echo $redirect;
    echo "\n\n";

    $params = array(
        'client_id' => MYJAT_GOOGLE_CLIENT_ID,
        'redirect_uri' => $redirect,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'access_type' => 'online',
        'prompt' => 'select_account'
    );

    $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

    echo "OAuth URL:\n";
    echo "\n\n============================\n";
echo "OPEN THIS URL MANUALLY\n";
echo "============================\n\n";
echo $url;
echo "\n\n";
exit;
    echo $url;
    echo "\n\n";

    $response = wp_remote_get($url, array(
        'redirection' => 0,
        'timeout' => 20
    ));

    echo "HTTP Result:\n";

    if (is_wp_error($response)) {

        print_r($response->get_error_message());

    } else {

        echo "Status : " . wp_remote_retrieve_response_code($response) . "\n\n";

        echo "Headers:\n";
        print_r(wp_remote_retrieve_headers($response));

    }

    exit;
}