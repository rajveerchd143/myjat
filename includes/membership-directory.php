<?php

// =========================================================
// Membership Directory
// Standalone PHP Version
// =========================================================

function myjat_members_directory($pdo)
{
    $stmt = $pdo->query(
        "SELECT *
         FROM membership_applications
         WHERE status = 'Approved'
         ORDER BY membership_no ASC"
    );

    $members = $stmt->fetchAll(PDO::FETCH_OBJ);

    ob_start();
    ?>

    <table class="myjat-members-directory">

        <thead>

            <tr>
                <th>ABJM No</th>
                <th>नाम</th>
                <th>जिला</th>
                <th>सदस्यता प्रकार</th>
                <th>सदस्य वर्ष</th>
            </tr>

        </thead>

        <tbody>

            <?php foreach ($members as $member): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $member->membership_no ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $member->full_name ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $member->district ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $member->membership_type ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $member->member_since ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <?php

    return ob_get_clean();
}