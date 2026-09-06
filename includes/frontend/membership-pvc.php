<?php

// =========================================================
// Member PVC Card
// Standalone PHP Version
// =========================================================

function myjat_render_member_card($application)
{
    ob_start();

    $photo_url = htmlspecialchars(
        $application->photo_url ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $full_name = htmlspecialchars(
        $application->full_name ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $membership_no = htmlspecialchars(
        $application->membership_no ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $membership_type = htmlspecialchars(
        $application->membership_type ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $district = htmlspecialchars(
        $application->district ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $member_since = htmlspecialchars(
        $application->member_since ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $qr_data =
        trim(
            $application->qr_code ?? ''
        );

    if (!$qr_data && !empty($application->membership_no)) {

        $qr_data =
            '/member-verification/?id='
            . urlencode(
                $application->membership_no
            );
    }

    $qr_code =
        urlencode($qr_data);
?>

    <!-- =====================================================
         PVC Card Front
    ====================================================== -->

    <div class="myjat-pvc">

        <div class="myjat-pvc-back-watermark">

            <img
                class="myjat-pvc-back-watermark-logo"
                src="/assets/images/background/logo.png"
                alt="Logo">

        </div>


        <div class="myjat-pvc-header">

            <div class="myjat-pvc-logo-wrap">

                <img
                    class="myjat-pvc-logo"
                    src="/assets/images/background/logo.png"
                    alt="Logo">

            </div>


            <div class="myjat-pvc-header-content">

                <div class="myjat-pvc-title">
                    अखिल भारतवर्षीय जाट महासभा
                </div>

                <div class="myjat-pvc-subtitle">
                    सदस्य पहचान पत्र
                    <br>
                    (Member Identity Card)
                </div>

            </div>

        </div>


        <div class="myjat-pvc-divider"></div>


        <div class="myjat-pvc-body">

            <?php if (!empty($photo_url)): ?>

                <img
                    class="myjat-pvc-photo"
                    src="<?= $photo_url ?>"
                    alt="<?= $full_name ?>">

            <?php else: ?>

                <div class="myjat-pvc-photo-placeholder"></div>

            <?php endif; ?>


            <div class="myjat-pvc-header-content">

                <strong class="myjat-pvc-name">
                    <?= $full_name ?>
                </strong>


                <table class="myjat-pvc-table">

                    <tr>

                        <td class="myjat-pvc-cell">
                            🏷️ ABJM No<br>
                            <strong><?= $membership_no ?></strong>
                        </td>

                        <td class="myjat-pvc-cell">
                            👥 Membership Type<br>
                            <strong><?= $membership_type ?></strong>
                        </td>

                    </tr>


                    <tr>

                        <td class="myjat-pvc-cell">
                            📍 District<br>
                            <strong><?= $district ?></strong>
                        </td>

                        <td class="myjat-pvc-cell">
                            📅 Member Since<br>
                            <strong><?= $member_since ?></strong>
                        </td>

                    </tr>

                </table>

            </div>

        </div>


        <!-- =================================================
             Signatures
        ================================================== -->

        <div class="myjat-pvc-signatures">


            <div class="myjat-pvc-sign myjat-pvc-sign-left">

                <img
                    class="myjat-pvc-sign-image"
                    src="/assets/images/background/president_sign.png"
                    alt="President Signature">

                <div class="myjat-pvc-sign-line"></div>

                <div class="myjat-pvc-sign-name">
                    चौ० वीरेन्द्र सिंह
                </div>

                <div class="myjat-pvc-sign-post">
                    राष्ट्रीय अध्यक्ष
                </div>

            </div>


            <div class="myjat-pvc-sign myjat-pvc-sign-right">

                <img
                    class="myjat-pvc-sign-image"
                    src="/assets/images/background/secret_sign.png"
                    alt="General Secretary Signature">

                <div class="myjat-pvc-sign-line"></div>

                <div class="myjat-pvc-sign-name">
                    चौ० शेरसिंह
                </div>

                <div class="myjat-pvc-sign-post">
                    राष्ट्रीय महासचिव
                </div>

            </div>


        </div>

    </div>


    <!-- =====================================================
         PVC Card Back
    ====================================================== -->

    <br>

    <div class="myjat-pvc myjat-pvc-back">


        <div class="myjat-pvc-watermark">

            <img
                class="myjat-pvc-back-watermark-logo"
                src="/assets/images/background/logo.png"
                alt="Logo">

        </div>


        <div class="myjat-pvc-back-title">

            अखिल भारतवर्षीय जाट महासभा

            <hr class="myjat-pvc-back-divider">

        </div>


        <!-- =================================================
             QR Code
        ================================================== -->

        <div class="myjat-pvc-qr">

            <img
                class="myjat-pvc-qr-image"
                src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= $qr_code ?>"
                alt="QR Code">

            <div class="myjat-pvc-qr-title">
                Scan to Verify
            </div>

            <div class="myjat-pvc-qr-url">
                www.myjat.in
            </div>


            <div class="myjat-pvc-back-footer">

                This card remains the property of

                <br>

                <strong>
                    Akhil Bharatvarshiya Jat Mahasabha
                </strong>

                <br>

                If found, please return to the organization.

                <br>

                📞 Contact:
                <strong>+91 9412274738</strong>

            </div>

        </div>

    </div>

<?php

    return ob_get_clean();
}


// =========================================================
// Member Card Page
// =========================================================
// =========================================================
// Member Card Page
// =========================================================

function myjat_member_card_page($pdo)
{
    // =====================================================
    // Login Check
    // =====================================================

    if (empty($_SESSION['myjat_user'])) {
        return '<div class="myjat-warning-message">
            Please login first.
        </div>';
    }


    // =====================================================
    // Get Application ID from URL
    // Example: /member-card/?id=17
    // =====================================================

    $id = (int) ($_GET['id'] ?? 0);

    if (!$id) {
        return '<div class="myjat-warning-message">
            Member not found.
        </div>';
    }


    // =====================================================
    // Get Exact Application
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->execute([$id]);

    $application = $stmt->fetch(PDO::FETCH_OBJ);


    if (!$application) {
        return '<div class="myjat-warning-message">
            Member application not found.
        </div>';
    }


    // =====================================================
    // Approval Check
    // =====================================================

    if (
        strtolower(
            trim($application->status ?? '')
        ) !== 'approved'
    ) {
        return '<div class="myjat-warning-message">
            PVC card is available after approval.
        </div>';
    }


    // =====================================================
    // Render Exact Member Card
    // =====================================================

    return myjat_render_member_card(
        $application
    );
}
// =========================================================
// Admin Member Card Page
// =========================================================

function myjat_admin_member_card_page($pdo)
{
    // =====================================================
    // Admin Permission Check
    // =====================================================

    if (
        !function_exists('myjat_can')
        || !myjat_can('myjat_view_application')
    ) {
        http_response_code(403);

        return 'Access denied.';
    }


    // =====================================================
    // Application ID
    // =====================================================

    $id = (int) (
        $_GET['id'] ?? 0
    );

    if (!$id) {
        return 'Member not found.';
    }


    // =====================================================
    // Get Application
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->execute([
    $_SESSION['myjat_user']['email'] ?? '',
    $_SESSION['myjat_user']['email'] ?? ''
]);

    $application = $stmt->fetch(PDO::FETCH_OBJ);


    if (!$application) {
        return 'Member not found.';
    }


    // =====================================================
    // Approval Check
    // =====================================================

    if (
        strtolower(
            trim($application->status ?? '')
        ) !== 'approved'
    ) {
        return 'PVC card is available after approval.';
    }


    // =====================================================
    // Render Card
    // =====================================================

    return myjat_render_member_card(
        $application
    );
}
