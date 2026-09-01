<?php

function myjat_application_status($pdo, $user_id = null)
{
    if ($user_id === null) {
        $user_id = (int) (
            $_SESSION['myjat_user']['id'] ?? 0
        );
    }

    if (!$user_id) {
        return '<div class="status-message">Please login first.</div>';
    }

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE user_id = ?
         LIMIT 1"
    );

    $stmt->execute([
        (int) $user_id
    ]);

    $app = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$app) {
        return '<div class="status-message">
            No membership application found.
        </div>';
    }

    $name = htmlspecialchars(
        $app->full_name ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $number = htmlspecialchars(
        $app->membership_no ?: 'Pending',
        ENT_QUOTES,
        'UTF-8'
    );

    $status = htmlspecialchars(
        ucfirst((string) ($app->status ?? '')),
        ENT_QUOTES,
        'UTF-8'
    );

    $date = htmlspecialchars(
        $app->application_date ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    ob_start();
    ?>

    <div class="status-page">

        <div class="status-box">

            <h1>Membership Application Status</h1>

            <div class="status-row">
                <span>Name</span>
                <strong><?= $name ?></strong>
            </div>

            <div class="status-row">
                <span>Membership No.</span>
                <strong><?= $number ?></strong>
            </div>

            <div class="status-row">
                <span>Status</span>
                <strong><?= $status ?></strong>
            </div>

            <div class="status-row">
                <span>Application Date</span>
                <strong><?= $date ?></strong>
            </div>

        </div>

    </div>

    <?php

    return ob_get_clean();
}