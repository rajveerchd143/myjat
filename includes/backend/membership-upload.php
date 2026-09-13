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

    if ($file['size'] > 5 * 1024 * 1024) {
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


// =========================================================
// TEMPORARY MEMBER PHOTO UPLOAD
// =========================================================

function myjat_upload_member_photo_temp()
{
    if (
        empty($_FILES['photo'])
        || !is_array($_FILES['photo'])
    ) {
        return false;
    }

    $file = $_FILES['photo'];

    // Upload error
    if (
        ($file['error'] ?? UPLOAD_ERR_NO_FILE)
        !== UPLOAD_ERR_OK
    ) {
        return false;
    }

    // Maximum 5 MB
    if (
        ($file['size'] ?? 0)
        > (5 * 1024 * 1024)
    ) {
        return false;
    }

    // Verify actual image
    $image_info =
        @getimagesize(
            $file['tmp_name']
        );

    if ($image_info === false) {
        return false;
    }

    // Allowed image types
    $allowed_types = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];

    $image_type =
        $image_info[2] ?? null;

    if (
        !isset(
            $allowed_types[$image_type]
        )
    ) {
        return false;
    }

    // =====================================================
    // Temporary Directory
    // =====================================================

    $temp_dir =
        MYJAT_ROOT .
        '/uploads/members/temp/';

    if (!is_dir($temp_dir)) {

        if (
            !mkdir(
                $temp_dir,
                0755,
                true
            )
        ) {
            return false;
        }
    }

    // =====================================================
    // Random Temporary Filename
    // =====================================================

    $filename =
        'temp_' .
        bin2hex(
            random_bytes(16)
        ) .
        '.' .
        $allowed_types[$image_type];

    $destination =
        $temp_dir .
        $filename;

    // =====================================================
    // Move Uploaded File
    // =====================================================

    if (
        !move_uploaded_file(
            $file['tmp_name'],
            $destination
        )
    ) {
        return false;
    }

    // Return server-side path.
    // This is intentionally NOT a public URL.

    return $destination;
}


// =========================================================
// MOVE TEMP MEMBER PHOTO TO FINAL LOCATION
// =========================================================

function myjat_move_temp_member_photo($temp_photo)
{
    $temp_photo =
        (string) $temp_photo;


    if (
        $temp_photo === ''
        || !is_file($temp_photo)
    ) {
        return false;
    }


    // =====================================================
    // Security Check
    // =====================================================

    $temp_dir =
        realpath(
            MYJAT_ROOT .
                '/uploads/members/temp/'
        );


    $real_temp_photo =
        realpath($temp_photo);


    if (
        $temp_dir === false
        || $real_temp_photo === false
    ) {
        return false;
    }


    /*
     * Make sure the supplied file actually belongs
     * to our temporary member-photo directory.
     */

    if (
        strpos(
            $real_temp_photo,
            $temp_dir . DIRECTORY_SEPARATOR
        ) !== 0
    ) {
        return false;
    }


    // =====================================================
    // Final Directory
    // =====================================================

    $final_dir =
        MYJAT_ROOT .
        '/uploads/members/';


    if (!is_dir($final_dir)) {

        if (!mkdir($final_dir, 0755, true)) {
            return false;
        }
    }


    // =====================================================
    // Preserve Extension
    // =====================================================

    $extension =
        strtolower(
            pathinfo(
                $real_temp_photo,
                PATHINFO_EXTENSION
            )
        );


    $allowed_extensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];


    if (
        !in_array(
            $extension,
            $allowed_extensions,
            true
        )
    ) {
        return false;
    }


    // =====================================================
    // Final Filename
    // =====================================================

    $filename =
        'member_' .
        bin2hex(random_bytes(16)) .
        '.' .
        $extension;


    $final_path =
        $final_dir .
        $filename;


    // =====================================================
    // Move File
    // =====================================================

    if (
        !rename(
            $real_temp_photo,
            $final_path
        )
    ) {
        return false;
    }


    // =====================================================
    // Public URL
    // =====================================================

    return
        '/uploads/members/' .
        $filename;
}
