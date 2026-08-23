<?php

// =========================================================
// MYJAT Membership Admin Page
// Standalone PHP Version
// =========================================================

function myjat_membership_admin_page($pdo)
{
    $search = trim(
        $_GET['search_member'] ?? ''
    );

    // =====================================================
    // Applications
    // =====================================================

    if (!empty($search)) {

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

    ?>

    <div>

        <div class="myjat-dashboard-header">

            <div class="myjat-dashboard-title">

                <h1>
                    MYJAT Membership Management
                </h1>

                <p>
                    Secure Membership Management Portal
                </p>

            </div>

        </div>


        <div class="myjat-grid myjat-grid-3 myjat-quick-actions">

            <a
                href="?export_csv=1"
                class="myjat-action-card">

                <span>📥</span>

                <strong>
                    Export CSV
                </strong>

            </a>


            <a
                href="/member-registration/"
                target="_blank"
                class="myjat-action-card">

                <span>➕</span>

                <strong>
                    New Application
                </strong>

            </a>


            <a
                href="/settings/"
                class="myjat-action-card">

                <span>⚙️</span>

                <strong>
                    Settings
                </strong>

            </a>

        </div>

                <!-- =================================================
             Search
        ================================================== -->

        <div class="myjat-card myjat-search-card">

            <form
                class="myjat-search-form"
                method="get">

                <div class="myjat-search-box">

                    <span class="myjat-search-icon">
                        🔍
                    </span>

                    <input
                        class="myjat-search-input"
                        type="text"
                        name="search_member"
                        value="<?= htmlspecialchars(
                            $search,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        placeholder="नाम, मोबाइल या ABJM नंबर खोजें">

                </div>

                <button
                    class="myjat-primary myjat-btn"
                    type="submit">

                    Search

                </button>

            </form>

        </div>


        <!-- =================================================
             Filters
        ================================================== -->

        <div class="myjat-card myjat-filter-bar">

            <select class="myjat-select myjat-filter">

                <option>Status</option>
                <option>Pending</option>
                <option>Approved</option>
                <option>Rejected</option>

            </select>


            <select class="myjat-select myjat-filter">

                <option>Membership Type</option>
                <option>साधारण सदस्य</option>
                <option>सक्रिय सदस्य</option>
                <option>संरक्षक सदस्य</option>

            </select>


            <button
                type="button"
                class="myjat-primary myjat-btn">

                Reset

            </button>

        </div>


        <?php

        // =====================================================
        // Membership Counts
        // =====================================================

        $total_count =
            myjat_total_membership_applications($pdo);

        $pending_count =
            myjat_total_pending_members($pdo);

        $approved_count =
            myjat_total_approved_members($pdo);

        $rejected_count =
            myjat_total_rejected_members($pdo);

        ?>


        <div class="myjat-stats">

            <div class="myjat-stat-card">

                <div class="myjat-stat-icon">
                    📄
                </div>

                <div class="myjat-stat-title">
                    Total Application
                </div>

                <h2 class="myjat-stat-number">
                    <?= $total_count; ?>
                </h2>

            </div>


            <div class="myjat-stat-card">

                <div class="myjat-stat-icon">
                    🟡
                </div>

                <div class="myjat-stat-title">
                    Pending
                </div>

                <h2 class="myjat-stat-number">
                    <?= $pending_count; ?>
                </h2>

            </div>


            <div class="myjat-stat-card">

                <div class="myjat-stat-icon">
                    ✅
                </div>

                <div class="myjat-stat-title">
                    Approved
                </div>

                <h2 class="myjat-stat-number">
                    <?= $approved_count; ?>
                </h2>

            </div>


            <div class="myjat-stat-card">

                <div class="myjat-stat-icon">
                    ❌
                </div>

                <div class="myjat-stat-title">
                    Rejected
                </div>

                <h2 class="myjat-stat-number">
                    <?= $rejected_count; ?>
                </h2>

            </div>

        </div>

                <!-- =================================================
             Applications Table
        ================================================== -->

        <div class="myjat-table-wrapper">

            <table class="myjat-table">

                <thead>

                    <tr>
                        <th>ABJM No</th>
                        <th>नाम</th>
                        <th>मोबाइल</th>
                        <th>जिला</th>
                        <th>सदस्यता प्रकार</th>
                        <th>स्थिति</th>
                        <th>दिनांक</th>
                        <th>विवरण</th>
                        <th>कार्यवाही</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($applications as $row) : ?>

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

                                <?php

                                $status = strtolower(
                                    $row->status ?? ''
                                );

                                if ($status === 'approved') {

                                    echo '<span class="myjat-badge myjat-approved">Approved</span>';

                                } elseif ($status === 'rejected') {

                                    echo '<span class="myjat-badge myjat-rejected">Rejected</span>';

                                } else {

                                    echo '<span class="myjat-badge myjat-pending">Pending</span>';

                                }

                                ?>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $row->application_date ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </td>

                            <td>

                                <a
                                    class="myjat-btn"
                                    href="/application-view/?id=<?= (int) $row->id; ?>"
                                    target="_blank">

                                    View

                                </a>

                                <a
                                    class="myjat-btn"
                                    href="/member-card/?id=<?= (int) $row->id; ?>"
                                    target="_blank">

                                    PVC Card

                                </a>

                            </td>

                            <td>

                                <?php if ($status === 'pending') : ?>

                                    <a
                                        class="myjat-btn"
                                        href="?myjat_action=approve&application_id=<?= (int) $row->id; ?>">

                                        Approve

                                    </a>

                                    <a
                                        class="myjat-btn"
                                        href="?myjat_action=reject&application_id=<?= (int) $row->id; ?>">

                                        Reject

                                    </a>

                                <?php elseif ($status === 'approved') : ?>

                                    <span class="myjat-badge myjat-badge-success">

                                        ✓ Approved

                                    </span>

                                <?php elseif ($status === 'rejected') : ?>

                                    <span class="myjat-badge myjat-badge-danger">

                                        ✗ Rejected

                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <?php
}