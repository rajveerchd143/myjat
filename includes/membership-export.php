<?php

// =========================================================
// Membership Export
// Standalone PHP Version
// =========================================================

function myjat_export_members_csv($pdo)
{
    if (empty($_GET['export_csv'])) {
        return;
    }

    $stmt = $pdo->query(
        "SELECT *
         FROM membership_applications
         ORDER BY id DESC"
    );

    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header(
        'Content-Disposition: attachment; filename=myjat-members.csv'
    );

    $output = fopen('php://output', 'w');

    if (!empty($members)) {

        fputcsv(
            $output,
            array_keys($members[0])
        );

        foreach ($members as $member) {

            fputcsv(
                $output,
                $member
            );
        }
    }

    fclose($output);

    exit;
}