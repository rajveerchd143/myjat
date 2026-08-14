<?php
if (!defined('ABSPATH')) exit;
function myjat_empty_home()
{
    ob_start(); ?>
    <section class="myjat-home">
        <div class="myjat-orb myjat-orb-1"></div>
        <div class="myjat-orb myjat-orb-2"></div>
        <div class="myjat-orb myjat-orb-3"></div>

        <div class="menu-para">






        </div>
    

    </section>
<?php
    return ob_get_clean();
}
add_shortcode('myjat_empty', 'myjat_empty_home');
