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




/* =========================================================
   Gallery Management
   ========================================================= */

function myjat_gallery_upload($pdo)
{
    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST' ||
        ($_POST['myjat_gallery_action'] ?? '') !== 'upload'
    ) {
        return;
    }

    if (
        empty($_FILES['gallery_image']) ||
        !isset($_FILES['gallery_image']['error'])
    ) {
        return;
    }

    $file = $_FILES['gallery_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return;
    }

    $caption = trim(
        (string) ($_POST['gallery_caption'] ?? '')
    );

    $max_size = 5 * 1024 * 1024;

    if ((int) $file['size'] > $max_size) {
        return;
    }

    $image_info = @getimagesize($file['tmp_name']);

    if ($image_info === false) {
        return;
    }

    $allowed_types = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    $image_type = $image_info[2] ?? 0;

    if (!isset($allowed_types[$image_type])) {
        return;
    }

    $extension = $allowed_types[$image_type];

    $upload_dir =
        rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')
        . DIRECTORY_SEPARATOR
        . 'uploads'
        . DIRECTORY_SEPARATOR
        . 'gallery';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename =
        'gallery-'
        . bin2hex(random_bytes(8))
        . '.'
        . $extension;

    $target =
        $upload_dir
        . DIRECTORY_SEPARATOR
        . $filename;

    if (!move_uploaded_file(
        $file['tmp_name'],
        $target
    )) {
        return;
    }

    $image_url =
        '/uploads/gallery/'
        . $filename;

    $stmt = $pdo->prepare(
        "INSERT INTO gallery
        (
            image,
            caption,
            status,
            created_at,
            updated_at
        )
        VALUES
        (
            ?,
            ?,
            'active',
            NOW(),
            NOW()
        )"
    );

    $stmt->execute([
        $image_url,
        $caption
    ]);
}


