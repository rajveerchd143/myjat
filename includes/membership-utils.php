<?php

// =========================================================
// Utilities
// Standalone PHP Version
// =========================================================


// =========================================================
// Get Membership Table Name
// =========================================================

function myjat_membership_table()
{
    return 'membership_applications';
}


// =========================================================
// Check Admin Screen
// =========================================================

function myjat_is_admin_page()
{
    if (empty($_GET['page'])) {
        return false;
    }

    return $_GET['page'] === 'myjat-membership-applications';
}


// =========================================================
// Get Current Member
// =========================================================

function myjat_get_member($pdo, $id)
{
    $stmt = $pdo->prepare(
        "SELECT *
         FROM " . myjat_membership_table() . "
         WHERE id = ?"
    );

    $stmt->execute([(int) $id]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}


// =========================================================
// Get Application By Membership Number
// =========================================================

function myjat_get_member_by_number($pdo, $membership_number)
{
    $stmt = $pdo->prepare(
        "SELECT *
         FROM " . myjat_membership_table() . "
         WHERE membership_number = ?"
    );

    $stmt->execute([
        trim($membership_number)
    ]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}


// =========================================================
// Get Application By ID
// =========================================================

function myjat_get_membership_application($pdo, $id)
{
    $stmt = $pdo->prepare(
        "SELECT *
         FROM " . myjat_membership_table() . "
         WHERE id = ?"
    );

    $stmt->execute([
        (int) $id
    ]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}


// =========================================================
// Get All Applications
// =========================================================

function myjat_get_all_membership_applications($pdo, $order = 'DESC')
{
    $order = strtoupper($order) === 'ASC'
        ? 'ASC'
        : 'DESC';

    $stmt = $pdo->query(
        "SELECT *
         FROM " . myjat_membership_table() . "
         ORDER BY id {$order}"
    );

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}