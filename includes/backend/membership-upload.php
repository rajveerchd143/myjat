<?php

// =========================================================
// Membership Photo Upload
// =========================================================

function myjat_upload_member_photo()
{
    if (
        empty($_FILES['photo']) ||
        empty($_FILES['photo']['tmp_name'])
    ) {
        return '';
    }

    $file = $_FILES['photo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return '';
    }

    $image = getimagesize($file['tmp_name']);

    if ($image === false) {
        return '';
    }

    $allowed_types = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!in_array($image['mime'], $allowed_types, true)) {
        return '';
    }

    // Actual website root
    $upload_dir =
        rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')
        . DIRECTORY_SEPARATOR
        . 'uploads'
        . DIRECTORY_SEPARATOR
        . 'members';

    if (!is_dir($upload_dir)) {

        if (!mkdir($upload_dir, 0755, true)) {
            return '';
        }
    }

    $extension = strtolower(
        pathinfo(
            $file['name'],
            PATHINFO_EXTENSION
        )
    );

    $filename =
        'member_'
        . bin2hex(random_bytes(8))
        . '.'
        . $extension;

    $destination =
        $upload_dir
        . DIRECTORY_SEPARATOR
        . $filename;

    if (!move_uploaded_file(
        $file['tmp_name'],
        $destination
    )) {
        return '';
    }

    return '/uploads/members/' . $filename;
}