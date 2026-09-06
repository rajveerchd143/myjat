<?php

declare(strict_types=1);

function myjat_render_application_view($application)
{
    ob_start();

    $esc = static function ($value): string {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    };

    $name = $esc($application->full_name ?? '');
    $membership_no = $esc($application->membership_no ?? '');
    $status_raw = (string) ($application->status ?? '');
    $status = $esc($status_raw);
    $status_key = strtolower(
        preg_replace('/[^a-zA-Z0-9_-]/', '', $status_raw)
    );

    $photo = $esc($application->photo_url ?? '');
    $id = (int) ($application->id ?? 0);
    $card_printed = $application->card_printed ?? '';

?>

    <div class="view-page">

        <header class="view-top">

            <div>
                <small>Membership Application</small>

                <h1><?= $name ?></h1>

                <div class="view-meta">
                    <span><?= $membership_no ?></span>
                    <span class="view-status status-<?= $status_key ?>">
                        <?= $status ?>
                    </span>
                </div>
            </div>

            <div class="view-buttons">

                <a href="javascript:history.back()">
                    ← Back
                </a>

                <a

                    href="/member-card/?id=<?= $id ?>"
                    target="_blank">

                    PVC Card

                </a>

                <button
                    type="button"
                    onclick="window.print()">

                    Print

                </button>

            </div>

        </header>


        <div class="view-layout">


            <aside class="view-side">

                <div class="photo-box">

                    <?php if ($photo !== '') : ?>

                        <img
                            src="<?= $photo ?>"
                            alt="<?= $name ?>">

                    <?php else : ?>

                        <div class="no-photo">
                            No Photo
                        </div>

                    <?php endif; ?>

                    <strong>
                        <?= $membership_no ?>
                    </strong>

                </div>


                <div class="box">

                    <h2>Member Photo</h2>

                    <form method="post">

                        <input
                            type="hidden"
                            name="member_id"
                            value="<?= $id ?>">

                        <input
                            type="text"
                            name="photo_url"
                            value="<?= $photo ?>"
                            placeholder="Photo URL">

                        <button type="submit">
                            Save Photo
                        </button>

                    </form>

                </div>

            </aside>


            <main class="view-main">


                <section class="box">

                    <h2>👤 Personal Information</h2>

                    <div class="data-grid">

                        <div>
                            <label>नाम</label>
                            <p><?= $esc($application->full_name ?? '') ?></p>
                        </div>

                        <div>
                            <label>पिता / पति का नाम</label>
                            <p><?= $esc($application->father_husband_name ?? '') ?></p>
                        </div>

                        <div>
                            <label>लिंग</label>
                            <p><?= $esc($application->gender ?? '') ?></p>
                        </div>

                        <div>
                            <label>जन्मतिथि</label>
                            <p><?= $esc($application->dob ?? '') ?></p>
                        </div>

                        <div>
                            <label>शिक्षा</label>
                            <p><?= $esc($application->education ?? '') ?></p>
                        </div>

                        <div>
                            <label>व्यवसाय</label>
                            <p><?= $esc($application->profession ?? '') ?></p>
                        </div>

                    </div>

                </section>


                <section class="box">

                    <h2>📍 Address Information</h2>

                    <div class="data-grid">

                        <div>
                            <label>राज्य</label>
                            <p><?= $esc($application->state_name ?? '') ?></p>
                        </div>

                        <div>
                            <label>जिला</label>
                            <p><?= $esc($application->district ?? '') ?></p>
                        </div>

                        <div>
                            <label>ग्राम / पंचायत</label>
                            <p><?= $esc($application->village_panchayat ?? '') ?></p>
                        </div>

                        <div>
                            <label>ब्लॉक</label>
                            <p><?= $esc($application->block_name ?? '') ?></p>
                        </div>

                        <div>
                            <label>विधानसभा</label>
                            <p><?= $esc($application->vidhansabha ?? '') ?></p>
                        </div>

                        <div>
                            <label>लोकसभा</label>
                            <p><?= $esc($application->loksabha ?? '') ?></p>
                        </div>

                        <div>
                            <label>पिनकोड</label>
                            <p><?= $esc($application->pincode ?? '') ?></p>
                        </div>

                        <div>
                            <label>मोबाइल</label>
                            <p><?= $esc($application->mobile_no ?? '') ?></p>
                        </div>

                        <div>
                            <label>ईमेल</label>
                            <p><?= $esc($application->email ?? '') ?></p>
                        </div>

                        <div class="full">
                            <label>वर्तमान पता</label>
                            <p><?= $esc($application->current_address ?? '') ?></p>
                        </div>

                        <div class="full">
                            <label>स्थायी पता</label>
                            <p><?= $esc($application->permanent_address ?? '') ?></p>
                        </div>

                    </div>

                </section>


                <section class="box">

                    <h2>🪪 Membership Information</h2>

                    <div class="data-grid">

                        <div>
                            <label>Membership No.</label>
                            <p><?= $membership_no ?></p>
                        </div>

                        <div>
                            <label>सदस्यता प्रकार</label>
                            <p><?= $esc($application->membership_type ?? '') ?></p>
                        </div>

                        <div>
                            <label>स्थिति</label>
                            <p>
                                <span class="view-status status-<?= $status_key ?>">
                                    <?= $status ?>
                                </span>
                            </p>
                        </div>

                        <div>
                            <label>स्वीकृति दिनांक</label>
                            <p><?= $esc($application->approval_date ?? '') ?></p>
                        </div>

                        <div>
                            <label>स्वीकृत किया</label>
                            <p><?= $esc($application->approved_by ?? '') ?></p>
                        </div>

                        <div>
                            <label>सदस्य वर्ष</label>
                            <p><?= $esc($application->member_since ?? '') ?></p>
                        </div>

                    </div>

                </section>


            </main>

        </div>

    </div>

<?php

    return ob_get_clean();
}
