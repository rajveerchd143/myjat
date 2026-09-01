<?php

// =========================================================
// MYJAT Membership Registration Form
// Standalone PHP Version
// =========================================================

function myjat_membership_form($pdo)
{
    ob_start();

    // =====================================================
    // Current User
    // =====================================================

    $current_user = $_SESSION['myjat_user'] ?? null;


    if (!$current_user) {

        return myjat_login_page();
    }



    $google_name =
        $current_user['display_name']
        ?? $current_user['name']
        ?? '';

    $google_email =
        $current_user['email']
        ?? $current_user['user_email']
        ?? '';

?>

    <form
        method="post"
        enctype="multipart/form-data"
        class="myjat-membership-form"
        id="myjat-membership-form"
        autocomplete="off"
        novalidate>

        <!-- Header -->

        <div class="myjat-form-header">

            <h1>
                अखिल भारतवर्षीय जाट महासभा
            </h1>

            <h2>
                सदस्यता आवेदन पत्र
            </h2>

            <div class="myjat-form-meta">

                <div>

                    <strong>दिनांक :</strong>

                    <?= htmlspecialchars(
                        date('d-m-Y'),
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </div>

                <div>

                    <strong>सदस्यता क्रमांक :</strong>

                    आवेदन स्वीकृति के बाद जारी होगा

                </div>

            </div>

        </div>


        <?php if (
            isset($_GET['membership']) &&
            $_GET['membership'] === 'submitted'
        ) : ?>

            <div class="myjat-success-message">

                <strong>
                    ✅ आवेदन सफलतापूर्वक जमा हो गया।
                </strong>

                <br>

                सदस्यता क्रमांक :

                <strong>

                    <?= htmlspecialchars(
                        $_GET['member_no'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </strong>

            </div>

        <?php endif; ?>


        <?php if (
            isset($_GET['application']) &&
            $_GET['application'] === 'exists'
        ) : ?>

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

                <label>
                    पूरा नाम *
                </label>

                <input
                    class="myjat-input"
                    type="text"
                    name="full_name"
                    maxlength="100"
                    autocomplete="name"
                    required
                    value="<?= htmlspecialchars(
                                $google_name,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>">

            </div>


            <div class="myjat-form-group">

                <label>
                    पिता का नाम *
                </label>

                <input
                    class="myjat-input"
                    type="text"
                    name="father_husband_name"
                    maxlength="100"
                    autocomplete="off"
                    required>

            </div>


            <div class="myjat-form-group">

                <label>
                    जन्म तिथि *
                </label>

                <input
                    class="myjat-input"
                    type="date"
                    name="dob"
                    required>

            </div>


            <div class="myjat-form-group">

                <label>
                    लिंग *
                </label>

                <select
                    class="myjat-select"
                    name="gender"
                    required>

                    <option value="">
                        चुनें
                    </option>

                    <option>
                        पुरुष
                    </option>

                    <option>
                        महिला
                    </option>

                    <option>
                        अन्य
                    </option>

                </select>

            </div>


            <div class="myjat-form-group">

                <label>
                    शिक्षा
                </label>

                <input
                    class="myjat-input"
                    type="text"
                    name="education"
                    maxlength="100">

            </div>


            <div class="myjat-form-group">

                <label>
                    व्यवसाय
                </label>

                <input
                    class="myjat-input"
                    type="text"
                    name="profession"
                    maxlength="100">

            </div>


            <!-- Contact -->

            <div class="myjat-form-group">

                <label>
                    मोबाइल नंबर *
                </label>

                <input
                    class="myjat-input"
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

                <label>
                    ईमेल
                </label>

                <input
                    class="myjat-input"
                    type="email"
                    name="email"
                    maxlength="100"
                    autocomplete="email"
                    spellcheck="false"
                    value="<?= htmlspecialchars(
                                $google_email,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                    <?= $google_email ? 'readonly' : ''; ?>>

            </div>


            <!-- Address -->

            <div class="myjat-form-group">

                <label>
                    वर्तमान पता *
                </label>

                <textarea
                    class="myjat-textarea"
                    name="current_address"
                    rows="3"
                    maxlength="500"
                    required></textarea>

            </div>


            <div class="myjat-form-group">

                <label>
                    स्थायी पता
                </label>

                <textarea
                    class="myjat-textarea"
                    name="permanent_address"
                    rows="3"
                    maxlength="500"></textarea>

            </div>


            <div class="myjat-form-group">

                <label>
                    आधार संख्या
                </label>

                <input
                    class="myjat-input"
                    type="text"
                    name="aadhaar_no"
                    maxlength="12"
                    minlength="12"
                    pattern="[0-9]{12}"
                    inputmode="numeric"
                    autocomplete="off">

            </div>


            <div class="myjat-form-group">

                <label>
                    फोन नंबर
                </label>

                <input
                    class="myjat-input"
                    type="tel"
                    name="phone_no"
                    maxlength="10"
                    inputmode="numeric">

            </div>


            <div class="myjat-form-group">

                <label>
                    परिवार का विवरण
                </label>

                <textarea
                    class="myjat-textarea"
                    name="family_details"
                    rows="4"></textarea>

            </div>


            <!-- =================================================
                 निर्वाचन क्षेत्र की जानकारी
            ================================================== -->

            <div class="myjat-section">

                <h3 class="myjat-section-title">
                    निर्वाचन क्षेत्र की जानकारी
                </h3>


                <div class="myjat-form-group">

                    <label>
                        राज्य *
                    </label>

                    <select
                        name="state_name"
                        id="myjat-state"
                        class="myjat-select"
                        required>

                        <option value="">
                            राज्य चुनें
                        </option>

                        <?php

                        $states =
                            MyJat_Location_Service::get_states($pdo);

                        foreach ($states as $state) :

                        ?>

                            <option
                                value="<?= htmlspecialchars(
                                            $state,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>">

                                <?= htmlspecialchars(
                                    $state,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="myjat-form-group">

                    <label>
                        जिला *
                    </label>

                    <select
                        name="district"
                        id="myjat-district"
                        class="myjat-select"
                        required>

                        <option value="">
                            जिला चुनें
                        </option>

                    </select>

                </div>


                <div class="myjat-form-group">

                    <label>
                        ब्लॉक
                    </label>

                    <select
                        id="myjat-block"
                        name="block_name"
                        class="myjat-select"
                        required>

                        <option value="">
                            ब्लॉक चुनें
                        </option>

                    </select>

                </div>


                <div class="myjat-form-group">

                    <label>
                        ग्राम / पंचायत
                    </label>

                    <select
                        id="myjat-village"
                        name="village_panchayat"
                        class="myjat-select">

                        <option value="">
                            ग्राम / पंचायत चुनें
                        </option>

                    </select>

                </div>


                <div class="myjat-form-group">

                    <label>
                        पिन कोड *
                    </label>

                    <input
                        type="text"
                        id="myjat-pincode"
                        name="pincode"
                        class="myjat-input"
                        maxlength="6"
                        required>

                </div>


                <div class="myjat-form-group">

                    <label>
                        विधानसभा
                    </label>

                    <input
                        class="myjat-input"
                        type="text"
                        name="vidhansabha">

                </div>


                <div class="myjat-form-group">

                    <label>
                        लोकसभा
                    </label>

                    <input
                        class="myjat-input"
                        type="text"
                        name="loksabha">

                </div>

                <!-- =================================================
                     Membership Information
                ================================================== -->

                <div class="myjat-section">

                    <h3 class="myjat-section-title">
                        सदस्यता जानकारी
                    </h3>


                    <div class="myjat-form-group">

                        <label>
                            सदस्यता प्रकार *
                        </label>

                        <select
                            class="myjat-select"
                            name="membership_type"
                            required>

                            <option value="">
                                सदस्यता प्रकार चुनें
                            </option>

                            <option value="साधारण सदस्य">
                                साधारण सदस्य
                            </option>

                            <option value="सक्रिय सदस्य">
                                सक्रिय सदस्य
                            </option>

                            <option value="संरक्षक सदस्य">
                                संरक्षक सदस्य
                            </option>

                        </select>

                    </div>


                    <!-- =================================================
                         Recommender
                    ================================================== -->

                    <h3 class="myjat-section-title">
                        अनुशंसक की जानकारी
                    </h3>


                    <div class="myjat-form-group">

                        <label>
                            अनुशंसक का नाम
                        </label>

                        <input
                            class="myjat-input"
                            type="text"
                            name="recommender_name"
                            maxlength="100">

                    </div>


                    <div class="myjat-form-group">

                        <label>
                            अनुशंसक सदस्यता क्रमांक
                        </label>

                        <input
                            class="myjat-input"
                            type="text"
                            name="recommender_membership_no"
                            maxlength="50">

                    </div>


                    <div class="myjat-form-group">

                        <label>
                            अनुशंसक मोबाइल नंबर
                        </label>

                        <input
                            class="myjat-input"
                            type="tel"
                            name="recommender_mobile"
                            maxlength="10"
                            inputmode="numeric">

                    </div>


                    <!-- =================================================
                         Member Photo
                    ================================================== -->

                    <h3 class="myjat-section-title">
                        सदस्य का फोटो
                    </h3>


                    <div class="myjat-form-group">

                        <label>
                            फोटो *
                        </label>

                        <input
                            class="myjat-input"
                            type="file"
                            name="photo"
                            id="photo"
                            accept="image/jpeg,image/png,image/webp"
                            required>

                        <img
                            id="myjat-photo-preview"
                            src=""
                            alt="Photo Preview"
                            style="display:none;">

                    </div>


                    <!-- =================================================
                         Declaration
                    ================================================== -->

                    <div class="myjat-form-group">

                        <label>

                            <input
                                type="checkbox"
                                name="declaration"
                                value="1"
                                required>

                            मैं घोषणा करता/करती हूँ कि मेरे द्वारा दी गई
                            सभी जानकारी सही है।

                        </label>

                    </div>


                    <div class="myjat-form-actions">

                        <button
                            type="submit"
                            name="myjat_membership_submit"
                            value="1"
                            class="myjat-primary myjat-btn">

                            सदस्यता आवेदन जमा करें

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

<?php

    return ob_get_clean();
}
