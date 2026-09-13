<?php

declare(strict_types=1);


/* =========================================================
   Membership Admin Page
   ========================================================= */

function myjat_membership_admin_page($pdo)
{
    $admin_tab = $_GET['tab'] ?? 'members';

    /*
     * Allowed Admin Tabs
     */
    $allowed_tabs = [
        'members',
        'gallery',
        'organization',
        'settings'
    ];

    if (!in_array($admin_tab, $allowed_tabs, true)) {
        $admin_tab = 'members';
    }


    /*
     * Gallery Actions
     */
    if ($admin_tab === 'gallery') {
        myjat_process_gallery_actions($pdo);
    }


    /*
     * Members Data
     */
    $search = trim(
        (string) ($_GET['search_member'] ?? '')
    );

    $applications = [];

    if ($admin_tab === 'members') {

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

            $applications =
                $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {

            $stmt = $pdo->query(
                "SELECT *
                 FROM membership_applications
                 ORDER BY id DESC"
            );

            $applications =
                $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }


    /*
     * Member Statistics
     */
    $total =
        myjat_total_membership_applications($pdo);

    $pending =
        myjat_total_pending_members($pdo);

    $approved =
        myjat_total_approved_members($pdo);

    $rejected =
        myjat_total_rejected_members($pdo);


    /*
     * Gallery Data
     */
    $gallery_items = [];

    if ($admin_tab === 'gallery') {

        $stmt = $pdo->query(
            "SELECT
                id,
                image,
                caption,
                status,
                created_at
             FROM gallery
             ORDER BY id DESC"
        );

        $gallery_items =
            $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    ob_start();

?>

    <div class="admin-page">

        <!-- =====================================================
         MAIN ADMIN HEADER
         ===================================================== -->

        <section class="admin-heading myjat-glassblur">

            <h1>
                MYJAT Membership Management
            </h1>

            <p>
                Secure Membership Management Portal
            </p>

        </section>


        <!-- =====================================================
         ADMIN TABS
         ===================================================== -->

        <nav
            class="myjat-admin-tabs"
            aria-label="Admin Sections">

            <a
                href="/membership-admin/?tab=members"
                class="myjat-admin-tab myjat-glassblur <?= $admin_tab === 'members' ? 'active' : ''; ?>">
                👥 Members
            </a>


            <a
                href="/membership-admin/?tab=gallery"
                class="myjat-admin-tab myjat-glassblur <?= $admin_tab === 'gallery' ? 'active' : ''; ?>">
                📷 Gallery
            </a>


            <a
                href="/membership-admin/?tab=organization"
                class="myjat-admin-tab myjat-glassblur <?= $admin_tab === 'organization' ? 'active' : ''; ?>">
                🏢 Organization
            </a>


            <a
                href="/membership-admin/?tab=settings"
                class="myjat-admin-tab myjat-glassblur <?= $admin_tab === 'settings' ? 'active' : ''; ?>">
                ⚙ Settings
            </a>

        </nav>


        <!-- =====================================================
         MEMBERS
         ===================================================== -->

        <?php if ($admin_tab === 'members') : ?>

            <section class="admin-content-wrapper myjat-glassblur">

                <div class="admin-section-title">

                    <h2>
                        👥 सदस्य प्रबंधन
                    </h2>

                    <p>
                        सदस्यता आवेदन, सदस्य विवरण और आवेदन की स्थिति manage करें।
                    </p>

                </div>


                <!-- Members Tools -->

                <div class="admin-tools">

                    <form
                        method="get"
                        class="admin-search">

                        <input
                            type="hidden"
                            name="tab"
                            value="members">

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

                            <a href="/membership-admin/?tab=members">
                                Reset
                            </a>

                        <?php endif; ?>

                    </form>


                    <a
                        href="?export_csv=1"
                        class="admin-export">
                        📥 Export CSV
                    </a>

                </div>


                <!-- Statistics -->

                <section class="admin-stats">

                    <div class="admin-stat">

                        <span>📄</span>

                        <small>
                            Total Applications
                        </small>

                        <strong>
                            <?= (int) $total; ?>
                        </strong>

                    </div>


                    <div class="admin-stat">

                        <span>🟡</span>

                        <small>
                            Pending
                        </small>

                        <strong>
                            <?= (int) $pending; ?>
                        </strong>

                    </div>


                    <div class="admin-stat">

                        <span>✅</span>

                        <small>
                            Approved
                        </small>

                        <strong>
                            <?= (int) $approved; ?>
                        </strong>

                    </div>


                    <div class="admin-stat">

                        <span>❌</span>

                        <small>
                            Rejected
                        </small>

                        <strong>
                            <?= (int) $rejected; ?>
                        </strong>

                    </div>

                </section>


                <!-- Members Table -->

                <section class="admin-table-box">

                    <div class="admin-table-scroll">

                        <table class="admin-table">

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
                                            (string) (
                                                $row->status ?? ''
                                            )
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

                                                <?php if (
                                                    $status === 'approved'
                                                ) : ?>

                                                    <span class="status approved">
                                                        Approved
                                                    </span>

                                                <?php elseif (
                                                    $status === 'rejected'
                                                ) : ?>

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

                                                <?php if (
                                                    $status === 'pending'
                                                ) : ?>

                                                    <a
                                                        href="?tab=members&myjat_action=approve&application_id=<?= (int) $row->id; ?>">
                                                        Approve
                                                    </a>


                                                    <a
                                                        href="?tab=members&myjat_action=reject&application_id=<?= (int) $row->id; ?>">
                                                        Reject
                                                    </a>

                                                <?php elseif (
                                                    $status === 'approved'
                                                ) : ?>

                                                    <span class="action-ok">
                                                        ✓ Approved
                                                    </span>

                                                <?php elseif (
                                                    $status === 'rejected'
                                                ) : ?>

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

            </section>


            <!-- =====================================================
         GALLERY
         ===================================================== -->

        <?php elseif ($admin_tab === 'gallery') : ?>

            <section class="admin-content-wrapper myjat-glassblur">

                <div class="admin-section-title">

                    <h2>
                        📷 फोटो गैलरी
                    </h2>

                    <p>
                        Homepage पर दिखाई देने वाली तस्वीरों को manage करें।
                    </p>

                </div>

                <!-- Upload -->

                <form
                    method="post"
                    enctype="multipart/form-data"
                    class="admin-gallery-upload">

                    <input
                        type="hidden"
                        name="myjat_gallery_action"
                        value="upload">


                    <div class="admin-gallery-field">

                        <label for="gallery_image">
                            Photo
                        </label>

                        <input
                            type="file"
                            id="gallery_image"
                            name="gallery_image"
                            accept="image/jpeg,image/png,image/webp"
                            required>

                    </div>


                    <div class="admin-gallery-field">

                        <label for="gallery_caption">
                            Caption
                        </label>

                        <input
                            type="text"
                            id="gallery_caption"
                            name="gallery_caption"
                            placeholder="Photo caption लिखें...">

                    </div>


                    <button type="submit">
                        Upload Photo
                    </button>

                </form>

                <!-- Gallery Cards -->

                <?php if (empty($gallery_items)) : ?>

                    <div class="admin-empty">
                        अभी कोई photo upload नहीं की गई है।
                    </div>

                <?php else : ?>

                    <div class="myjat-gallery-admin-grid">

                        <?php foreach ($gallery_items as $gallery) : ?>

                            <article class="myjat-gallery-admin-card">

                                <div class="myjat-gallery-admin-image">

                                    <img
                                        src="<?= htmlspecialchars(
                                                    $gallery->image ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                        alt="">

                                </div>


                                <div class="myjat-gallery-admin-info">

                                    <form
                                        method="post"
                                        class="myjat-gallery-caption-form">

                                        <input
                                            type="hidden"
                                            name="myjat_gallery_action"
                                            value="update_caption">

                                        <input
                                            type="hidden"
                                            name="gallery_id"
                                            value="<?= (int) $gallery->id; ?>">

                                        <input
                                            type="text"
                                            name="gallery_caption"
                                            value="<?= htmlspecialchars(
                                                        $gallery->caption ?? '',
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>"
                                            placeholder="Photo caption लिखें...">

                                        <button type="submit">
                                            Save
                                        </button>

                                    </form>


                                    <div class="myjat-gallery-admin-meta">

                                        <?php if (
                                            ($gallery->status ?? '') === 'active'
                                        ) : ?>

                                            <span class="status approved">
                                                Active
                                            </span>

                                        <?php else : ?>

                                            <span class="status pending">
                                                Hidden
                                            </span>

                                        <?php endif; ?>


                                        <span>
                                            <?= htmlspecialchars(
                                                $gallery->created_at ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </span>

                                    </div>


                                    <div class="myjat-gallery-admin-actions">

                                        <a
                                            href="?tab=gallery&gallery_toggle=<?= (int) $gallery->id; ?>">
                                            <?= (
                                                ($gallery->status ?? '') === 'active'
                                            )
                                                ? 'Hide'
                                                : 'Show';
                                            ?>
                                        </a>


                                        <a
                                            href="?tab=gallery&gallery_delete=<?= (int) $gallery->id; ?>"
                                            onclick="return confirm('क्या आप यह photo delete करना चाहते हैं?');">
                                            Delete
                                        </a>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>


            <!-- =====================================================
         ORGANIZATION
         ===================================================== -->

        <?php elseif ($admin_tab === 'organization') : ?>

            <section class="admin-content-wrapper myjat-glassblur">

                <div class="admin-section-title">

                    <h2>
                        🏢 संगठन प्रबंधन
                    </h2>

                    <p>
                        संगठन की संरचना और पदों की जानकारी यहाँ manage की जाएगी।
                    </p>

                </div>


                <div class="myjat-admin-placeholder">

                    Organization management यहाँ बाद में जोड़ा जाएगा।

                </div>

            </section>


            <!-- =====================================================
         SETTINGS
         ===================================================== -->

        <?php elseif ($admin_tab === 'settings') : ?>

            <?php
            $membership_plans =
                myjat_membership_plans_admin($pdo);

            $edit_plan_id = (int) (
                $_GET['membership_plan_edit'] ?? 0
            );

            $edit_plan = null;

            if ($edit_plan_id > 0) {

                foreach ($membership_plans as $plan) {

                    if ((int) $plan->id === $edit_plan_id) {

                        $edit_plan = $plan;

                        break;
                    }
                }
            }




            ?>

            <section class="admin-content-wrapper myjat-glassblur">

                <div class="admin-section-title">

                    <h2>
                        ⚙️ Membership Plans
                    </h2>

                    <p>
                        सदस्यता प्रकार, शुल्क, validity और membership rules manage करें।
                    </p>

                </div>


                <!-- Add Plan -->

                <div class="admin-plan-form">

                    <h3>
                        <?= $edit_plan
                            ? 'Membership Plan Edit करें'
                            : 'नया Membership Plan'; ?>
                    </h3>

                    <form method="post">

                        <input
                            type="hidden"
                            name="membership_plan_action"
                            value="save">

                        <input
                            type="hidden"
                            name="plan_id"
                            value="<?= (int) ($edit_plan->id ?? 0); ?>">


                        <div class="admin-form-grid">

                            <div>
                                <label>Plan Name *</label>

                                <input
                                    type="text"
                                    name="name"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->name ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                    required
                                    placeholder="जैसे साधारण सदस्य">
                            </div>


                            <div>
                                <label>Slug *</label>

                                <input
                                    type="text"
                                    name="slug"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->slug ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                    required
                                    placeholder="ordinary">
                            </div>


                            <div>
                                <label>Fee *</label>

                                <input
                                    type="number"
                                    name="amount"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->amount ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                    min="0"
                                    step="0.01"
                                    required>
                            </div>


                            <div>
                                <label>Currency</label>

                                <input
                                    type="text"
                                    name="currency"
                                    value="INR"
                                    readonly>
                            </div>


                            <div>
                                <label>Validity</label>

                                <select name="validity_type">

                                    <option
                                        value="lifetime"
                                        <?= (
                                            $edit_plan
                                            && $edit_plan->validity_type === 'lifetime'
                                        )
                                            ? 'selected'
                                            : ''; ?>>
                                        आजीवन
                                    </option>

                                    <option
                                        value="years"
                                        <?= (
                                            $edit_plan
                                            && $edit_plan->validity_type === 'years'
                                        )
                                            ? 'selected'
                                            : ''; ?>>
                                        Years
                                    </option>

                                </select>
                            </div>


                            <div>
                                <label>Validity Value</label>

                                <input
                                    type="number"
                                    name="validity_value"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->validity_value ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                    min="1"
                                    placeholder="जैसे 5">
                            </div>


                            <div>
                                <label>Minimum Age</label>

                                <input
                                    type="number"
                                    name="min_age"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->min_age ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                    min="0">
                            </div>


                            <div>
                                <label>Maximum Age</label>

                                <input
                                    type="number"
                                    name="max_age"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->max_age ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                    min="0">
                            </div>


                            <div>
                                <label>Sort Order</label>

                                <input
                                    type="number"
                                    name="sort_order"
                                    value="<?= htmlspecialchars(
                                                $edit_plan->sort_order ?? '0',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>">
                            </div>

                        </div>


                        <div class="admin-plan-options">

                            <label>
                                <input
                                    type="checkbox"
                                    name="requires_ordinary_membership"
                                    value="1"
                                    <?= (
                                        $edit_plan
                                        && (int) $edit_plan->requires_ordinary_membership === 1
                                    )
                                        ? 'checked'
                                        : ''; ?>>

                                साधारण सदस्य होना आवश्यक
                            </label>


                            <label>
                                <input
                                    type="checkbox"
                                    name="requires_sankalp"
                                    value="1"
                                    <?= (
                                        $edit_plan
                                        && (int) $edit_plan->requires_sankalp === 1
                                    )
                                        ? 'checked'
                                        : ''; ?>>

                                संकल्प आवश्यक
                            </label>


                            <label>
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    <?= (
                                        !$edit_plan
                                        || (int) $edit_plan->is_active === 1
                                    )
                                        ? 'checked'
                                        : ''; ?>>

                                Active
                            </label>

                        </div>


                        <button
                            type="submit"
                            class="admin-btn">

                            <?= $edit_plan
                                ? 'Update Membership Plan'
                                : 'Save Membership Plan'; ?>

                        </button>


                        <?php if ($edit_plan) : ?>

                            <a
                                href="?tab=settings"
                                class="admin-btn">
                                Cancel
                            </a>

                        <?php endif; ?>

                    </form>

                </div>

                <!-- Existing Plans -->

                <div class="admin-plan-list">

                    <h3>
                        Existing Membership Plans
                    </h3>


                    <?php if (empty($membership_plans)) : ?>

                        <div class="admin-empty">
                            कोई membership plan नहीं मिला।
                        </div>

                    <?php else : ?>

                        <div class="admin-table-scroll">

                            <table class="admin-table">

                                <thead>

                                    <tr>
                                        <th>नाम</th>
                                        <th>Fee</th>
                                        <th>Validity</th>
                                        <th>Age</th>
                                        <th>Rules</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>


                                <tbody>

                                    <?php foreach ($membership_plans as $plan) : ?>

                                        <tr>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $plan->name ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </td>


                                            <td>
                                                ₹<?= number_format(
                                                        (float) ($plan->amount ?? 0),
                                                        2
                                                    ); ?>
                                            </td>


                                            <td>

                                                <?php if (
                                                    ($plan->validity_type ?? '')
                                                    === 'lifetime'
                                                ) : ?>

                                                    आजीवन

                                                <?php else : ?>

                                                    <?= (int) (
                                                        $plan->validity_value ?? 0
                                                    ); ?>
                                                    वर्ष

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <?php
                                                $min_age =
                                                    $plan->min_age ?? null;

                                                $max_age =
                                                    $plan->max_age ?? null;
                                                ?>

                                                <?php if (
                                                    $min_age !== null
                                                    || $max_age !== null
                                                ) : ?>

                                                    <?= $min_age !== null
                                                        ? (int) $min_age
                                                        : '—'; ?>

                                                    -

                                                    <?= $max_age !== null
                                                        ? (int) $max_age
                                                        : '—'; ?>

                                                <?php else : ?>

                                                    —

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <?php if (
                                                    (int) (
                                                        $plan->requires_ordinary_membership
                                                        ?? 0
                                                    ) === 1
                                                ) : ?>

                                                    <small>
                                                        Ordinary required
                                                    </small><br>

                                                <?php endif; ?>


                                                <?php if (
                                                    (int) (
                                                        $plan->requires_sankalp
                                                        ?? 0
                                                    ) === 1
                                                ) : ?>

                                                    <small>
                                                        Sankalp
                                                    </small>

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <?php if (
                                                    (int) (
                                                        $plan->is_active ?? 0
                                                    ) === 1
                                                ) : ?>

                                                    <span class="status approved">
                                                        Active
                                                    </span>

                                                <?php else : ?>

                                                    <span class="status pending">
                                                        Inactive
                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <a
                                                    href="?tab=settings&membership_plan_edit=<?= (int) $plan->id; ?>">
                                                    Edit
                                                </a>

                                                &nbsp;|&nbsp;

                                                <a
                                                    href="?tab=settings&membership_plan_toggle=<?= (int) $plan->id; ?>"
                                                    onclick="return confirm('क्या आप इस membership plan का status बदलना चाहते हैं?');">

                                                    <?= (
                                                        (int) (
                                                            $plan->is_active ?? 0
                                                        ) === 1
                                                    )
                                                        ? 'Deactivate'
                                                        : 'Activate'; ?>

                                                </a>

                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </section> <?php endif; ?>

    </div>

<?php

    return ob_get_clean();
}
