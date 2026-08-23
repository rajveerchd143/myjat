<?php

// =========================================================
// Membership Application Status
// Standalone PHP Version
// =========================================================

function myjat_application_status($pdo, $user_id)
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
        return '<div class="myjat-warning-message">No membership application found.</div>';
    }

    ob_start();
    ?>

    <div class="myjat-status-card">

        <h2>Membership Application Status</h2>

        <table class="myjat-status-table">

            <tr>
                <th>Name</th>
                <td>
                    <?= htmlspecialchars($app->full_name ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>
            </tr>

            <tr>
                <th>Membership No.</th>
                <td>
                    <?= htmlspecialchars(
                        $app->membership_no ?: 'Pending',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <strong>
                        <?= htmlspecialchars(
                            ucfirst($app->status ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </td>
            </tr>

            <tr>
                <th>Application Date</th>
                <td>
                    <?= htmlspecialchars(
                        $app->application_date ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>
            </tr>

        </table>

    </div>

    <?php

    return ob_get_clean();
}