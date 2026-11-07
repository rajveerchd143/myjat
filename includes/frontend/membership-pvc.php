<?php

// =========================================================
// Member PVC Card
// Standalone PHP Version
// =========================================================

function myjat_render_member_card($application)
{
    ob_start();

    $photo_url = htmlspecialchars(
        $application->photo_url ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

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

    $membership_type = htmlspecialchars(
        $application->membership_type ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $district = htmlspecialchars(
        $application->district ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $member_since = htmlspecialchars(
        $application->member_since ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $qr_data =
        trim(
            $application->qr_code ?? ''
        );

    if (!$qr_data && !empty($application->membership_no)) {

        $qr_data =
            '/member-verification/?id='
            . urlencode(
                $application->membership_no
            );
    }

    $qr_code =
        urlencode($qr_data);
?>

    <!-- PVC card image download controls -->
    <div class="myjat-pvc-download-controls">

        <button type="button"
            class="myjat-pvc-download-btn"
            id="myjat-pvc-download-both">
            Download PVC Card
        </button>
    </div>

    <!-- =====================================================
         PVC Card Front
    ====================================================== -->

    <div class="myjat-pvc">

        <div class="myjat-pvc-back-watermark">

            <img
                class="myjat-pvc-back-watermark-logo"
                src="/assets/images/background/logo.png"
                alt="Logo">

        </div>


        <div class="myjat-pvc-header">

            <div class="myjat-pvc-logo-wrap">

                <img
                    class="myjat-pvc-logo"
                    src="/assets/images/background/logo.png"
                    alt="Logo">

            </div>


            <div class="myjat-pvc-header-content">

                <div class="myjat-pvc-title">
                    अखिल भारतवर्षीय जाट महासभा
                </div>

                <div class="myjat-pvc-subtitle">
                    सदस्य पहचान पत्र
                    <br>
                    (Member Identity Card)
                </div>

            </div>

        </div>


        <div class="myjat-pvc-divider"></div>


        <div class="myjat-pvc-body">

            <?php if (!empty($photo_url)): ?>

                <img
                    class="myjat-pvc-photo"
                    src="<?= $photo_url ?>"
                    alt="<?= $full_name ?>">

            <?php else: ?>

                <div class="myjat-pvc-photo-placeholder"></div>

            <?php endif; ?>


            <div class="myjat-pvc-header-content">

                <strong class="myjat-pvc-name">
                    <?= $full_name ?>
                </strong>


                <table class="myjat-pvc-table">

                    <tr>

                        <td class="myjat-pvc-cell">
                            🏷️ ABJM No<br>
                            <strong><?= $membership_no ?></strong>
                        </td>

                        <td class="myjat-pvc-cell">
                            👥 Membership Type<br>
                            <strong><?= $membership_type ?></strong>
                        </td>

                    </tr>


                    <tr>

                        <td class="myjat-pvc-cell">
                            📍 District<br>
                            <strong><?= $district ?></strong>
                        </td>

                        <td class="myjat-pvc-cell">
                            📅 Member Since<br>
                            <strong><?= $member_since ?></strong>
                        </td>

                    </tr>

                </table>

            </div>

        </div>


        <!-- =================================================
             Signatures
        ================================================== -->

        <div class="myjat-pvc-signatures">


            <div class="myjat-pvc-sign myjat-pvc-sign-left">

                <img
                    class="myjat-pvc-sign-image"
                    src="/assets/images/background/president_sign.png"
                    alt="President Signature">

                <div class="myjat-pvc-sign-line"></div>

                <div class="myjat-pvc-sign-name">
                    चौ० वीरेन्द्र सिंह
                </div>

                <div class="myjat-pvc-sign-post">
                    राष्ट्रीय अध्यक्ष
                </div>

            </div>


            <div class="myjat-pvc-sign myjat-pvc-sign-right">

                <img
                    class="myjat-pvc-sign-image"
                    src="/assets/images/background/secret_sign.png"
                    alt="General Secretary Signature">

                <div class="myjat-pvc-sign-line"></div>

                <div class="myjat-pvc-sign-name">
                    चौ० शेरसिंह
                </div>

                <div class="myjat-pvc-sign-post">
                    राष्ट्रीय महासचिव
                </div>

            </div>


        </div>

    </div>


    <!-- =====================================================
         PVC Card Back
    ====================================================== -->

    <br>

    <div class="myjat-pvc myjat-pvc-back">


        <div class="myjat-pvc-watermark">

            <img
                class="myjat-pvc-back-watermark-logo"
                src="/assets/images/background/logo.png"
                alt="Logo">

        </div>


        <div class="myjat-pvc-back-title">

            अखिल भारतवर्षीय जाट महासभा

            <hr class="myjat-pvc-back-divider">

        </div>


        <!-- =================================================
             QR Code
        ================================================== -->

        <div class="myjat-pvc-qr">

            <img
                class="myjat-pvc-qr-image"
                src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= $qr_code ?>"
                alt="QR Code">

            <div class="myjat-pvc-qr-title">
                Scan to Verify
            </div>

            <div class="myjat-pvc-qr-url">
                www.myjat.in
            </div>


            <div class="myjat-pvc-back-footer">

                This card remains the property of

                <br>

                <strong>
                    Akhil Bharatvarshiya Jat Mahasabha
                </strong>

                <br>

                If found, please return to the organization.

                <br>

                📞 Contact:
                <strong>+91 9412274738</strong>

            </div>

        </div>

    </div>

    <script>
        (function() {
            const button = document.getElementById('myjat-pvc-download-both');

            if (!button || button.dataset.bound === '1') return;
            button.dataset.bound = '1';

            async function loadLibrary() {
                if (window.html2canvas) return window.html2canvas;

                await new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });

                return window.html2canvas;
            }

            async function renderCard(card, html2canvas) {
                await Promise.all(
                    [...card.querySelectorAll('img')].map(img => {
                        if (img.complete && img.naturalWidth) return Promise.resolve();

                        return new Promise(resolve => {
                            img.onload = resolve;
                            img.onerror = resolve;
                        });
                    })
                );

                // Mobile browser ke liye ensure karo ki card ka size valid ho
                const canvases = [...card.querySelectorAll('canvas')];

                console.log(
                    'PVC CANVASES:',
                    canvases.map((c, i) => ({
                        index: i,
                        width: c.width,
                        height: c.height,
                        cssWidth: getComputedStyle(c).width,
                        cssHeight: getComputedStyle(c).height
                    }))
                );
                const source = await html2canvas(card, {
                    scale: 3,
                    backgroundColor: '#ffffff',
                    useCORS: true,
                    allowTaint: false,
                    logging: false
                });

                // Standard ATM card: 85.60 x 53.98 mm at 300 DPI
                const width = 1011;
                const height = 638;

                const output = document.createElement('canvas');
                output.width = width;
                output.height = height;

                const ctx = output.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, width, height);

                const scale = Math.min(
                    width / source.width,
                    height / source.height
                );

                const w = source.width * scale;
                const h = source.height * scale;

                ctx.drawImage(
                    source,
                    (width - w) / 2,
                    (height - h) / 2,
                    w,
                    h
                );

                return output;
            }

            button.addEventListener('click', async function() {
                const cards = document.querySelectorAll('.myjat-pvc');

                if (cards.length < 2) {
                    alert('Front aur Back PVC card nahi mile.');
                    return;
                }

                button.disabled = true;

                try {
                    button.textContent = 'Preparing PNG...';

                    const html2canvas = await loadLibrary();

                    const front = await renderCard(cards[0], html2canvas);
                    const back = await renderCard(cards[1], html2canvas);

                    // One combined PNG with 36px gap
                    const gap = 36;
                    const topGap = 30;

                    const combined = document.createElement('canvas');

                    combined.width = 1011;
                    combined.height = topGap + 638 + gap + 638 + gap;

                    const ctx = combined.getContext('2d');

                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, combined.width, combined.height);

                    ctx.drawImage(front, 0, topGap);
                    ctx.drawImage(back, 0, topGap + 638 + gap);
                    const link = document.createElement('a');
                    link.download = 'MYJAT-PVC-Front-Back.png';
                    link.href = combined.toDataURL('image/png');

                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                } catch (error) {
                    console.error("PNG EXPORT ERROR:", error);

                    alert(
                        "PNG export failed:\n\n" +
                        error.name + "\n" +
                        error.message
                    );
                } finally {
                    button.disabled = false;
                    button.textContent = 'Download PVC Card';
                }
            });
        })();
    </script>
    <script>
        (function() {

            function fitPVC() {

                document.querySelectorAll(".myjat-pvc").forEach(card => {

                    const parent = card.parentElement;

                    if (!parent) return;

                    const availableWidth = parent.clientWidth;

                    const scale = Math.min(1, (availableWidth / 530) * 0.90);
                    card.style.zoom = scale;

                });

            }

            window.addEventListener("load", fitPVC);
            window.addEventListener("resize", fitPVC);

            fitPVC();

        })();
    </script>
<?php



    return ob_get_clean();
}


