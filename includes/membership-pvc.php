<?php

// =========================================================
// Description:
// Renders Member PVC Card.
// =========================================================
if (! defined('ABSPATH')) {
    exit;
}


function myjat_render_member_card($application)
{
    ob_start();


    // main pvc card front side
    echo '
<div class="myjat-pvc myjat-pvc-front">
<div class="myjat-pvc-back-watermark">

<img class="myjat-pvc-back-watermark-logo"
src="https://myjat.in/wp-content/uploads/2026/08/Logo.png">

</div>

    <div class="myjat-pvc-header">
    <div class="myjat-pvc-logo-wrap">
    
    <img class="myjat-pvc-logo"
    src="https://myjat.in/wp-content/uploads/2026/08/Logo.png">

    </div>
    
    <div class="myjat-pvc-header-content">

    <div class="myjat-pvc-title">
    अखिल भारतवर्षीय जाट महासभा
    </div>
    
   <div class="myjat-pvc-subtitle">    
    सदस्य पहचान पत्र
    <br>
    (Member Identity Card)
    </div>
    
    </div>
    
    </div>
    <div class="myjat-pvc-divider"></div>
    
    ';


    echo '<div class="myjat-pvc-body">';

    if (! empty($application->photo_url)) {

        echo '<img
class="myjat-pvc-photo"
src="' . esc_url($application->photo_url) . '">';
    } else {

        echo '<div class="myjat-pvc-photo-placeholder"></div>';
    }

    echo '<div class="myjat-pvc-header-content">';
    echo '<strong class="myjat-pvc-name">' . esc_html($application->full_name) . '</strong>
    
 <table class="myjat-pvc-table">
<tr>
    <td class="myjat-pvc-cell">
    🏷️ ABJM No<br>
    <strong>' . esc_html($application->membership_no) . '</strong>
    </td>
    
    <td class="myjat-pvc-cell">
    
    
    👥 Membership Type<br>
    <strong>' . esc_html($application->membership_type) . '</strong>
    </td>
    
    </tr>
    
    <tr>
    
  <td class="myjat-pvc-cell">  
    
    📍 District<br>
    <strong>' . esc_html($application->district) . '</strong>
    </td>
    <td class="myjat-pvc-cell">
    
    📅 Member Since<br>
    <strong>' . esc_html($application->member_since) . '</strong>
    </td>
    
    </tr>
    
    </table>
    ';

    echo '</div>'; // details div
    echo '</div>'; // photo/details flex div




    // Signatures

    echo '<div class="myjat-pvc-signatures">
    
    <!-- President -->
    
    <div class="myjat-pvc-sign myjat-pvc-sign-left">
    <img class="myjat-pvc-sign-image"
    src="https://myjat.in/wp-content/uploads/2026/08/President_Sign-1.png">
    

    <div class="myjat-pvc-sign-line"></div>
   
    <div class="myjat-pvc-sign-name">
    चौ० वीरेन्द्र सिंह
    </div>
    <div class="myjat-pvc-sign-post">
    राष्ट्रीय अध्यक्ष
    </div>
    </div>
    
    
    <!-- General Secretary -->
    
    <div class="myjat-pvc-sign myjat-pvc-sign-right">
    <img class="myjat-pvc-sign-image"
    src="https://myjat.in/wp-content/uploads/2026/06/general-secretary-sign.png">
    
    <div class="myjat-pvc-sign-line"></div>
    
    <div class="myjat-pvc-sign-name">
    चौ० शेरसिंह
    </div>
    <div class="myjat-pvc-sign-post">
    राष्ट्रीय महासचिव
    </div>
    </div>
    

    </div>
    
    ';


    echo '</div>';
    // main pvc card front side DIV CLOSE




    // main pvc card Back side
    echo '
    
    <br>
   <div class="myjat-pvc myjat-pvc-back">
    
        <!-- Watermark YAHAN -->
    
    <div class="myjat-pvc-watermark">
    <img
    class="myjat-pvc-back-watermark-logo"
    src="https://myjat.in/wp-content/uploads/2026/08/Logo.png">
    </div>
        
    <div class="myjat-pvc-back-title">
    अखिल भारतवर्षीय जाट महासभा
    <hr class="myjat-pvc-back-divider">
       
    </div>
        
    <!-- =====================================================
    QR Code Section
    Description:
    Displays large QR code for member verification.
    ===================================================== -->
    
    <div class="myjat-pvc-qr">
    <img class="myjat-pvc-qr-image"
    src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($application->qr_code) . '">
    
    <div class="myjat-pvc-qr-title">
    
    Scan to Verify
    
    </div>
    
    <div class="myjat-pvc-qr-url">
    www.myjat.in
    </div>
    
    <div class="myjat-pvc-back-footer">
    
    This card remains the property of
    
    <br>
    
    <strong>
    Akhil Bharatvarshiya Jat Mahasabha
    </strong>
    
    <br>
    
    If found, please return to the organization.
    
    <br>
    
    📞 Contact: <strong>+91 9412274738</strong>
    
    </div>
    
    </div>
</div>'; // main pvc card Back side Div Close

    return ob_get_clean();
}

add_shortcode('jat_member_card', 'myjat_member_card_shortcode');
function myjat_member_card_shortcode()
{

    if (empty($_GET['id'])) {
        return 'Member not found.';
    }

    $application = myjat_get_membership_application(intval($_GET['id']));

    if (! $application) {
        return 'Member not found.';
    }

    return myjat_render_member_card($application);
}

add_action('wp', function () {

    if (is_page('member-card')) {
        show_admin_bar(false);
    }
});
