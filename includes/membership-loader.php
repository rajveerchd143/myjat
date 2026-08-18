<?php

// =========================================================
// MYJAT Membership Management System
// Module: Loader
//
// Description:
// Central loader for all Membership Management System modules.
// Loads modules in dependency order.
//
// =========================================================

if (! defined('ABSPATH')) {
	exit;
}


// =========================================================
// Core Modules
// =========================================================
require_once __DIR__ . '/membership-config.php';
require_once __DIR__ . '/membership-utils.php';
require_once __DIR__ . '/membership-roles.php';
require_once __DIR__ . '/membership-database.php';
require_once __DIR__ . '/membership-number.php';
require_once __DIR__ . '/membership-upload.php';
require_once __DIR__ . '/services/location/location-loader.php';
require_once __DIR__ . '/membership-handler.php';


// =========================================================
// Shared Modules
// =========================================================

require_once __DIR__ . '/membership-assets.php';


// =========================================================
// Frontend Modules
// =========================================================

require_once __DIR__ . '/membership-form.php';
require_once __DIR__ . '/membership-verification.php';
require_once __DIR__ . '/membership-directory.php';
require_once __DIR__ . '/membership-view.php';
require_once __DIR__ . '/membership-pvc.php';
require_once __DIR__ . '/membership-applications.php';


// =========================================================
// Admin Modules
// =========================================================

require_once __DIR__ . '/membership-admin.php';
require_once __DIR__ . '/membership-export.php';



// =========================================================
// Google Login
// =========================================================
require_once __DIR__ . '/membership-google-login.php';
require_once __DIR__ . '/membership-auth.php';
require_once __DIR__ . '/membership-status.php';
//require_once __DIR__ . '/membership-google-debug.php';
require_once __DIR__ . '/membership-member-dashboard.php';
require_once __DIR__ . '/membership-admin-security.php';



// =========================================================
// Menu pages
// =========================================================

require_once __DIR__ . '/pages/home.php';
require_once __DIR__ . '/pages/estab.php';
require_once __DIR__ . '/pages/kalamse.php';
require_once __DIR__ . '/pages/about.php';
require_once __DIR__ . '/pages/contact.php';
require_once __DIR__ . '/pages/slider.php';


// =========================================================
// Great pages
// =========================================================
require_once __DIR__ . '/great/great-pages.php';

require_once __DIR__ . '/great/karmabai.php';
require_once __DIR__ . '/great/charan.php';
require_once __DIR__ . '/great/devilal.php';
require_once __DIR__ . '/great/dhanna.php';
require_once __DIR__ . '/great/ranjit.php';
require_once __DIR__ . '/great/surjmal.php';
require_once __DIR__ . '/great/tiket.php';
require_once __DIR__ . '/great/nahar.php';
require_once __DIR__ . '/great/mahender.php';
require_once __DIR__ . '/great/gokula.php';
require_once __DIR__ . '/great/tejaji.php';
require_once __DIR__ . '/great/bigaji.php';
require_once __DIR__ . '/great/bhagat.php';
require_once __DIR__ . '/great/chhotu.php';
require_once __DIR__ . '/great/sinhavlocan.php';
