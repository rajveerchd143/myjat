<?php

function myjat_member_dashboard($pdo, $user_id)
{
    if (empty($user_id)) {
        return '<div class="dashboard-message">Please login first.</div>';
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
        return '<div class="dashboard-message">No membership record found.</div>';
    }

    $name = htmlspecialchars(
        $app->full_name ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $status = (string) ($app->status ?? '');

    $status_text = htmlspecialchars(
        ucfirst($status),
        ENT_QUOTES,
        'UTF-8'
    );

    $number = htmlspecialchars(
        $app->membership_no ?: 'Pending',
        ENT_QUOTES,
        'UTF-8'
    );

    $id = (int) ($app->id ?? 0);

    ob_start();
    ?>

    <div class="dashboard">

        <div class="dashboard-head">
            <h1>Welcome, <?= $name ?></h1>
            <p>Your membership dashboard</p>
        </div>


        <div class="dashboard-grid">


            <div class="dashboard-box">

                <h2>Membership Status</h2>

                <span class="status status-<?= strtolower($status) ?>">
                    <?= $status_text ?>
                </span>

            </div>


            <div class="dashboard-box">

                <h2>Membership Number</h2>

                <strong class="number">
                    <?= $number ?>
                </strong>

            </div>


            <div class="dashboard-box">

                <h2>PVC Card</h2>

                <?php if (strtolower($status) === 'approved'): ?>

                    <a
                        class="action"
                        href="/member-card/?id=<?= $id ?>">

                        View PVC Card

                    </a>

                <?php else: ?>

                    <p class="muted">
                        Available after approval.
                    </p>

                <?php endif; ?>

            </div>


            <div class="dashboard-box">

                <h2>Application</h2>

                <a
                    class="action"
                    href="/application-status">

                    View Status

                </a>

            </div>


        </div>

    </div>

    <?php

    return ob_get_clean();
}