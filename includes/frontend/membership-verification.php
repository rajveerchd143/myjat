<?php

// =========================================================
// Membership Verification
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

    ?>

    <h2>सदस्य सत्यापन</h2>

    <form method="post">

        <input
            type="text"
            name="membership_no"
            placeholder="ABJM-0000-000000"
            class="myjat-input myjat-verification-input"
            value="<?= htmlspecialchars($membership_no, ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit">
            Verify
        </button>

    </form>

    <br>

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

                <h3>✅ Valid Member</h3>

                <?php if (!empty($member->photo_url)): ?>

                    <p>
                        <img
                            src="<?= htmlspecialchars($member->photo_url, ENT_QUOTES, 'UTF-8') ?>"
                            class="myjat-verification-photo"
                            alt="Member Photo">
                    </p>

                <?php endif; ?>

                <p>
                    <strong>नाम:</strong>
                    <?= htmlspecialchars($member->full_name, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>सदस्य संख्या:</strong>
                    <?= htmlspecialchars($member->membership_no, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>जिला:</strong>
                    <?= htmlspecialchars($member->district, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>सदस्यता प्रकार:</strong>
                    <?= htmlspecialchars($member->membership_type, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>स्थिति:</strong>
                    <?= htmlspecialchars($member->status, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>सदस्य वर्ष:</strong>
                    <?= htmlspecialchars($member->member_since, ENT_QUOTES, 'UTF-8') ?>
                </p>

            </div>

            <?php

        } else {

            ?>

            <div class="myjat-verification-card myjat-verification-error">

                <h3>❌ Member Not Found</h3>

            </div>

            <?php
        }
    }

    return ob_get_clean();
}