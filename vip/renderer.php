<?php

declare(strict_types=1);

function myjat_vip_render_page($content): void
{
?>
    <!DOCTYPE html>
    <html lang="hi">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0">

        <title>MYJAT</title>

        <?php
        if (function_exists('myjat_vip_render_css')) {
            myjat_vip_render_css();
        }
        ?>

    </head>

    <body>

        <?php

        $header = MYJAT_ROOT . '/includes/pages/zheader.php';

        if (is_file($header)) {
            require $header;
        }

        ?>
        <main id="myjat-main-content">
            <div class="myjat-container myjat-glassclear">
                <?= $content ?>
            </div>
        </main>
        <?php

        $footer = MYJAT_ROOT . '/includes/pages/zfooter.php';

        if (is_file($footer)) {
            require $footer;
        }

        ?>

        <?php

        if (function_exists('myjat_vip_render_js')) {
            myjat_vip_render_js();
        }

        ?>

    </body>

    </html>
<?php
}


function myjat_vip_render_function(
    $function,
    $pdo = null
) {

    if (!function_exists($function)) {
        http_response_code(500);

        return
            '<h1>Page function unavailable.</h1>'
            . '<p>'
            . htmlspecialchars(
                $function,
                ENT_QUOTES,
                'UTF-8'
            )
            . '</p>';
    }

    $pdo_functions = [
        'myjat_membership_form',
        'myjat_application_status',
        'myjat_membership_admin_page',
        'myjat_member_card_page',
        'myjat_admin_member_card_page',
        'myjat_member_verify',
    ];


        // =====================================================
    // ADMIN ACCESS
    // =====================================================

    $admin_functions = [
        'myjat_membership_admin_page',
        'myjat_render_application_view',
        'myjat_admin_member_card_page',
    ];

if (
    in_array(
        $function,
        $admin_functions,
        true
    )
) {
    $user = myjat_current_user();

    if (($user['role'] ?? '') !== 'admin') {

    

        return '<h1>Access Denied.</h1>';
    }
}





    if ($function === 'myjat_member_dashboard') {

        $user_id =
            $_SESSION['myjat_user']['id']
            ?? 0;

        return (string) call_user_func(
            $function,
            $pdo,
            $user_id
        );
    }

    if (
        in_array(
            $function,
            $pdo_functions,
            true
        )
    ) {

        return (string) call_user_func(
            $function,
            $pdo
        );
    }


    if ($function === 'myjat_render_application_view') {

        $application_id = (int) (
            $_GET['id'] ?? 0
        );

        if ($application_id <= 0 || !$pdo) {
            http_response_code(404);

            return '<h1>Application not found.</h1>';
        }

        $stmt = $pdo->prepare(
            'SELECT *
         FROM membership_applications
         WHERE id = ?
         LIMIT 1'
        );

        $stmt->execute([
            $application_id
        ]);

        $application = $stmt->fetch(
            PDO::FETCH_OBJ
        );

        if (!$application) {
            http_response_code(404);

            return '<h1>Application not found.</h1>';
        }

        return (string) call_user_func(
            $function,
            $application
        );
    }
    return (string) call_user_func(
        $function
    );
}
