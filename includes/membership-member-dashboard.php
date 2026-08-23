<?php

// =========================================================
// Member Dashboard
// Standalone PHP Version
// =========================================================

function myjat_member_dashboard($pdo, $user_id)
{
    if (empty($user_id)) {
        return '<div class="myjat-warning-message">Please login first.</div>';
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
        return '<div class="myjat-warning-message">No membership record found.</div>';
    }

    ob_start();

    $full_name = htmlspecialchars(
        $app->full_name ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $status = $app->status ?? '';

    $status_display = htmlspecialchars(
        ucfirst($status),
        ENT_QUOTES,
        'UTF-8'
    );

    $membership_no = htmlspecialchars(
        $app->membership_no ?: 'Pending',
        ENT_QUOTES,
        'UTF-8'
    );

    $application_id = (int) ($app->id ?? 0);

    ?>

    <div class="myjat-dashboard">

        <h2>
            Welcome, <?= $full_name ?>
        </h2>

        <div class="myjat-dashboard-grid">


            <div class="myjat-dashboard-card">

                <h3>
                    Membership Status
                </h3>

                <p>
                    <strong>
                        <?= $status_display ?>
                    </strong>
                </p>

            </div>


            <div class="myjat-dashboard-card">

                <h3>
                    Membership Number
                </h3>

                <p>
                    <?= $membership_no ?>
                </p>

            </div>


            <div class="myjat-dashboard-card">

                <h3>
                    PVC Card
                </h3>

                <?php if (strtolower($status) === 'approved'): ?>

                    <a
                        class="button"
                        href="/member-card/?id=<?= $application_id ?>">
                        View PVC Card
                    </a>

                <?php else: ?>

                    <p>
                        Available after approval.
                    </p>

                <?php endif; ?>

            </div>


            <div class="myjat-dashboard-card">

                <h3>
                    Application
                </h3>

                <a
                    class="button"
                    href="/application-status">
                    View Status
                </a>

            </div>


        </div>

    </div>

    <?php

    return ob_get_clean();
}