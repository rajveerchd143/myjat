<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Verification
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_shortcode('myjat_member_verify', function () {

    global $wpdb;

    $table = $wpdb->prefix . 'membership_applications';

    ob_start();

    echo '<h2>सदस्य सत्यापन</h2>';

    echo '
    <form method="post">
        <input
            type="text"
            name="membership_no"
            placeholder="ABJM-2026-000001"
            
            class="myjat-input myjat-verification-input"
            >

        <button type="submit">
            Verify
        </button>
    </form><br>';

    $membership_no = '';

if (!empty($_GET['id'])) {

    $membership_no = sanitize_text_field($_GET['id']);

} elseif (!empty($_POST['membership_no'])) {

    $membership_no = sanitize_text_field($_POST['membership_no']);

}

if (!empty($membership_no)) {

        $member = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT *
                 FROM {$table}
                 WHERE membership_no = %s
                 AND status = 'Approved'",
                $membership_no
            )
        );

        if ($member) {

            echo '<div class="myjat-verification-card myjat-verification-success">';

            echo '<h3>✅ Valid Member</h3>';
            if (!empty($member->photo_url)) {

    echo '<p>
    <img src="' . esc_url($member->photo_url) . '"

    class="myjat-verification-photo">
    </p>';

}
            echo '<p><strong>नाम:</strong> '
                 . esc_html($member->full_name)
                 . '</p>';

            echo '<p><strong>सदस्य संख्या:</strong> '
                 . esc_html($member->membership_no)
                 . '</p>';

            echo '<p><strong>जिला:</strong> '
                 . esc_html($member->district)
                 . '</p>';
                 
                 echo '<p><strong>सदस्यता प्रकार:</strong> '
     . esc_html($member->membership_type)
     . '</p>';

echo '<p><strong>स्थिति:</strong> '
     . esc_html($member->status)
     . '</p>';

            echo '<p><strong>सदस्य वर्ष:</strong> '
                 . esc_html($member->member_since)
                 . '</p>';

            echo '</div>';

        } else {

            echo '<div class="myjat-verification-card myjat-verification-error">';
            echo '<h3>❌ Member Not Found</h3>';
            echo '</div>';
        }
    }

    return ob_get_clean();
});