// =========================================================
// Member Card Page
// =========================================================

function myjat_member_card_page($pdo)
{
    // =====================================================
    // Login Check
    // =====================================================

    if (empty($_SESSION['myjat_user'])) {
        return '<div class="myjat-warning-message">
            Please login first.
        </div>';
    }


    // =====================================================
    // Get Application ID from URL
    // Example: /member-card/?id=17
    // =====================================================

    $id = (int) ($_GET['id'] ?? 0);

    if (!$id) {
        return '<div class="myjat-warning-message">
            Member not found.
        </div>';
    }


    // =====================================================
    // Get Exact Application
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->execute([$id]);

    $application = $stmt->fetch(PDO::FETCH_OBJ);


    if (!$application) {
        return '<div class="myjat-warning-message">
            Member application not found.
        </div>';
    }


    // =====================================================
    // Approval Check
    // =====================================================

    if (
        strtolower(
            trim($application->status ?? '')
        ) !== 'approved'
    ) {
        return '<div class="myjat-warning-message">
            PVC card is available after approval.
        </div>';
    }


    // =====================================================
    // Render Exact Member Card
    // =====================================================

    return myjat_render_member_card(
        $application
    );
}
// =========================================================
// Admin Member Card Page
// =========================================================

function myjat_admin_member_card_page($pdo)
{
    // =====================================================
    // Admin Permission Check
    // =====================================================

    if (
        !function_exists('myjat_can')
        || !myjat_can('myjat_view_application')
    ) {
        http_response_code(403);

        return 'Access denied.';
    }


    // =====================================================
    // Application ID
    // =====================================================

    $id = (int) (
        $_GET['id'] ?? 0
    );

    if (!$id) {
        return 'Member not found.';
    }


    // =====================================================
    // Get Application
    // =====================================================

    $stmt = $pdo->prepare(
        "SELECT *
         FROM membership_applications
         WHERE id = ?
         LIMIT 1"
    );

    $stmt->execute([
        $_SESSION['myjat_user']['email'] ?? '',
        $_SESSION['myjat_user']['email'] ?? ''
    ]);

    $application = $stmt->fetch(PDO::FETCH_OBJ);


    if (!$application) {
        return 'Member not found.';
    }


    // =====================================================
    // Approval Check
    // =====================================================

    if (
        strtolower(
            trim($application->status ?? '')
        ) !== 'approved'
    ) {
        return 'PVC card is available after approval.';
    }


    // =====================================================
    // Render Card
    // =====================================================

    return myjat_render_member_card(
        $application
    );
}
