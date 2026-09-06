<?php

// =========================================================
// MYJAT Member Dashboard
// Standalone PHP Version
// =========================================================

function myjat_member_dashboard($pdo, $user_id = 0)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $current_user = $_SESSION['myjat_user'] ?? null;

    if (!$current_user) {

        return '
            <div class="dashboard-message">
                कृपया पहले लॉगिन करें।
            </div>
        ';
    }


    // =====================================================
    // Logged-in User
    // =====================================================

    $email = trim(
        $current_user['email']
        ?? $current_user['user_email']
        ?? ''
    );

    if (!$email) {

        return '
            <div class="dashboard-message">
                लॉगिन ईमेल उपलब्ध नहीं है।
            </div>
        ';
    }


    // =====================================================
    // Membership Application
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE email = ?
            OR google_email = ?
         ORDER BY id DESC
         LIMIT 1"
    );

    $stmt->execute([
        $email,
        $email
    ]);

    $app = $stmt->fetch(PDO::FETCH_OBJ);


    if (!$app) {

        return '
            <div class="dashboard-message">
                <strong>
                    अभी आपका सदस्यता आवेदन रिकॉर्ड उपलब्ध नहीं है।
                </strong>
            </div>
        ';
    }


    // =====================================================
    // Member Data
    // =====================================================

    $name = htmlspecialchars(
        $app->full_name
        ?? $current_user['name']
        ?? '',
        ENT_QUOTES,
        'UTF-8'
    );


    $raw_status = trim(
        (string) ($app->status ?? 'Pending')
    );

    $status_key = strtolower($raw_status);


    if (
        in_array(
            $status_key,
            ['approved', 'approve', 'स्वीकृत'],
            true
        )
    ) {

        $status_class = 'status-approved';
        $status_text = 'स्वीकृत';

    } elseif (
        in_array(
            $status_key,
            ['rejected', 'reject', 'अस्वीकृत'],
            true
        )
    ) {

        $status_class = 'status-rejected';
        $status_text = 'अस्वीकृत';

    } else {

        $status_class = 'status-pending';
        $status_text = 'लंबित';

    }


    $membership_no = htmlspecialchars(
        $app->membership_no ?: '—',
        ENT_QUOTES,
        'UTF-8'
    );


    $membership_type = htmlspecialchars(
        $app->membership_type ?: '—',
        ENT_QUOTES,
        'UTF-8'
    );


    $application_date = '—';

    if (!empty($app->application_date)) {

        $timestamp =
            strtotime($app->application_date);

        if ($timestamp) {

            $application_date =
                date('d-m-Y', $timestamp);

        }
    }


    $application_id =
        (int) ($app->id ?? 0);

    ?>

    <div class="dashboard">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="dashboard-head">

            <h1>
                नमस्कार, <?= $name ?> जी 🙏
            </h1>

            <p>
                आपकी सदस्यता जानकारी
            </p>

        </div>


        <!-- =================================================
             DASHBOARD GRID
        ================================================== -->

        <div class="dashboard-grid">


            <!-- MEMBERSHIP STATUS -->

            <div class="dashboard-box">

                <h2>
                    सदस्यता स्थिति
                </h2>

                <span class="status <?= $status_class ?>">

                    <?= $status_text ?>

                </span>

            </div>


            <!-- MEMBERSHIP NUMBER -->

            <div class="dashboard-box">

                <h2>
                    सदस्यता क्रमांक
                </h2>

                <p class="number">

                    <?= $membership_no ?>

                </p>

            </div>


            <!-- MEMBERSHIP TYPE -->

            <div class="dashboard-box">

                <h2>
                    सदस्यता प्रकार
                </h2>

                <p>
                    <?= $membership_type ?>
                </p>

            </div>


            <!-- APPLICATION DATE -->

            <div class="dashboard-box">

                <h2>
                    आवेदन दिनांक
                </h2>

                <p>
                    <?= htmlspecialchars(
                        $application_date,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </p>

            </div>


            <!-- PVC CARD -->

            <div class="dashboard-box">

                <h2>
                    PVC कार्ड
                </h2>

                <?php if (
                    $status_class === 'status-approved'
                ) : ?>

                    <a
                        href="/member-card/?id=<?= $application_id ?>"
                        class="action">

                        PVC कार्ड देखें

                    </a>

                <?php else : ?>

                    <span class="muted">

                        स्वीकृति के बाद उपलब्ध होगा।

                    </span>

                <?php endif; ?>

            </div>


        </div>


    </div>

    <?php

    return ob_get_clean();
}