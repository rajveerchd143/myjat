
<?php

// =========================================================
// MYJAT — Membership Verification
// Standalone PHP Version
// =========================================================

function myjat_member_verify($pdo)
{
    ob_start();

    $membership_no = '';

    if (!empty($_GET['id'])) {
        $membership_no = trim($_GET['id']);
    } elseif (!empty($_POST['membership_no'])) {
        $membership_no = trim($_POST['membership_no']);
    }

    $escape = static function ($value) {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    };

    ?>

    <section class="myjat-verification-page">

        <header class="myjat-verification-header">
            <h2>सदस्य सत्यापन</h2>
            <p>अपनी सदस्य संख्या दर्ज करके सदस्यता की पुष्टि करें।</p>
        </header>

        <form
            method="post"
            class="myjat-verification-form"
            autocomplete="off"
        >

            <input
                type="text"
                name="membership_no"
                class="myjat-verification-input"
                placeholder="ABJM-2026-000008"
                value="<?= $escape($membership_no) ?>"
                aria-label="Membership Number"
                required
            >

            <button
                type="submit"
                class="myjat-verification-button"
            >
                सत्यापित करें
            </button>

        </form>

        <?php

        if (!empty($membership_no)) {

            $stmt = $pdo->prepare(
                "SELECT *
                 FROM membership_applications
                 WHERE membership_no = ?
                 AND status = 'Approved'
                 LIMIT 1"
            );

            $stmt->execute([$membership_no]);

            $member = $stmt->fetch(PDO::FETCH_OBJ);

            if ($member) {

                ?>

                <div class="myjat-verification-card myjat-verification-success">

                    <h3 class="myjat-verification-result-heading">
                        <span aria-hidden="true">✅</span>
                        Valid Member
                    </h3>

                    <div class="myjat-member-result">

                        <?php if (!empty($member->photo_url)): ?>

                            <div class="myjat-verification-photo-wrap">
                                <img
                                    src="<?= $escape($member->photo_url) ?>"
                                    class="myjat-verification-photo"
                                    alt="Member Photo"
                                >
                            </div>

                        <?php endif; ?>

                        <div class="myjat-member-details">

                            <div class="myjat-member-detail-row">
                                <span class="myjat-member-detail-label">
                                    नाम:
                                </span>
                                <span class="myjat-member-detail-value">
                                    <?= $escape($member->full_name) ?>
                                </span>
                            </div>

                            <div class="myjat-member-detail-row">
                                <span class="myjat-member-detail-label">
                                    सदस्य संख्या:
                                </span>
                                <span class="myjat-member-detail-value">
                                    <?= $escape($member->membership_no) ?>
                                </span>
                            </div>

                            <div class="myjat-member-detail-row">
                                <span class="myjat-member-detail-label">
                                    जिला:
                                </span>
                                <span class="myjat-member-detail-value">
                                    <?= $escape($member->district) ?>
                                </span>
                            </div>

                            <div class="myjat-member-detail-row">
                                <span class="myjat-member-detail-label">
                                    सदस्यता प्रकार:
                                </span>
                                <span class="myjat-member-detail-value">
                                    <?= $escape($member->membership_type) ?>
                                </span>
                            </div>

                            <div class="myjat-member-detail-row">
                                <span class="myjat-member-detail-label">
                                    स्थिति:
                                </span>
                                <span class="myjat-member-status">
                                    <?= $escape($member->status) ?>
                                </span>
                            </div>

                            <div class="myjat-member-detail-row">
                                <span class="myjat-member-detail-label">
                                    सदस्य वर्ष:
                                </span>
                                <span class="myjat-member-detail-value">
                                    <?= $escape($member->member_since) ?>
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

                <?php

            } else {

                ?>

                <div class="myjat-verification-card myjat-verification-error">

                    <h3>❌ Member Not Found</h3>

                    <p>
                        इस सदस्य संख्या से कोई approved member नहीं मिला।
                    </p>

                </div>

                <?php
            }
        }

        ?>

    </section>

    <?php

    return ob_get_clean();
}