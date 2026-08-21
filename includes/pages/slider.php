<?php
if (!defined('ABSPATH')) exit;

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

                    <button class="myjat-slider-arrow myjat-slider-prev" type="button" aria-label="पिछला">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <?php foreach ($heroes as $hero): ?>

                        <?php
                        preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $hero['html'], $title_match);
                        $title = isset($title_match[1]) ? wp_strip_all_tags($title_match[1]) : '';

                        preg_match('/<p[^>]*>(.*?)<\/p>/is', $hero['html'], $desc_match);
                        $description = isset($desc_match[1]) ? wp_trim_words(wp_strip_all_tags($desc_match[1]), 35) : '';
                        ?>

                        <div class="myjat-slider-item">

                            <div class="myjat-slider-image">

                                <img
                                    src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/great/' . $hero['slug'] . '.webp'); ?>"
                                    alt="<?php echo esc_attr($title); ?>"
                                    class="myjat-slider-img">

                            </div>

                            <div class="myjat-slider-content">

                                <span class="myjat-slider-badge">
                                    भारत के महान जाट महापुरुष
                                </span>

                                <h2 class="myjat-slider-title">
                                    <?php echo esc_html($title); ?>
                                </h2>

                                <p class="myjat-slider-desc">
                                    <?php echo esc_html($description); ?>
                                </p>

                                <a

                                    <?php
                                    $hero_shortcode = 'myjat_' . $hero['slug'];
                                    $hero_pages = get_pages([
                                        'post_status' => 'publish',
                                        'number' => -1
                                    ]);

                                    $hero_url = '#';

                                    foreach ($hero_pages as $hero_page) {
                                        if (has_shortcode($hero_page->post_content, $hero_shortcode)) {
                                            $hero_url = get_permalink($hero_page->ID);
                                            break;
                                        }
                                    }
                                    ?>

                                    <a
                                    href="<?php echo esc_url($hero_url); ?>"
                                    class="myjat-slider-button">
                                    जीवन परिचय पढ़ें...
                                </a>


                            </div>

                        </div>

                    <?php endforeach; ?>

                    <button class="myjat-slider-arrow myjat-slider-next" type="button" aria-label="अगला">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                </div>

            </div>

            <div class="myjat-slider-dots">

                <?php foreach ($heroes as $index => $hero): ?>

                    <span
                        class="<?php echo $index === 0 ? 'active' : ''; ?>"
                        data-slide="<?php echo esc_attr($index); ?>"></span>

                <?php endforeach; ?>

            </div>

    

    </section>

<?php
    return ob_get_clean();
}
