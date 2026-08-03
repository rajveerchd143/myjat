<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =========================================================
// Description:
// Render Membership Application View
// =========================================================

function myjat_render_application_view( $application ) {

    ob_start();

    global $wpdb;

    $table = myjat_membership_table();
    // admin view form 

echo '
<div class="myjat-view-page">

    <div class="myjat-view-header">

        <div class="myjat-view-header-left">

            <span class="myjat-view-subtitle">
                Membership Application
            </span>

            <h1>' . esc_html( $application->full_name ) . '</h1>

            <div class="myjat-member-meta">

                <span class="myjat-member-no">
                    ' . esc_html( $application->membership_no ) . '
                </span>

                <span class="myjat-status-badge status-' . strtolower( esc_attr( $application->status ) ) . '">
                    ' . esc_html( $application->status ) . '
                </span>

            </div>

        </div>

        <div class="myjat-view-actions">

            <a href="?page=myjat-membership-applications"
               class="myjat-btn myjat-btn-light">
                ← Back
            </a>

            <a href="' . home_url( '/member-card/?id=' . $application->id ) . '"
               target="_blank"
               class="myjat-btn myjat-btn-primary">
                PVC Card
            </a>

            <button
                type="button"
                onclick="window.print();"
                class="myjat-btn myjat-btn-dark">

                Print

            </button>

        </div>

    </div>
';


// =========================================================
// Main Content Start
// =========================================================

echo '<div class="myjat-view-content">';

// =========================================================
// Photo Section
// =========================================================

echo '<div class="myjat-photo-section">';
echo '<div class="myjat-photo-card">';
if ( ! empty( $application->photo_url ) ) {
    echo '<img src="' . esc_url( $application->photo_url ) . '" class="myjat-member-photo">';
} else {
    echo '<div class="myjat-no-photo">No Photo</div>';
}
echo '<div class="myjat-photo-member-no">' . esc_html( $application->membership_no ) . '</div>';
echo '</div>';
echo '<div class="myjat-card myjat-photo-editor">';
echo '<h3>Member Photo</h3>';
echo '<form method="post">';
echo '<input type="hidden" name="member_id" value="' . $application->id . '">';
echo '<input type="text" name="photo_url" class="myjat-photo-input" value="' . esc_attr( $application->photo_url ) . '" placeholder="Photo URL">';
echo '<button type="submit" class="myjat-btn myjat-btn-primary">Save Photo</button>';
echo '</form>';
echo '</div>';
echo '</div>';

// =========================================================
// Right Column Start
// =========================================================
echo '<div class="myjat-details-column">';
// =========================================================
// Personal Information
// =========================================================

echo '<div class="myjat-card myjat-info-card">';
echo '<div class="myjat-card-title">👤 Personal Information</div>';
echo '<div class="myjat-info-grid-2">';
echo '<div class="myjat-field"><label>नाम</label><div>' . esc_html( $application->full_name ) . '</div></div>';
echo '<div class="myjat-field"><label>पिता / पति का नाम</label><div>' . esc_html( $application->father_husband_name ) . '</div></div>';
echo '<div class="myjat-field"><label>लिंग</label><div>' . esc_html( $application->gender ) . '</div></div>';
echo '<div class="myjat-field"><label>जन्मतिथि</label><div>' . esc_html( $application->dob ) . '</div></div>';
echo '<div class="myjat-field"><label>शिक्षा</label><div>' . esc_html( $application->education ) . '</div></div>';
echo '<div class="myjat-field"><label>व्यवसाय</label><div>' . esc_html( $application->profession ) . '</div></div>';
echo '</div>';
echo '</div>';

// =========================================================
// Right Column & Main Grid End
// =========================================================

echo '</div>';
echo '</div>';

// =========================================================
// Address Information
// =========================================================

echo '<div class="myjat-card myjat-info-card">';
echo '<div class="myjat-card-title">📍 Address Information</div>';
echo '<div class="myjat-info-grid-2">';
echo '<div class="myjat-field"><label>राज्य</label><div>' . esc_html( $application->state_name ) . '</div></div>';
echo '<div class="myjat-field"><label>जिला</label><div>' . esc_html( $application->district ) . '</div></div>';
echo '<div class="myjat-field"><label>ग्राम / पंचायत</label><div>' . esc_html( $application->village_panchayat ) . '</div></div>';
echo '<div class="myjat-field"><label>ब्लॉक</label><div>' . esc_html( $application->block_name ) . '</div></div>';
echo '<div class="myjat-field"><label>विधानसभा</label><div>' . esc_html( $application->vidhansabha ) . '</div></div>';
echo '<div class="myjat-field"><label>लोकसभा</label><div>' . esc_html( $application->loksabha ) . '</div></div>';
echo '<div class="myjat-field"><label>पिनकोड</label><div>' . esc_html( $application->pincode ) . '</div></div>';
echo '<div class="myjat-field"><label>मोबाइल</label><div>' . esc_html( $application->mobile_no ) . '</div></div>';
echo '<div class="myjat-field"><label>ईमेल</label><div>' . esc_html( $application->email ) . '</div></div>';
echo '<div class="myjat-field myjat-field-full"><label>वर्तमान पता</label><div>' . esc_html( $application->current_address ) . '</div></div>';
echo '<div class="myjat-field myjat-field-full"><label>स्थायी पता</label><div>' . esc_html( $application->permanent_address ) . '</div></div>';
echo '</div>';
echo '</div>';
// =========================================================
// Membership Information
// =========================================================

echo '<div class="myjat-card myjat-info-card">';
echo '<div class="myjat-card-title">🪪 Membership Information</div>';
echo '<div class="myjat-info-grid-2">';
echo '<div class="myjat-field"><label>Membership No.</label><div>' . esc_html( $application->membership_no ) . '</div></div>';
echo '<div class="myjat-field"><label>सदस्यता प्रकार</label><div>' . esc_html( $application->membership_type ) . '</div></div>';
echo '<div class="myjat-field"><label>स्थिति</label><div><span class="myjat-status-badge status-' . strtolower( $application->status ) . '">' . esc_html( $application->status ) . '</span></div></div>';
echo '<div class="myjat-field"><label>स्वीकृति दिनांक</label><div>' . esc_html( $application->approval_date ) . '</div></div>';
echo '<div class="myjat-field"><label>स्वीकृत किया</label><div>' . esc_html( $application->approved_by ) . '</div></div>';
echo '<div class="myjat-field"><label>सदस्य वर्ष</label><div>' . esc_html( $application->member_since ) . '</div></div>';
echo '</div>';
echo '</div>';
// =========================================================
// Administration Information
// =========================================================

echo '<div class="myjat-card myjat-info-card">';
echo '<div class="myjat-card-title">⚙️ Administration</div>';
echo '<div class="myjat-info-grid-2">';

echo '<div class="myjat-field">';
echo '<label>कार्ड प्रिंट स्थिति</label>';
echo '<div>';
if ( $application->card_printed == 'Yes' ) {
    echo '<span class="myjat-status-badge status-approved">Printed</span>';
} else {
    echo '<span class="myjat-status-badge status-pending">Not Printed</span>';
}
echo '</div>';
echo '</div>';

echo '<div class="myjat-field">';
echo '<label>Application ID</label>';
echo '<div>' . esc_html( $application->id ) . '</div>';
echo '</div>';

echo '</div>';

if ( $application->card_printed != 'Yes' ) {
echo '<div class="myjat-view-actions">';
echo '<a class="myjat-btn myjat-btn-primary" href="?page=myjat-membership-applications&mark_card_printed=' . $application->id . '">Mark Card Printed</a>';
    echo '</div>';
}

echo '</div>';
// =========================================================
// View Page End
// =========================================================

echo '</div>';

return ob_get_clean();
}


// =========================================================
// Description:
// Application View Shortcode
// =========================================================

function myjat_application_view_shortcode() {

    if ( empty( $_GET['id'] ) ) {
        return 'Application not found.';
    }

    $application = myjat_get_membership_application( intval( $_GET['id'] ) );

    if ( ! $application ) {
        return 'Application not found.';
    }

    return myjat_render_application_view( $application );
}

add_shortcode( 'jat_application_view', 'myjat_application_view_shortcode' );

    


