<?php

// =========================================================
// Membership Admin
// Standalone PHP Version
// =========================================================


// =========================================================
// Update Application Status
// =========================================================

function myjat_update_application_status($pdo)
{
    if (
        empty($_GET['myjat_action']) ||
        empty($_GET['application_id'])
    ) {
        return;
    }

    $id = (int) $_GET['application_id'];

    $stmt = $pdo->prepare(
        "SELECT membership_no
         FROM membership_applications
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->execute([$id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return;
    }

    $membership_no = $row['membership_no'] ?? '';

    if ($_GET['myjat_action'] === 'approve') {

        $current_user = myjat_current_user();

        $approved_by = is_array($current_user)
            ? ($current_user['username'] ?? '')
            : '';

        $stmt = $pdo->prepare(
            "UPDATE membership_applications
             SET
                status = ?,
                approval_date = ?,
                approved_by = ?,
                member_since = ?,
                qr_code = ?
             WHERE id = ?"
        );

        $stmt->execute([
            'Approved',
            date('Y-m-d'),
            $approved_by,
            date('Y'),
            '/member-verification/?id=' .
                urlencode($membership_no),
            $id
        ]);

    } elseif ($_GET['myjat_action'] === 'reject') {

        $stmt = $pdo->prepare(
            "UPDATE membership_applications
             SET status = ?
             WHERE id = ?"
        );

        $stmt->execute([
            'Rejected',
            $id
        ]);
    }
}


// =========================================================
// Save Member Photo
// =========================================================

function myjat_save_photo_url($pdo)
{
    if (
        empty($_POST['member_id']) ||
        !isset($_POST['photo_url'])
    ) {
        return;
    }

    $id = (int) $_POST['member_id'];

    $photo_url = trim(
        $_POST['photo_url']
    );

    $stmt = $pdo->prepare(
        "UPDATE membership_applications
         SET photo_url = ?
         WHERE id = ?"
    );

    $stmt->execute([
        $photo_url,
        $id
    ]);
}


// =========================================================
// Mark Card Printed
// =========================================================

function myjat_mark_card_printed($pdo)
{
    if (empty($_GET['mark_card_printed'])) {
        return;
    }

    $id = (int) $_GET['mark_card_printed'];

    $stmt = $pdo->prepare(
        "UPDATE membership_applications
         SET card_printed = ?
         WHERE id = ?"
    );

    $stmt->execute([
        'Yes',
        $id
    ]);
}


// =========================================================
// Admin Page
// =========================================================



// =========================================================
// Organization Settings
// =========================================================

function myjat_settings_page()
{
    ob_start();
    ?>

    <div class="myjat-settings-page">

        <h1>
            Organization Settings
        </h1>

    </div>

    <?php

    return ob_get_clean();
}


// =========================================================
// Print CSS
// =========================================================

function myjat_admin_print_css()
{
    ?>

    <style>

        @media print {

            .myjat-admin-actions,
            .myjat-btn,
            .button,
            .button-primary {
                display: none !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            table th,
            table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
            }

            body {
                background: #fff !important;
            }

        }

    </style>

    <?php
}