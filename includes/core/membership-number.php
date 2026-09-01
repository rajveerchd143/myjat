<?php

// =========================================================
// Membership Number Generator
// Standalone PHP Version
// =========================================================

function myjat_generate_membership_number($pdo)
{
    $year = date('Y');

    $stmt = $pdo->prepare(
        "SELECT membership_no
         FROM membership_applications
         WHERE membership_no LIKE ?
         ORDER BY id DESC
         LIMIT 1"
    );

    $stmt->execute([
        'ABJM-' . $year . '-%'
    ]);

    $last_number = $stmt->fetchColumn();

    if (!empty($last_number)) {

        $parts = explode('-', $last_number);

        $last_sequence = (int) end($parts);

        $next = max(1, $last_sequence + 1);

    } else {

        $next = 1;
    }

    return sprintf(
        'ABJM-%s-%06d',
        $year,
        $next
    );
}