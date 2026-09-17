<?php

/**
 * MY JAT - Photo Gallery
 *
 * Renders the homepage photo gallery.
 * Gallery records are read from the `gallery` table.
 */

if (!function_exists('myjat_gallery')) {
    function myjat_gallery($pdo = null)
    {
        if ($pdo === null && isset($GLOBALS['pdo'])) {
            $pdo = $GLOBALS['pdo'];
        }

        if (!$pdo instanceof PDO) {
            return '';
        }

        try {
            $stmt = $pdo->query(
                "SELECT id, image, caption
                 FROM gallery
                 WHERE status = 'active'
                 ORDER BY id DESC"
            );

            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            return '';
        }


        ob_start();
?>

        <section class="myjat-gallery myjat-glassblur" aria-label="Photo Gallery">


            <div class="myjat-gallery-wrap">

                <button
                    type="button"
                    class="myjat-gallery-arrow myjat-gallery-prev"
                    aria-label="Previous photos">‹</button>

                <div class="myjat-gallery-viewport">
                    <div class="myjat-gallery-track">

                        <?php if (!$photos): ?>

                            <div class="myjat-gallery-empty">
                                <div class="myjat-gallery-empty-icon">📷</div>

                                <h3>हमारी गतिविधियों की झलकियाँ</h3>

                                <p>
                                    महासभा की गतिविधियों और विशेष अवसरों की तस्वीरें यहाँ दिखाई देंगी।
                                </p>
                            </div>

                        <?php else: ?>

                            <?php foreach ($photos as $photo): ?>

                                <?php
                                $image = trim((string) ($photo['image'] ?? ''));
                                $caption = trim((string) ($photo['caption'] ?? ''));

                                if ($image === '') {
                                    continue;
                                }
                                ?>

                                <button
                                    type="button"
                                    class="myjat-gallery-item"
                                    data-gallery-image="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                                    data-gallery-caption="<?= htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') ?>">

                                    <img
                                        src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($caption ?: 'MY JAT Gallery', ENT_QUOTES, 'UTF-8') ?>"
                                        loading="lazy">

                                    <?php if ($caption !== ''): ?>

                                        <span class="myjat-gallery-caption">
                                            <?= htmlspecialchars($caption, ENT_QUOTES, 'UTF-8') ?>
                                        </span>

                                    <?php endif; ?>

                                </button>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>

                <button
                    type="button"
                    class="myjat-gallery-arrow myjat-gallery-next"
                    aria-label="Next photos">›</button>

            </div>

        </section>


        <div class="myjat-gallery-lightbox" aria-hidden="true">

            <button
                type="button"
                class="myjat-gallery-lightbox-close"
                aria-label="Close photo">×</button>

            <div class="myjat-gallery-lightbox-content">

                <img
                    class="myjat-gallery-lightbox-image"
                    src=""
                    alt="">

                <p class="myjat-gallery-lightbox-caption"></p>

            </div>

        </div>

<?php

        return ob_get_clean();
    }
}
