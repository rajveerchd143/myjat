<?php
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}
// =========================================================
// Description:
// Website page for Membership Applications.
// This module will replace the WordPress admin listing
// after migration is complete.
// =========================================================
// =========================================================
// Search Box
// =========================================================
function myjat_membership_admin_page()
{
    global $wpdb;
    // =========================================================
    // Hide WordPress Admin Notices
    // =========================================================
    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
    remove_all_actions('network_admin_notices');
    $table = $wpdb->prefix . 'membership_applications';
    $search = sanitize_text_field($_GET['search_member'] ?? '');
    if (!empty($search)) {
        $applications = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT *
             FROM {$table}
             WHERE
             full_name LIKE %s
             OR mobile_no LIKE %s
             OR membership_no LIKE %s
             ORDER BY id DESC",
                "%{$search}%",
                "%{$search}%",
                "%{$search}%"
            )
        );
    } else {
        $applications = $wpdb->get_results(
            "SELECT * FROM {$table} ORDER BY id DESC"
        );
    }
?>
    <div>
        <div class="myjat-dashboard-header">
            <div class="myjat-dashboard-title">
                <h1>MYJAT Membership Management</h1>
                <p>Secure Membership Management Portal</p>
            </div>
           
        </div>
        <div class="myjat-grid myjat-grid-3 myjat-quick-actions">
            <a href="?page=myjat-membership-applications&export_csv=1"
                class="myjat-action-card">
                
                <span>📥</span>
                <strong>Export CSV</strong>
            </a>
            <a href="<?php echo home_url('/member-registration/'); ?>"
                target="_blank"
                class="myjat-action-card">
                <span>➕</span>
                <strong>New Application</strong>
            </a>
            <a href="<?php echo admin_url('admin.php?page=myjat-settings'); ?>"
                class="myjat-action-card">
                <span>⚙️</span>
                <strong>Settings</strong>
            </a>
        </div>
        <?php
        //search Bar and Counts in Admin View Pannel
        ?>
        <div class="myjat-card myjat-search-card">
            <form class="myjat-search-form" method="get">
                <input
                    type="hidden"
                    name="page"
                    value="myjat-membership-applications">
                <div class="myjat-search-box">
                    <span class="myjat-search-icon">🔍</span>
                    <input
                        class="myjat-search-input"
                        type="text"
                        name="search_member"
                        value="<?php echo esc_attr($_GET['search_member'] ?? ''); ?>"
                        placeholder="नाम, मोबाइल या ABJM नंबर खोजें">
                </div>
                <button class="myjat-primary myjat-btn">
                    Search
                </button>
            </form>
        </div>
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
            <button class="myjat-primary myjat-btn">
                Reset
            </button>
        </div>
        <?php
        $total_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        $pending_count = $wpdb->get_var("
    SELECT COUNT(*)
    FROM {$table}
    WHERE status='pending'
");
        $approved_count = $wpdb->get_var("
    SELECT COUNT(*)
    FROM {$table}
    WHERE status='Approved'
");
        $rejected_count = $wpdb->get_var("
    SELECT COUNT(*)
    FROM {$table}
    WHERE status='Rejected'
");
        ?>
        <div class="myjat-stats">
            <div class="myjat-stat-card">
                <div class="myjat-stat-icon">📄</div>
                <div class="myjat-stat-title">Total Application</div>
                <h2 class="myjat-stat-number">
                    <?php echo $total_count; ?>
                </h2>
            </div>
            <div class="myjat-stat-card">
                <div class="myjat-stat-icon">🟡</div>
                <div class="myjat-stat-title">Pending</div>
                <h2 class="myjat-stat-number">
                    <?php echo $pending_count; ?>
                </h2>
            </div>
            <div class="myjat-stat-card">
                <div class="myjat-stat-icon">✅</div>
                <div class="myjat-stat-title">Approved</div>
                <h2 class="myjat-stat-number">
                    <?php echo $approved_count; ?>
                </h2>
            </div>
            <div class="myjat-stat-card">
                <div class="myjat-stat-icon">❌</div>
                <div class="myjat-stat-title">Rejected</div>
                <h2 class="myjat-stat-number">
                    <?php echo $rejected_count; ?>
                </h2>
            </div>
        </div>
        <?php
        //search Bar and Counts in Admin View Pannel close
        //Karyavahi Sadysata avedan form approval in Admin View Pannel
        ?>
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
                            <td><?php echo esc_html($row->membership_no); ?></td>
                            <td><?php echo esc_html($row->full_name); ?></td>
                            <td><?php echo esc_html($row->mobile_no); ?></td>
                            <td><?php echo esc_html($row->district); ?></td>
                            <td><?php echo esc_html($row->membership_type); ?></td>
                            <td>
                                <?php
                                $status = strtolower($row->status);
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
                                <?php echo esc_html($row->application_date); ?>
                            </td>
                            <td>
                                <a
                                    class="myjat-btn"
                                    href="<?php echo home_url('/application-view/?id=' . $row->id); ?>"
                                    target="_blank">
                                    View
                                </a>
                                <a
                                    class="myjat-btn"
                                    href="<?php echo home_url('/member-card/?id=' . $row->id); ?>"
                                    target="_blank">
                                    PVC Card
                                </a>
                            </td>
                            <td>
                                <?php
                                if (strtolower($row->status) === 'pending') {
                                ?>
                                    <a
                                        class="myjat-btn"
                                        href="?page=myjat-membership-applications&myjat_action=approve&application_id=<?php echo $row->id; ?>">
                                        Approve
                                    </a>
                                    <a
                                        class="myjat-btn"
                                        href="?page=myjat-membership-applications&myjat_action=reject&application_id=<?php echo $row->id; ?>">
                                        Reject
                                    </a>
                                <?php
                                } elseif ($row->status == 'Approved') {
                                ?>
                                    <span class="myjat-badge myjat-badge-success">
                                        ✓ Approved
                                    </span>
                                <?php
                                } elseif ($row->status == 'Rejected') {
                                ?>
                                    <span class="myjat-badge myjat-badge-danger">
                                        ✗ Rejected
                                    </span>
                                <?php
                                }
                                ?>
                            </td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php
}
// =========================================================
// Description:
// Membership Applications Shortcode
// =========================================================
function myjat_membership_applications_shortcode()
{
    ob_start();
    myjat_membership_admin_page();
    return ob_get_clean();
}
add_shortcode(
    'jat_member_applications',
    'myjat_membership_applications_shortcode'
);