function myjat_gallery_delete($pdo)
{
    if (
        empty($_GET['gallery_delete'])
    ) {
        return;
    }

    $id = (int) $_GET['gallery_delete'];

    if (!$id) {
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT image
         FROM gallery
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->execute([$id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return;
    }

    $image = trim(
        (string) ($row['image'] ?? '')
    );

    if (
        $image !== ''
        && strpos($image, '/uploads/gallery/') === 0
    ) {
        $file_path =
            rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $image
            );

        if (is_file($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare(
        "DELETE FROM gallery
         WHERE id = ?"
    );

    $stmt->execute([$id]);
}


function myjat_gallery_toggle_status($pdo)
{
    if (
        empty($_GET['gallery_toggle'])
    ) {
        return;
    }

    $id = (int) $_GET['gallery_toggle'];

    if (!$id) {
        return;
    }

    $stmt = $pdo->prepare(
        "UPDATE gallery
         SET status =
            CASE
                WHEN status = 'active'
                THEN 'hidden'
                ELSE 'active'
            END,
            updated_at = NOW()
         WHERE id = ?"
    );

    $stmt->execute([$id]);
}

function myjat_gallery_update_caption($pdo)
{
    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST' ||
        ($_POST['myjat_gallery_action'] ?? '') !== 'update_caption'
    ) {
        return;
    }

    $id = (int) ($_POST['gallery_id'] ?? 0);

    if (!$id) {
        return;
    }

    $caption = trim(
        (string) ($_POST['gallery_caption'] ?? '')
    );

    $stmt = $pdo->prepare(
        "UPDATE gallery
         SET caption = ?, updated_at = NOW()
         WHERE id = ?"
    );

    $stmt->execute([
        $caption,
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


function myjat_process_gallery_actions($pdo)
{
    myjat_gallery_upload($pdo);
    myjat_gallery_update_caption($pdo);
    myjat_gallery_delete($pdo);
    myjat_gallery_toggle_status($pdo);
}
// =========================================================
// Organization Settings
// =========================================================


// =========================================================
// Membership Plans Management
// =========================================================

function myjat_membership_plans_admin($pdo)
{



    // =====================================================
    // Edit Plan Load
    // =====================================================

    $edit_plan = null;

    if (isset($_GET['membership_plan_edit'])) {

        $edit_id = (int) $_GET['membership_plan_edit'];

        if ($edit_id > 0) {

            $stmt = $pdo->prepare(
                "SELECT *
             FROM membership_plans
             WHERE id = ?
             LIMIT 1"
            );

            $stmt->execute([$edit_id]);

            $edit_plan = $stmt->fetch(PDO::FETCH_OBJ);
        }
    }


    
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && ($_POST['membership_plan_action'] ?? '') === 'save'
    ) {

        $id = (int) ($_POST['plan_id'] ?? 0);

        $name = trim(
            (string) ($_POST['name'] ?? '')
        );

        $slug = trim(
            (string) ($_POST['slug'] ?? '')
        );

        $amount = (float) ($_POST['amount'] ?? 0);

        $currency = strtoupper(
            trim((string) ($_POST['currency'] ?? 'INR'))
        );

        $validity_type = $_POST['validity_type'] ?? 'lifetime';

        $validity_value = (int) (
            $_POST['validity_value'] ?? 0
        );

        $min_age = trim(
            (string) ($_POST['min_age'] ?? '')
        );

        $max_age = trim(
            (string) ($_POST['max_age'] ?? '')
        );

        $requires_ordinary =
            isset($_POST['requires_ordinary_membership'])
            ? 1
            : 0;

        $requires_sankalp =
            isset($_POST['requires_sankalp'])
            ? 1
            : 0;

        $is_active =
            isset($_POST['is_active'])
            ? 1
            : 0;

        $sort_order = (int) (
            $_POST['sort_order'] ?? 0
        );

        if (
            $name === ''
            || $slug === ''
            || $amount < 0
            || $currency !== 'INR'
            || !in_array(
                $validity_type,
                ['lifetime', 'years'],
                true
            )
        ) {
            return;
        }

        $min_age = $min_age === ''
            ? null
            : max(0, (int) $min_age);

        $max_age = $max_age === ''
            ? null
            : max(0, (int) $max_age);

        $validity_value =
            $validity_type === 'years'
            ? max(1, $validity_value)
            : null;

        if ($id > 0) {

            $stmt = $pdo->prepare(
                "UPDATE membership_plans
                 SET
                    name = ?,
                    slug = ?,
                    amount = ?,
                    currency = ?,
                    validity_type = ?,
                    validity_value = ?,
                    min_age = ?,
                    max_age = ?,
                    requires_ordinary_membership = ?,
                    requires_sankalp = ?,
                    is_active = ?,
                    sort_order = ?,
                    updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([
                $name,
                $slug,
                $amount,
                $currency,
                $validity_type,
                $validity_value,
                $min_age,
                $max_age,
                $requires_ordinary,
                $requires_sankalp,
                $is_active,
                $sort_order,
                $id
            ]);
        } else {

            $stmt = $pdo->prepare(
                "INSERT INTO membership_plans
                (
                    name,
                    slug,
                    amount,
                    currency,
                    validity_type,
                    validity_value,
                    min_age,
                    max_age,
                    requires_ordinary_membership,
                    requires_sankalp,
                    is_active,
                    sort_order,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    NOW(), NOW()
                )"
            );

            $stmt->execute([
                $name,
                $slug,
                $amount,
                $currency,
                $validity_type,
                $validity_value,
                $min_age,
                $max_age,
                $requires_ordinary,
                $requires_sankalp,
                $is_active,
                $sort_order
            ]);
        }
    }


    // =====================================================
    // Toggle Active / Inactive
    // =====================================================

    if (
        isset($_GET['membership_plan_toggle'])
    ) {

        $id = (int) $_GET['membership_plan_toggle'];

        if ($id > 0) {

            $stmt = $pdo->prepare(
                "UPDATE membership_plans
                 SET
                    is_active =
                        CASE
                            WHEN is_active = 1
                            THEN 0
                            ELSE 1
                        END,
                    updated_at = NOW()
                 WHERE id = ?"
            );

            $stmt->execute([$id]);
        }
    }


    // =====================================================
    // Load Plans
    // =====================================================

    $stmt = $pdo->query(
        "SELECT *
         FROM membership_plans
         ORDER BY sort_order ASC, id ASC"
    );

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}


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
