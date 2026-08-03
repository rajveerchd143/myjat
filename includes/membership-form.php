<?php
// =========================================================
// MYJAT Membership Management System
// Module: Membership Registration Form
// File: membership-form.php
//
// Description:
// Handles custom membership registration form.
// (Development module - initial structure)
//
// =========================================================



if (! defined('ABSPATH')) {
    exit;
}


// =========================================================
// Description:
// Register Membership Form Shortcode
// =========================================================

function myjat_membership_form_shortcode()
{

    ob_start();


    if (!is_user_logged_in()) {

        return '<div class="myjat-warning-message">
	Please login with your Google account before submitting the membership application.
	<br><br>
	<a class="myjat-google-login" href="' . esc_url(myjat_google_login_url()) . '">
	Continue with Google
	</a>
	</div>';
    }

    $current_user = wp_get_current_user();

    $google_name = '';

    $google_email = '';

    if (is_user_logged_in()) {

        $google_name = $current_user->display_name;

        $google_email = $current_user->user_email;
    }



?>

    <?php
    // =========================================================
    // Description:
    // Success Message
    // =========================================================

    if (
        isset($_GET['membership']) &&
        $_GET['membership'] === 'submitted'
    ) :
    ?>

    <?php endif; ?>


    <form
        method="post"
        enctype="multipart/form-data"
        class="myjat-membership-form"
        id="myjat-membership-form"
        autocomplete="off"
        novalidate>

        <!----Header---->
        <div class="myjat-form-header">
            <h1>अखिल भारतवर्षीय जाट महासभा</h1>
            <h2>सदस्यता आवेदन पत्र</h2>
            <div class="myjat-form-meta">
                <div>
                    <strong>दिनांक :</strong>
                    <?php echo esc_html(current_time('d-m-Y')); ?>
                </div>
                <div>
                    <strong>सदस्यता क्रमांक :</strong>
                    आवेदन स्वीकृति के बाद जारी होगा
                </div>
            </div>
        </div>


        <?php wp_nonce_field('myjat_membership_form', 'myjat_membership_nonce'); ?>
        <?php if (isset($_GET['membership']) && $_GET['membership'] === 'submitted') : ?>

            <div class="myjat-success-message">
                <strong>✅ आवेदन सफलतापूर्वक जमा हो गया।</strong><br>

                सदस्यता क्रमांक :
                <strong><?php echo esc_html($_GET['member_no'] ?? ''); ?></strong>
            </div>

        <?php endif; ?>

        <?php if (isset($_GET['application']) && $_GET['application'] === 'exists'): ?>

            <div class="myjat-warning-message">

                <strong>
                    आप पहले ही सदस्यता आवेदन जमा कर चुके हैं।
                </strong>

            </div>

        <?php endif; ?>


        <div class="myjat-section">
            <h3 class="myjat-section-title">
                व्यक्तिगत जानकारी
            </h3>


            <div class="myjat-form-group">
                <label>पूरा नाम *</label>

                <input class="myjat-input"
                    type="text"
                    name="full_name"
                    maxlength="100"
                    autocomplete="name"
                    required
                    value="<?php echo esc_attr($google_name); ?>">


            </div>

            <div class="myjat-form-group">
                <label>पिता का नाम *</label>
                <input class="myjat-input"
                    type="text"
                    name="father_husband_name"
                    maxlength="100"
                    autocomplete="off"
                    required>
            </div>


            <div class="myjat-form-group">
                <label>जन्म तिथि *</label>
                <input class="myjat-input"
                    type="date" name="dob" required>
            </div>

            <div class="myjat-form-group">
                <label>लिंग *</label>

                <select class="myjat-select"
                    name="gender" required>
                    <option value="">चुनें</option>
                    <option>पुरुष</option>
                    <option>महिला</option>
                    <option>अन्य</option>
                </select>
            </div>


            <div class="myjat-form-group">
                <label>शिक्षा</label>
                <input class="myjat-input"
                    type="text"
                    name="education"
                    maxlength="100">
            </div>

            <div class="myjat-form-group">
                <label>व्यवसाय</label>
                <input class="myjat-input"
                    type="text"
                    name="profession"
                    maxlength="100">
            </div>

            <!-- ========================= -->
            <!-- Contact -->
            <!-- ========================= -->

            <div class="myjat-form-group">
                <label>मोबाइल नंबर *</label>

                <input class="myjat-input"
                    type="tel"
                    name="mobile_no"
                    maxlength="10"
                    minlength="10"
                    pattern="[0-9]{10}"
                    inputmode="numeric"
                    autocomplete="off"
                    required>

            </div>


            <div class="myjat-form-group">
                <label>ईमेल</label>
                <input class="myjat-input"
                    type="email"
                    name="email"
                    maxlength="100"
                    autocomplete="email"
                    spellcheck="false"
                    value="<?php echo esc_attr($google_email); ?>"
                    <?php echo $google_email ? 'readonly' : ''; ?>>

            </div>

            <!-- ========================= -->
            <!-- Address -->
            <!-- ========================= -->

            <div class="myjat-form-group">
                <label>वर्तमान पता *</label>
                <textarea class="myjat-textarea"
                    name="current_address"
                    rows="3"
                    maxlength="500"
                    required></textarea>
            </div>


            <div class="myjat-form-group">
                <label>स्थायी पता</label>

                <textarea class="myjat-textarea"
                    name="permanent_address"
                    rows="3"
                    maxlength="500"></textarea>
            </div>


            <div class="myjat-form-group">
                <label>आधार संख्या</label>

                <input class="myjat-input"
                    type="text"
                    name="aadhaar_no"
                    maxlength="12"
                    minlength="12"
                    pattern="[0-9]{12}"
                    inputmode="numeric"
                    autocomplete="off">
            </div>

            <div class="myjat-form-group">
                <label>फोन नंबर</label>

                <input class="myjat-input"
                    type="tel"
                    name="phone_no"
                    maxlength="10"
                    inputmode="numeric">

            </div>

            <div class="myjat-form-group">
                <label>परिवार का विवरण</label>
                <textarea class="myjat-textarea"
                    name="family_details" rows="4"></textarea>
            </div>



            <div class="myjat-section">
                <h3 class="myjat-section-title">
                    निर्वाचन क्षेत्र की जानकारी
                </h3>

                <div class="myjat-form-group">

                    <label>राज्य *</label>

                    <select
                        name="state_name"
                        id="myjat-state"
                        class="myjat-select"
                        required>

                        <option value="">राज्य चुनें</option>

                        <?php foreach (MyJat_Location_Service::get_states() as $state) : ?>

                            <option value="<?php echo esc_attr($state); ?>">
                                <?php echo esc_html($state); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="myjat-form-group">
                    <label>जिला *</label>
                    <select
                        name="district"
                        id="myjat-district"
                        class="myjat-select"
                        required>


                    </select>

                </div>


                <div class="myjat-form-group">
                    <label>ब्लॉक</label>
                    <select
                        id="myjat-block"
                        name="block_name"
                        class="myjat-select"
                        required>

                        <option value="">ब्लॉक चुनें</option>

                    </select>


                </div>

                <div class="myjat-form-group">
                    <label>ग्राम / पंचायत</label>

                    <select
                        id="myjat-village"
                        name="village_panchayat"
                        class="myjat-select">
                        <option value="">ग्राम / पंचायत चुनें</option>
                    </select>
                </div>

                <div class="myjat-form-group">
                    <label>पिन कोड *</label>
                    <input
                        type="text"
                        id="myjat-pincode"
                        name="pincode"
                        class="myjat-input"
                        maxlength="6"
                        required>
                </div>



                <div class="myjat-form-group">
                    <label>विधानसभा</label>
                    <input class="myjat-input"
                        type="text" name="vidhansabha">
                </div>

                <div class="myjat-form-group">
                    <label>लोकसभा</label>
                    <input class="myjat-input"
                        type="text" name="loksabha">
                </div>

                <div class="myjat-section">
                    <h3 class="myjat-section-title">
                        प्रायोजन जानकारी
                    </h3>
                    <div class="myjat-form-group">
                        <label>सिफारिश कर्ता का नाम</label>
                        <input class="myjat-input"
                            type="text" name="recommender_name">
                    </div>

                    <div class="myjat-form-group">
                        <label>सिफारिश कर्ता सदस्य संख्या</label>
                        <input class="myjat-input"
                            type="text"
                            name="recommender_membership_no"
                            maxlength="20"
                            autocomplete="off">
                    </div>

                    <div class="myjat-form-group">
                        <label>सिफारिश कर्ता मोबाइल</label>
                        <input class="myjat-input"
                            type="tel"
                            name="recommender_mobile"
                            maxlength="10"
                            minlength="10"
                            pattern="[0-9]{10}"
                            inputmode="numeric"
                            autocomplete="off">
                    </div>



                    <!-- ========================= -->
                    <!-- Membership -->
                    <!-- ========================= -->

                    <div class="myjat-form-group">
                        <label>सदस्यता प्रकार *</label>

                        <select class="myjat-select"
                            name="membership_type" required>
                            <option value="">चुनें</option>
                            <option>साधारण सदस्य</option>
                            <option>सक्रिय सदस्य</option>
                            <option>संरक्षक सदस्य</option>

                        </select>
                    </div>



                    <!-- ========================= -->



                    <!-- Photo -->
                    <!-- ========================= -->

                    <div class="myjat-form-group">
                        <label>फोटो *</label>

                        <input
                            class="myjat-file"
                            type="file"
                            name="photo"
                            id="photo"
                            accept=".jpg,.jpeg,.png,.webp"
                            required>

                        <small>
                            JPG, PNG, WEBP | Maximum Size: 2 MB
                        </small>

                        <img
                            id="myjat-photo-preview"
                            class="myjat-photo-preview"
                            src=""
                            alt="">

                    </div>

                    <!-- ========================= -->
                    <!-- Declaration -->
                    <!-- ========================= -->
                    <div class="myjat-section">
                        <h3 class="myjat-section-title">
                            प्रतिज्ञा:
                        </h3>

                        <div class="myjat-form-group">
                            <label>
                                <input class="myjat-checkbox"
                                    type="checkbox"
                                    name="declaration"
                                    value="1"
                                    required>
                                मैं, अखिल भारतवर्षीय जाट सभा (रजि०) के विधान एवं नियमावली को पढ़कर समझ लिया है।
                            </label>
                        </div>

                        <div class="myjat-form-group">
                            <label>
                                <input class="myjat-checkbox"
                                    type="checkbox"
                                    name="declaration"
                                    value="1"
                                    required>
                                मैं, संस्था के विधान एवं नियमावली को सर्वोपरि मानकर पालन करूंगा/करूंगी।
                            </label>
                        </div>

                        <div class="myjat-form-group">
                            <label>
                                <input class="myjat-checkbox"
                                    type="checkbox"
                                    name="declaration"
                                    value="1"
                                    required>
                                मैं, किसी भी प्रकार से देश विरोधी व संस्था विरोधी कार्यों में शामिल नहीं रहूंगा/रहूंगी।
                            </label>
                        </div>

                        <div class="myjat-form-group">
                            <label>
                                <input class="myjat-checkbox"
                                    type="checkbox"
                                    name="declaration"
                                    value="1"
                                    required>
                                मैं, संस्था से संबंधित सभी प्रकार की गतिविधियों के लिए अनुशासन समिति के आदेशों का पालन करूंगा/करूंगी।
                            </label>
                        </div>

                        <div class="myjat-form-group">
                            <label>
                                <input class="myjat-checkbox"
                                    type="checkbox"
                                    name="declaration"
                                    value="1"
                                    required>
                                मैं, स्वेच्छा से संस्था की साधारण/सक्रिय/संरक्षक सदस्यता हेतु क्रमशः ₹250/- ₹500/- ₹1100/- जमा कर आजीवन सदस्य बनना चाहता/चाहती हूं।
                            </label>
                        </div>

                        <button
                            type="submit"
                            name="myjat_membership_submit"
                            value="1"
                            class="myjat-btn myjat-btn-primary myjat-btn-block">
                            सदस्यता आवेदन जमा करें
                        </button>


    </form>

    </div>

<?php
    return ob_get_clean();
}


add_shortcode('jat_membership_form', 'myjat_membership_form_shortcode');
