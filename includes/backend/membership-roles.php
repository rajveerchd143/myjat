<?php

// =========================================================
// MYJAT Roles & Access Control
// =========================================================


function myjat_secretary_admin_access()
{
    return myjat_is_secretary();
}


function myjat_can($capability)
{
    if (!myjat_is_secretary()) {
        return false;
    }

    $capabilities = [

        'myjat_access',
        'myjat_manage_applications',
        'myjat_view_application',
        'myjat_export_applications',
        'myjat_approve_application',
        'myjat_reject_application',
        'myjat_print_pvc',
        'myjat_mark_card_printed',
                    
    ];

    return in_array(
        $capability,
        $capabilities,
        true
    );
}