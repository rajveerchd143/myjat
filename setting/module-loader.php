<?php

// =========================================================
// 01. PUBLIC PAGES
// =========================================================

require_once __DIR__ . '/../includes/pages/home.php';
require_once __DIR__ . '/../includes/pages/estab.php';
require_once __DIR__ . '/../includes/pages/kalamse.php';
require_once __DIR__ . '/../includes/pages/about.php';
require_once __DIR__ . '/../includes/pages/contact.php';
require_once __DIR__ . '/../includes/pages/slider.php';


// =========================================================
// 02. GREAT PAGES
// =========================================================

require_once __DIR__ . '/../includes/great/great-pages.php';

require_once __DIR__ . '/../includes/great/bhagat.php';
require_once __DIR__ . '/../includes/great/bigaji.php';
require_once __DIR__ . '/../includes/great/charan.php';
require_once __DIR__ . '/../includes/great/chhotu.php';
require_once __DIR__ . '/../includes/great/devilal.php';
require_once __DIR__ . '/../includes/great/dhanna.php';
require_once __DIR__ . '/../includes/great/gokula.php';
require_once __DIR__ . '/../includes/great/karmabai.php';
require_once __DIR__ . '/../includes/great/mahender.php';
require_once __DIR__ . '/../includes/great/nahar.php';
require_once __DIR__ . '/../includes/great/ranjit.php';
require_once __DIR__ . '/../includes/great/sinhavlocan.php';
require_once __DIR__ . '/../includes/great/surjmal.php';
require_once __DIR__ . '/../includes/great/tejaji.php';
require_once __DIR__ . '/../includes/great/tiket.php';


// =========================================================
// 03. AUTHENTICATION & USER SYSTEM
// =========================================================

//require_once __DIR__ . '/../includes/membership-auth.php';
//require_once __DIR__ . '/../includes/membership-google-login.php';
//require_once __DIR__ . '/../includes/membership-roles.php';
require_once __DIR__ . '/../includes/membership-status.php';
require_once __DIR__ . '/../includes/membership-member-dashboard.php';


// =========================================================
// 04. MEMBERSHIP CORE
// =========================================================

require_once __DIR__ . '/../includes/membership-config.php';
require_once __DIR__ . '/../includes/membership-database.php';
require_once __DIR__ . '/../includes/membership-utils.php';
require_once __DIR__ . '/../includes/membership-number.php';


// =========================================================
// 05. MEMBERSHIP APPLICATION
// =========================================================

require_once __DIR__ . '/../includes/membership-applications.php';
require_once __DIR__ . '/../includes/membership-form.php';
require_once __DIR__ . '/../includes/membership-handler.php';
require_once __DIR__ . '/../includes/membership-upload.php';


// =========================================================
// 06. LOCATION SYSTEM
// =========================================================

require_once __DIR__ . '/../includes/services/location/location-loader.php';


// =========================================================
// 07. MEMBER FRONTEND
// =========================================================

require_once __DIR__ . '/../includes/membership-verification.php';
require_once __DIR__ . '/../includes/membership-directory.php';
require_once __DIR__ . '/../includes/membership-view.php';
require_once __DIR__ . '/../includes/membership-pvc.php';


// =========================================================
// 08. ADMIN
// =========================================================

require_once __DIR__ . '/../includes/membership-admin-security.php';
require_once __DIR__ . '/../includes/membership-admin.php';
require_once __DIR__ . '/../includes/membership-export.php';