<?php

function trim_words($text, $limit = 35)
{
    $words = preg_split('/\s+/', trim($text));

    if (count($words) <= $limit) {
        return trim($text);
    }

    return implode(' ', array_slice($words, 0, $limit)) . '...';
}


$description = isset($desc_match[1])
    ? trim_words(strip_tags($desc_match[1]), 35)
    : '';
function myjat_slider_home()
{
    $great_pages = myjat_get_great_pages();

    $heroes = [];

    foreach ($great_pages as $slug) {

        $function = 'myjat_' . $slug . '_home';

        if (!function_exists($function)) {
            continue;
        }

        $html = call_user_func($function);

        $heroes[] = [
            'slug' => $slug,
            'html' => $html
        ];
    }

    ob_start();
?>

    <section class="myjat-slider-section">

        <div class="myjat-slider-box myjat-glass">

            <div class="myjat-slider-track">

                <button
                    class="myjat-slider-arrow myjat-slider-prev"
                    type="button"
                    aria-label="पिछला">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <?php foreach ($heroes as $hero): ?>

                    <?php

                    preg_match(
                        '/<h1[^>]*>(.*?)<\/h1>/is',
                        $hero['html'],
                        $title_match
                    );

                    $title = isset($title_match[1])
                        ? strip_tags($title_match[1])
                        : '';

                    preg_match(
                        '/<p[^>]*>(.*?)<\/p>/is',
                        $hero['html'],
                        $desc_match
                    );

                    $description = isset($desc_match[1])
                        ? trim_words(strip_tags($desc_match[1]), 35)
                        : '';

                    $image_url =
                        '/assets/images/great/' .
                        $hero['slug'] .
                        '.webp';

                    /*
                 * Standalone PHP URL
                 *
                 * WordPress get_pages(),
                 * has_shortcode(),
                 * get_permalink()
                 * completely removed.
                 */
$hero_url = '/system/great.php?slug=' . urlencode($hero['slug']);
                    ?>

                    <div class="myjat-slider-item">

                        <div class="myjat-slider-image">

                            <img
                                src="<?= htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                                class="myjat-slider-img">

                        </div>

                        <div class="myjat-slider-content">

                            <span class="myjat-slider-badge">
                                भारत के महान जाट महापुरुष
                            </span>

                            <h2 class="myjat-slider-title">
                                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                            </h2>

                            <p class="myjat-slider-desc">
                                <?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <a
                                href="<?= htmlspecialchars($hero_url, ENT_QUOTES, 'UTF-8') ?>"
                                class="myjat-slider-button">
                                जीवन परिचय पढ़ें...
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

                <button
                    class="myjat-slider-arrow myjat-slider-next"
                    type="button"
                    aria-label="अगला">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

            </div>

        </div>

        <div class="myjat-slider-dots">

            <?php foreach ($heroes as $index => $hero): ?>

                <span
                    class="<?= $index === 0 ? 'active' : '' ?>"
                    data-slide="<?= htmlspecialchars($index, ENT_QUOTES, 'UTF-8') ?>">
                </span>

            <?php endforeach; ?>

        </div>

    </section>

<?php

    return ob_get_clean();
}
