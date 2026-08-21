<?php
if (!defined('ABSPATH')) exit;
function myjat_render_home()
{
    ob_start(); ?>
    <section class="myjat-home">
        <div class="myjat-orb myjat-orb-1"></div>
        <div class="myjat-orb myjat-orb-2"></div>
        <div class="myjat-orb myjat-orb-3"></div>

        <div class="myjat-container myjat-glassborder">
            <div class="myjat-hero">
                <div class="myjat-hero-left myjat-glass">
                    <span class="myjat-badge">
                        अखिल भारतवर्षीय जाट महासभा
                    </span>
                    <h1>
                        एकता<br>
                        सेवा<br>
                        स्वाभिमान

                    </h1>
                    <p class="home-para">
                        अखिल भारतवर्षीय जाट महासभा का आधिकारिक डिजिटल मंच, सदस्यता, सत्यापन, संगठन,
                        नेतृत्व एवं सामाजिक विकास के लिए समर्पित है। 
                       </p>
                    <div class="myjat-buttons-box">
                        <a href="/register/" class="myjat-btn  myjat-glass">
                            सदस्य बनें
                        </a>
                        <a href="/member-verification/" class="myjat-btn myjat-glass">
                            सदस्य सत्यापित
                        </a>
                    </div>
                    <div class="myjat-stats">
                        <div class="myjat-stat myjat-glass">
                            <h3>20K+</h3>
                            <span>पंजीकृत सदस्य</span>
                        </div>
                        <div class="myjat-stat myjat-glass">
                            <h3>500+</h3>
                            <span>ग्राम</span>
                        </div>
                        <div class="myjat-stat myjat-glass">
                            <h3>18+</h3>
                            <span>राज्य</span>
                        </div>
                        <div class="myjat-stat myjat-glass">
                            <h3>100%</h3>
                            <span>डिजिटल सेवा</span>
                        </div>
                    </div>
                </div>
<!---for right side---->





            </div>





<?php echo myjat_slider_home(); ?>




            <section class="myjat-features">
                <div class="myjat-feature-card myjat-glass">
                    <div class="myjat-feature-icon"><i class="fa-solid fa-users"></i></div>
                    <h3>डिजिटल सदस्यता</h3>
                    <p class="feature-para">ऑनलाइन सदस्यता पंजीकरण एवं त्वरित सत्यापन की सुविधा</p>
                </div>
                <div class="myjat-feature-card myjat-glass">
                    <div class="myjat-feature-icon"><i class="fa-solid fa-id-card"></i></div>
                    <h3>सदस्य निर्देशिका</h3>
                    <p class="feature-para">देशभर के सत्यापित सदस्यों की खोज एवं जानकारी प्राप्त करें</p>
                </div>
                <div class="myjat-feature-card myjat-glass">
                    <div class="myjat-feature-icon"><i class="fa-solid fa-building-columns"></i></div>
                    <h3>संगठन संरचना</h3>
                    <p class="feature-para">राष्ट्रीय, राज्य एवं जिला स्तर की संगठनात्मक संरचना</p>
                </div>
                <div class="myjat-feature-card myjat-glass">
                    <div class="myjat-feature-icon"><i class="fa-solid fa-newspaper"></i></div>
                    <h3>समाचार एवं सूचनाएँ</h3>
                    <p class="feature-para">महासभा की नवीनतम घोषणाएँ, कार्यक्रम एवं सामुदायिक गतिविधियों की जानकारी</p>
                </div>
            </section>
        <?php
        return ob_get_clean();
    }
    add_shortcode('myjat_home', 'myjat_render_home');
