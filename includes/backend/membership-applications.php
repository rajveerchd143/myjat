<?php

declare(strict_types=1);

function myjat_membership_admin_page($pdo)
{
    ob_start();

    $search = trim(
        (string) ($_GET['search_member'] ?? '')
    );

    if ($search !== '') {

        $stmt = $pdo->prepare(
            "SELECT *
             FROM membership_applications
             WHERE
                full_name LIKE ?
                OR mobile_no LIKE ?
                OR membership_no LIKE ?
             ORDER BY id DESC"
        );

        $like = '%' . $search . '%';

        $stmt->execute([
            $like,
            $like,
            $like
        ]);

        $applications = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {

        $stmt = $pdo->query(
            "SELECT *
             FROM membership_applications
             ORDER BY id DESC"
        );

        $applications = $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    $total =
        myjat_total_membership_applications($pdo);

    $pending =
        myjat_total_pending_members($pdo);

    $approved =
        myjat_total_approved_members($pdo);

    $rejected =
        myjat_total_rejected_members($pdo);
?>
    <div class="admin-page">

        <section class="admin-heading myjat-glassblur">
            <h1>MYJAT Membership Management</h1>
            <p>Secure Membership Management Portal</p>
        </section>

        <section class="admin-tools">

            <form method="get" class="admin-search">

                <input
                    type="text"
                    name="search_member"
                    value="<?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                    placeholder="नाम, मोबाइल या ABJM नंबर खोजें">

                <button type="submit">
                    Search
                </button>

                <?php if ($search !== '') : ?>

                    <a href="/membership-admin/">
                        Reset
                    </a>

                <?php endif; ?>

            </form>

            <a
                href="?export_csv=1"
                class="admin-export">

                📥 Export CSV

            </a>

        </section>

        <section class="admin-stats">

            <div class="admin-stat myjat-glassblur">
                <span>📄</span>
                <small>Total Applications</small>
                <strong><?= (int) $total; ?></strong>
            </div>

            <div class="admin-stat myjat-glassblur">
                <span>🟡</span>
                <small>Pending</small>
                <strong><?= (int) $pending; ?></strong>
            </div>

            <div class="admin-stat myjat-glassblur">
                <span>✅</span>
                <small>Approved</small>
                <strong><?= (int) $approved; ?></strong>
            </div>

            <div class="admin-stat myjat-glassblur">
                <span>❌</span>
                <small>Rejected</small>
                <strong><?= (int) $rejected; ?></strong>
            </div>

        </section>

        <section class="admin-table-box">

            <div class="admin-table-scroll">

                <table class="admin-table  myjat-glassblur">

                    <thead>

                        <tr>
                            <th>ABJM No</th>
                            <th>नाम</th>
                            <th>मोबाइल</th>
                            <th>जिला</th>
                            <th>सदस्यता</th>
                            <th>स्थिति</th>
                            <th>दिनांक</th>
                            <th>विवरण</th>
                            <th>कार्यवाही</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($applications)) : ?>

                            <tr>
                                <td
                                    colspan="9"
                                    class="admin-empty">

                                    कोई आवेदन नहीं मिला।

                                </td>
                            </tr>

                        <?php else : ?>

                            <?php foreach ($applications as $row) : ?>

                                <?php
                                $status = strtolower(
                                    (string) ($row->status ?? '')
                                );
                                ?>
                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            $row->membership_no ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $row->full_name ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $row->mobile_no ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $row->district ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $row->membership_type ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>

                                        <?php if ($status === 'approved') : ?>

                                            <span class="status approved">
                                                Approved
                                            </span>

                                        <?php elseif ($status === 'rejected') : ?>

                                            <span class="status rejected">
                                                Rejected
                                            </span>

                                        <?php else : ?>

                                            <span class="status pending">
                                                Pending
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $row->application_date ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td class="admin-links">

                                        <a
                                            href="/application-view/?id=<?= (int) $row->id; ?>"
                                            target="_blank">

                                            View

                                        </a>

                                        <a
                                            href="/member-card/?id=<?= (int) $row->id; ?>"
                                            target="_blank">

                                            PVC Card

                                        </a>

                                    </td>

                                    <td class="admin-actions">

                                        <?php if ($status === 'pending') : ?>

                                            <a
                                                href="?myjat_action=approve&application_id=<?= (int) $row->id; ?>">

                                                Approve

                                            </a>

                                            <a
                                                href="?myjat_action=reject&application_id=<?= (int) $row->id; ?>">

                                                Reject

                                            </a>

                                        <?php elseif ($status === 'approved') : ?>

                                            <span class="action-ok">
                                                ✓ Approved
                                            </span>

                                        <?php elseif ($status === 'rejected') : ?>

                                            <span class="action-no">
                                                ✗ Rejected
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>
<?php

    return ob_get_clean();
}
