<?php

function myjat_register_home()
{
    global $pdo;

    return myjat_membership_form($pdo);
}