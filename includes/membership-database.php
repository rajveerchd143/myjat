<?php

// =========================================================
// Database
// Standalone PHP Version
// =========================================================


// =========================================================
// Insert Membership Application
// =========================================================

function myjat_save_membership_application($pdo, $data)
{
    if (empty($data)) {
        return false;
    }

    $columns = array_keys($data);

    $fields = implode(
        ', ',
        array_map(
            fn($column) => "`{$column}`",
            $columns
        )
    );

    $placeholders = implode(
        ', ',
        array_fill(0, count($columns), '?')
    );

    $sql = "
        INSERT INTO membership_applications
        ({$fields})
        VALUES
        ({$placeholders})
    ";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute(
        array_values($data)
    );
}


// =========================================================
// Update Membership Application
// =========================================================

function myjat_update_membership_application(
    $pdo,
    $id,
    $data
) {
    if (empty($data)) {
        return false;
    }

    $sets = [];

    foreach (array_keys($data) as $column) {
        $sets[] = "`{$column}` = ?";
    }

    $sql = "
        UPDATE membership_applications
        SET " . implode(', ', $sets) . "
        WHERE id = ?
    ";

    $values = array_values($data);
    $values[] = (int) $id;

    $stmt = $pdo->prepare($sql);

    return $stmt->execute($values);
}


// =========================================================
// Delete Application
// =========================================================

function myjat_delete_membership_application(
    $pdo,
    $id
) {
    $stmt = $pdo->prepare(
        "DELETE FROM membership_applications
         WHERE id = ?"
    );

    return $stmt->execute([
        (int) $id
    ]);
}


// =========================================================
// Search Applications
// =========================================================

function myjat_search_membership_applications(
    $pdo,
    $keyword
) {
    $keyword = '%' . trim($keyword) . '%';

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE
            full_name LIKE ?
            OR father_name LIKE ?
            OR membership_number LIKE ?
            OR district LIKE ?
         ORDER BY id DESC"
    );

    $stmt->execute([
        $keyword,
        $keyword,
        $keyword,
        $keyword
    ]);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}


// =========================================================
// Count Applications
// =========================================================

function myjat_total_membership_applications($pdo)
{
    return (int) $pdo->query(
        "SELECT COUNT(*)
         FROM membership_applications"
    )->fetchColumn();
}


// =========================================================
// Count Approved Members
// =========================================================

function myjat_total_approved_members($pdo)
{
    return (int) $pdo->query(
        "SELECT COUNT(*)
         FROM membership_applications
         WHERE status = 'approved'"
    )->fetchColumn();
}


// =========================================================
// Count Pending Members
// =========================================================

function myjat_total_pending_members($pdo)
{
    return (int) $pdo->query(
        "SELECT COUNT(*)
         FROM membership_applications
         WHERE status = 'pending'"
    )->fetchColumn();
}


// =========================================================
// Count Rejected Members
// =========================================================

function myjat_total_rejected_members($pdo)
{
    return (int) $pdo->query(
        "SELECT COUNT(*)
         FROM membership_applications
         WHERE status = 'rejected'"
    )->fetchColumn();
}