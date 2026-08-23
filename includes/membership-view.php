<?php

// =========================================================
// Membership Application View
// Standalone PHP Version
// =========================================================

function myjat_render_application_view($application)
{
    ob_start();

    $full_name = htmlspecialchars(
        $application->full_name ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $membership_no = htmlspecialchars(
        $application->membership_no ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $status_raw = $application->status ?? '';

    $status = htmlspecialchars(
        $status_raw,
        ENT_QUOTES,
        'UTF-8'
    );

    $status_class = strtolower(
        preg_replace('/[^a-zA-Z0-9_-]/', '', $status_raw)
    );

    $photo_url = htmlspecialchars(
        $application->photo_url ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $member_id = htmlspecialchars(
        $application->id ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $card_printed = $application->card_printed ?? '';

    ?>

    <div class="myjat-view-page">

        <!-- =====================================================
             Header
        ====================================================== -->

        <div class="myjat-view-header">

            <div class="myjat-view-header-left">

                <span class="myjat-view-subtitle">
                    Membership Application
                </span>

                <h1>
                    <?= $full_name ?>
                </h1>

                <div class="myjat-member-meta">

                    <span class="myjat-member-no">
                        <?= $membership_no ?>
                    </span>

                    <span class="myjat-status-badge status-<?= $status_class ?>">
                        <?= $status ?>
                    </span>

                </div>

            </div>

            <div class="myjat-view-actions">

                <a
                    href="javascript:history.back()"
                    class="myjat-btn myjat-btn-light">
                    ← Back
                </a>

                <a
                    href="/member-card/?id=<?= $member_id ?>"
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


        <!-- =====================================================
             Main Content
        ====================================================== -->

        <div class="myjat-view-content">


            <!-- =================================================
                 Photo Section
            ================================================== -->

            <div class="myjat-photo-section">

                <div class="myjat-photo-card">

                    <?php if (!empty($photo_url)): ?>

                        <img
                            src="<?= $photo_url ?>"
                            class="myjat-member-photo"
                            alt="<?= $full_name ?>">

                    <?php else: ?>

                        <div class="myjat-no-photo">
                            No Photo
                        </div>

                    <?php endif; ?>

                    <div class="myjat-photo-member-no">
                        <?= $membership_no ?>
                    </div>

                </div>


                <div class="myjat-card myjat-photo-editor">

                    <h3>
                        Member Photo
                    </h3>

                    <form method="post">

                        <input
                            type="hidden"
                            name="member_id"
                            value="<?= $member_id ?>">

                        <input
                            type="text"
                            name="photo_url"
                            class="myjat-photo-input"
                            value="<?= $photo_url ?>"
                            placeholder="Photo URL">

                        <button
                            type="submit"
                            class="myjat-btn myjat-btn-primary">
                            Save Photo
                        </button>

                    </form>

                </div>

            </div>


            <!-- =================================================
                 Right Column
            ================================================== -->

            <div class="myjat-details-column">


                <!-- =============================================
                     Personal Information
                ============================================== -->

                <div class="myjat-card myjat-info-card">

                    <div class="myjat-card-title">
                        👤 Personal Information
                    </div>

                    <div class="myjat-info-grid-2">

                        <div class="myjat-field">
                            <label>नाम</label>
                            <div>
                                <?= htmlspecialchars($application->full_name ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>पिता / पति का नाम</label>
                            <div>
                                <?= htmlspecialchars($application->father_husband_name ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>लिंग</label>
                            <div>
                                <?= htmlspecialchars($application->gender ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>जन्मतिथि</label>
                            <div>
                                <?= htmlspecialchars($application->dob ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>शिक्षा</label>
                            <div>
                                <?= htmlspecialchars($application->education ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>व्यवसाय</label>
                            <div>
                                <?= htmlspecialchars($application->profession ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                    </div>

                </div>


                <!-- =============================================
                     Address Information
                ============================================== -->

                <div class="myjat-card myjat-info-card">

                    <div class="myjat-card-title">
                        📍 Address Information
                    </div>

                    <div class="myjat-info-grid-2">

                        <div class="myjat-field">
                            <label>राज्य</label>
                            <div>
                                <?= htmlspecialchars($application->state_name ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>जिला</label>
                            <div>
                                <?= htmlspecialchars($application->district ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>ग्राम / पंचायत</label>
                            <div>
                                <?= htmlspecialchars($application->village_panchayat ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>ब्लॉक</label>
                            <div>
                                <?= htmlspecialchars($application->block_name ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>विधानसभा</label>
                            <div>
                                <?= htmlspecialchars($application->vidhansabha ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>लोकसभा</label>
                            <div>
                                <?= htmlspecialchars($application->loksabha ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>पिनकोड</label>
                            <div>
                                <?= htmlspecialchars($application->pincode ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>मोबाइल</label>
                            <div>
                                <?= htmlspecialchars($application->mobile_no ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>ईमेल</label>
                            <div>
                                <?= htmlspecialchars($application->email ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field myjat-field-full">
                            <label>वर्तमान पता</label>
                            <div>
                                <?= htmlspecialchars($application->current_address ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field myjat-field-full">
                            <label>स्थायी पता</label>
                            <div>
                                <?= htmlspecialchars($application->permanent_address ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                    </div>

                </div>


                <!-- =============================================
                     Membership Information
                ============================================== -->

                <div class="myjat-card myjat-info-card">

                    <div class="myjat-card-title">
                        🪪 Membership Information
                    </div>

                    <div class="myjat-info-grid-2">

                        <div class="myjat-field">
                            <label>Membership No.</label>
                            <div>
                                <?= $membership_no ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>सदस्यता प्रकार</label>
                            <div>
                                <?= htmlspecialchars($application->membership_type ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>स्थिति</label>
                            <div>

                                <span class="myjat-status-badge status-<?= $status_class ?>">
                                    <?= $status ?>
                                </span>

                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>स्वीकृति दिनांक</label>
                            <div>
                                <?= htmlspecialchars($application->approval_date ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>स्वीकृत किया</label>
                            <div>
                                <?= htmlspecialchars($application->approved_by ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <div class="myjat-field">
                            <label>सदस्य वर्ष</label>
                            <div>
                                <?= htmlspecialchars($application->member_since ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                    </div>

                </div>


                <!-- =============================================
                     Administration Information
                ============================================== -->

                <div class="myjat-card myjat-info-card">

                    <div class="myjat-card-title">
                        ⚙️ Administration
                    </div>

                    <div class="myjat-info-grid-2">


                        <div class="myjat-field">

                            <label>
                                कार्ड प्रिंट स्थिति
                            </label>

                            <div>

                                <?php if ($card_printed === 'Yes'): ?>

                                    <span class="myjat-status-badge status-approved">
                                        Printed
                                    </span>

                                <?php else: ?>

                                    <span class="myjat-status-badge status-pending">
                                        Not Printed
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <div class="myjat-field">

                            <label>
                                Application ID
                            </label>

                            <div>
                                <?= $member_id ?>
                            </div>

                        </div>

                    </div>


                    <?php if ($card_printed !== 'Yes'): ?>

                        <div class="myjat-view-actions">

                            <a
                                class="myjat-btn myjat-btn-primary"
                                href="?mark_card_printed=<?= $member_id ?>">
                                Mark Card Printed
                            </a>

                        </div>

                    <?php endif; ?>

                </div>


            </div>

        </div>

    </div>

    <?php

    return ob_get_clean();
}