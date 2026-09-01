<?php

// =========================================================
// Membership Photo Upload
// Standalone PHP Version
// =========================================================

function myjat_upload_member_photo()
{
    if (
        empty($_FILES['photo']) ||
        empty($_FILES['photo']['name'])
    ) {
        return '';
    }

    $allowed_types = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    $file = $_FILES['photo'];

    if (!empty($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return '';
    }

    $file_info = getimagesize($file['tmp_name']);

    if ($file_info === false) {
        return '';
    }

    $mime_type = $file_info['mime'];

    if (!in_array($mime_type, $allowed_types, true)) {
        return '';
    }

    $upload_dir = __DIR__ . '/../uploads/members';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    $filename = uniqid('member_', true) . '.' . $extension;

    $destination = $upload_dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return '';
    }

    return '/uploads/members/' . $filename;
}