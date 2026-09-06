<?php

// =========================================================
// MYJAT Membership Registration Form
// Standalone PHP Version
// =========================================================

function myjat_membership_form($pdo)
{
    ob_start();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

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

    // =====================================================
    // Submission / Existing Application Status
    // =====================================================

    $membership_flash = $_SESSION['myjat_membership_flash'] ?? null;
    unset($_SESSION['myjat_membership_flash']);

    $existing_application = null;

    if ($google_email) {

        $stmt = $pdo->prepare(
            "SELECT id, membership_no, status
             FROM membership_applications
             WHERE email = ?
                OR google_email = ?
             ORDER BY id DESC
             LIMIT 1"
        );

        $stmt->execute([
            $google_email,
            $google_email
        ]);

        $existing_application =
            $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    // =====================================================
    // Location Data
    // =====================================================

    $states = MyJat_Location_Service::get_states($pdo);

?>

    <?php if (
        $membership_flash &&
        ($membership_flash['type'] ?? '') === 'success'
    ) : ?>

        <div class="myjat-success-message">

            <strong>
                ✅ आपका सदस्यता आवेदन सफलतापूर्वक जमा हो गया है।
            </strong>

            <br><br>

            सदस्यता क्रमांक :
            <strong>
                <?= htmlspecialchars(
                    $membership_flash['member_no'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>

            <br><br>

            आपका आवेदन जाँच / स्वीकृति के लिए भेज दिया गया है।

        </div>


    <?php elseif ($existing_application) : ?>

        <div class="myjat-warning-message">

            <strong>
                आप पहले ही सदस्यता आवेदन जमा कर चुके हैं।
            </strong>

            <br><br>

            सदस्यता क्रमांक :
            <strong>
                <?= htmlspecialchars(
                    $existing_application['membership_no'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>

            <br><br>

            स्थिति :
            <strong>
                <?= htmlspecialchars(
                    $existing_application['status'] ?? 'Pending',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>

            <br><br>

            नया आवेदन जमा करने की आवश्यकता नहीं है।

        </div>


    <?php else : ?>

        <form
            method="post"
            enctype="multipart/form-data"
            class="myjat-membership-form"
            id="myjat-membership-form"
            autocomplete="off"
            novalidate>

            <!-- =================================================
             FORM HEADER
        ================================================== -->

            <div class="myjat-form-header">

                <h1>अखिल भारतवर्षीय जाट महासभा</h1>

                <h2>सदस्यता आवेदन पत्र</h2>

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


            <!-- =================================================
             MESSAGES
        ================================================== -->




            <!-- =================================================
             PROGRESS
        ================================================== -->

            <div class="myjat-registration-progress">

                <div class="myjat-progress-step is-active" data-step="1">
                    <span>1</span>
                    <strong>व्यक्तिगत जानकारी</strong>
                </div>

                <div class="myjat-progress-line"></div>

                <div class="myjat-progress-step" data-step="2">
                    <span>2</span>
                    <strong>पता एवं क्षेत्र</strong>
                </div>

                <div class="myjat-progress-line"></div>

                <div class="myjat-progress-step" data-step="3">
                    <span>3</span>
                    <strong>सदस्यता</strong>
                </div>

                <div class="myjat-progress-line"></div>

                <div class="myjat-progress-step" data-step="4">
                    <span>4</span>
                    <strong>संकल्प</strong>
                </div>

            </div>


            <!-- =================================================
             STEP 1 - PERSONAL INFORMATION
        ================================================== -->

            <section class="myjat-registration-step is-active" data-step="1">

                <div class="myjat-section-heading">
                    <h3>व्यक्तिगत जानकारी</h3>
                    <p>अपनी मूल जानकारी भरें।</p>
                </div>


                <div class="myjat-form-grid">

                    <div class="myjat-form-group">
                        <label for="full_name">पूरा नाम *</label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="full_name"
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
                        <label for="father_husband_name">
                            पिता / पति का नाम *
                        </label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="father_husband_name"
                            name="father_husband_name"
                            maxlength="100"
                            required>
                    </div>


                    <div class="myjat-form-group">
                        <label for="dob">जन्म तिथि *</label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="dob_display"
                            name="dob_display"
                            placeholder="dd/mm/yyyy"
                            maxlength="10"
                            inputmode="numeric"
                            autocomplete="bday"
                            pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"
                            required>

                        <input
                            type="hidden"
                            id="dob"
                            name="dob"
                            value="">
                    </div>



                    <div class="myjat-form-group">
                        <label for="gender">लिंग *</label>

                        <select
                            class="myjat-select"
                            id="gender"
                            name="gender"
                            required>

                            <option value="">लिंग चुनें</option>
                            <option value="पुरुष">पुरुष</option>
                            <option value="महिला">महिला</option>
                            <option value="अन्य">अन्य</option>

                        </select>
                    </div>


                    <div class="myjat-form-group">
                        <label for="education">शिक्षा</label>

                        <select
                            class="myjat-select"
                            id="education"
                            name="education">

                            <option value="">शिक्षा चुनें</option>
                            <option value="निरक्षर">निरक्षर</option>
                            <option value="प्राथमिक">प्राथमिक</option>
                            <option value="माध्यमिक">माध्यमिक</option>
                            <option value="उच्च माध्यमिक">उच्च माध्यमिक</option>
                            <option value="स्नातक">स्नातक</option>
                            <option value="स्नातकोत्तर">स्नातकोत्तर</option>
                            <option value="डिप्लोमा">डिप्लोमा</option>
                            <option value="आईटीआई">आईटीआई</option>
                            <option value="अन्य">अन्य</option>

                        </select>
                    </div>


                    <div class="myjat-form-group">
                        <label for="profession">व्यवसाय</label>

                        <select
                            class="myjat-select"
                            id="profession"
                            name="profession">

                            <option value="">व्यवसाय चुनें</option>
                            <option value="कृषि">कृषि</option>
                            <option value="व्यापार">व्यापार</option>
                            <option value="सरकारी नौकरी">सरकारी नौकरी</option>
                            <option value="निजी नौकरी">निजी नौकरी</option>
                            <option value="स्वरोजगार">स्वरोजगार</option>
                            <option value="विद्यार्थी">विद्यार्थी</option>
                            <option value="शिक्षक">शिक्षक</option>
                            <option value="वकील">वकील</option>
                            <option value="डॉक्टर">डॉक्टर</option>
                            <option value="सेवानिवृत्त">सेवानिवृत्त</option>
                            <option value="गृहिणी">गृहिणी</option>
                            <option value="अन्य">अन्य</option>

                        </select>
                    </div>

                    <div class="myjat-form-group">
                        <label for="mobile_no">मोबाइल नंबर *</label>

                        <input
                            class="myjat-input"
                            type="tel"
                            id="mobile_no"
                            name="mobile_no"
                            maxlength="10"
                            minlength="10"
                            pattern="[0-9]{10}"
                            inputmode="numeric"
                            autocomplete="tel"
                            required>
                    </div>


                    <div class="myjat-form-group">
                        <label for="email">ईमेल</label>

                        <input
                            class="myjat-input"
                            type="email"
                            id="email"
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

                </div>


                <div class="myjat-form-actions">
                    <button type="button" class="myjat-btn myjat-btn-next">
                        आगे बढ़ें →
                    </button>
                </div>

            </section>


            <!-- =================================================
             STEP 2 - ADDRESS & LOCATION
        ================================================== -->

            <section class="myjat-registration-step" data-step="2">

                <div class="myjat-section-heading">
                    <h3>पता एवं क्षेत्र</h3>
                    <p>अपने वर्तमान और क्षेत्र से संबंधित विवरण भरें।</p>
                </div>


                <div class="myjat-form-group">
                    <label for="current_address">वर्तमान पता *</label>

                    <textarea
                        class="myjat-textarea"
                        id="current_address"
                        name="current_address"
                        rows="3"
                        maxlength="500"
                        required></textarea>
                </div>


                <div class="myjat-same-address">
                    <label>
                        <input
                            type="checkbox"
                            id="same-address"
                            name="same_address"
                            value="1">

                        वर्तमान पता और स्थायी पता समान है
                    </label>
                </div>
                <div class="myjat-form-group">
                    <label for="permanent_address">स्थायी पता</label>

                    <textarea
                        class="myjat-textarea"
                        id="permanent_address"
                        name="permanent_address"
                        rows="3"
                        maxlength="500"></textarea>
                </div>


                <div class="myjat-form-grid">

                    <div class="myjat-form-group">
                        <label for="myjat-state">राज्य *</label>

                        <select
                            name="state_name"
                            id="myjat-state"
                            class="myjat-select"
                            required>

                            <option value="">राज्य चुनें</option>

                            <?php foreach ($states as $state) : ?>

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
                        <label for="myjat-district">जिला *</label>

                        <select
                            name="district"
                            id="myjat-district"
                            class="myjat-select"
                            required>

                            <option value="">जिला चुनें</option>

                        </select>
                    </div>


                    <div class="myjat-form-group">
                        <label for="myjat-block">ब्लॉक</label>

                        <select
                            id="myjat-block"
                            name="block_name"
                            class="myjat-select">

                            <option value="">ब्लॉक चुनें</option>

                        </select>
                    </div>


                    <div class="myjat-form-group">
                        <label for="myjat-village">ग्राम / पंचायत</label>

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
                        <label for="myjat-pincode">पिन कोड *</label>

                        <input
                            type="text"
                            id="myjat-pincode"
                            name="pincode"
                            class="myjat-input"
                            maxlength="6"
                            minlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            required>
                    </div>


                    <div class="myjat-form-group">
                        <label for="vidhansabha">विधानसभा</label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="vidhansabha"
                            name="vidhansabha"
                            maxlength="100">
                    </div>


                    <div class="myjat-form-group">
                        <label for="loksabha">लोकसभा</label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="loksabha"
                            name="loksabha"
                            maxlength="100">
                    </div>

                </div>


                <div class="myjat-form-actions">

                    <button type="button" class="myjat-btn myjat-btn-prev">
                        ← पीछे
                    </button>

                    <button type="button" class="myjat-btn myjat-btn-next">
                        आगे बढ़ें →
                    </button>

                </div>

            </section>


            <!-- =================================================
             STEP 3 - MEMBERSHIP
        ================================================== -->

            <section class="myjat-registration-step" data-step="3">

                <div class="myjat-section-heading">
                    <h3>सदस्यता जानकारी</h3>
                    <p>सदस्यता और अनुशंसक से संबंधित जानकारी भरें।</p>
                </div>


                <div class="myjat-form-group">
                    <label for="membership_type">सदस्यता प्रकार *</label>

                    <select
                        class="myjat-select"
                        id="membership_type"
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


                <div class="myjat-section-heading myjat-sub-heading">
                    <h3>अनुशंसक की जानकारी</h3>
                </div>


                <div class="myjat-form-grid">

                    <div class="myjat-form-group">
                        <label for="recommender_name">
                            अनुशंसक का नाम
                        </label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="recommender_name"
                            name="recommender_name"
                            maxlength="100">
                    </div>


                    <div class="myjat-form-group">
                        <label for="recommender_membership_no">
                            
                            अनुशंसक सदस्यता क्रमांक
                        </label>

                        <input
                            class="myjat-input"
                            type="text"
                            id="recommender_membership_no"
                            name="recommender_membership_no"
                            maxlength="50">
                    </div>


                    <div class="myjat-form-group">
                        <label for="recommender_mobile">
                            अनुशंसक मोबाइल नंबर
                        </label>

                        <input
                            class="myjat-input"
                            type="tel"
                            id="recommender_mobile"
                            name="recommender_mobile"
                            maxlength="10"
                            pattern="[0-9]{10}"
                            inputmode="numeric">
                    </div>

                </div>


                <div class="myjat-form-actions">

                    <button type="button" class="myjat-btn myjat-btn-prev">
                        ← पीछे
                    </button>

                    <button type="button" class="myjat-btn myjat-btn-next">
                        आगे बढ़ें →
                    </button>

                </div>

            </section>


            <!-- =================================================
             STEP 4 - SANKALP & CONFIRMATION
        ================================================== -->

            <section class="myjat-registration-step" data-step="4">

                <div class="myjat-section-heading">
                    <h3>ॐ संकल्प ॐ</h3>
                    <p>कृपया संकल्प के सभी बिंदु ध्यानपूर्वक पढ़ें।</p>
                </div>


                <div class="myjat-sankalp">

                    <p>
                        मैं अखिल भारतवर्षीय जाट महासभा (रजि० बी-1254)
                        के साधारण सक्रिय संरक्षक सदस्य बनने के लिए
                        संकल्प लेता/लेती हूं कि:-
                    </p>

                    <ol>

                        <li>
                            मैं किसी भी प्रकार के नशे से दूर रहूंगा/रहूंगी,
                            कभी भी किसी को नशे के लिए प्रेरित नहीं करूंगा/करूंगी।
                        </li>

                        <li>
                            मैं अपने परिवार के किसी भी सदस्य की मृत्यु हो जाने पर
                            मृत्युभोज का पूर्ण रूप से बहिष्कार करते हुए ना तो खाऊंगा/खाऊंगी
                            और ना ही करूंगा/करूंगी एवं सामाजिक कुरीतियों को पूर्ण रूप से
                            रोकने की सकारात्मक पहल करता/करती रहूंगा/रहूंगी।
                        </li>

                        <li>
                            मैं अपने बच्चों की शादी में किसी प्रकार का दहेज नहीं
                            लूंगा/लूंगी, दहेज रूपी कुरीतियों से अपने परिचितों को भी
                            दहेज के लेनदेन से दूर रखने का प्रयास करूंगा/करूंगी।
                            साथ ही मैं अपने बच्चों को अंतरजातीय एवं सगोत्र शादी करने
                            से रोकूंगा/रोकूंगी और यदि गलती से बच्चे ऐसा कर भी लेते हैं
                            तो मैं सामाजिक रीति-रिवाजों से शादी का समारोह नहीं करूंगा/करूंगी।
                        </li>

                        <li>
                            मैं स्वयं आज से ही कक्षा 6 से कक्षा 12 तक के विद्यार्थियों को
                            सामाजिक धार्मिक सनातनी परंपरा की जानकारी देने के लिए
                            स्कूल जाकर गोष्ठी आयोजित कर समाज में व्यापक कुरीतियों की
                            जानकारी देकर उन कुरीतियों को दूर करने का संकल्प कराऊंगा/कराऊंगी।
                        </li>

                        <li>
                            मैं अपने सजातीय बंधुओं को श्री रोजगार की दिशा में चलने के लिए
                            प्रेरित कर समूह बनवाकर व्यापार करने की दिशा में सहयोगी
                            बनूं/बनूंगी, सरकारी योजना का लाभ दिलाने हेतु सहयोगी बनूं/बनूंगी।
                        </li>

                        <li>
                            मैं संस्था हित में संस्था के क्रियाकलापों में अपनी स्वयं की
                            भूमिका को सकारात्मक रखूं/रखूंगी। संस्था में आपसी गुटबाजी को
                            बढ़ावा नहीं दूं/दूंगी। बस मुझे केवल देना है, कुछ भी लेना नहीं
                            है—इस प्रेरणा से समर्पित रहते हुए सहयोगी बनूं/बनूंगी।
                            संगठनात्मक मनोरंजन में संरक्षक मंडल के लिए निर्णय का
                            समर्पण करूं/करूंगी।
                        </li>

                        <li>
                            मैं संस्था के नाम से कभी भी किसी भी प्रकार से कोई भी
                            छुपा कर चंदा अथवा आर्थिक सहयोग नहीं लूं/लूंगी।
                        </li>

                        <li>
                            मैं अपने आसपास के सजातीय बंधुओं के आपसी झगड़ों, कोर्ट केस आदि
                            में पार्टी न बनते हुए उनके विवादों को आपसी वार्तालाप से
                            समाप्त करने में सहयोगी भूमिका दूंगा/दूंगी।
                        </li>

                        <li>
                            मैं संस्था की सरपरस्ती हेतु ली जाने वाली अपनी शुल्क राशि
                            जिसे नगद में जमा नहीं करूं/करूंगी, उसको कभी भी वापसी की
                            मांग नहीं करूं/करूंगी।
                        </li>

                        <li>
                            मैं अपने माता-पिता को भगवान की श्रेणी में रखते हुए उनके लिए
                            समर्पित रहते हुए उनके जीवन को आनंदमय बनाने में अपने को
                            समाहित करूं/करूंगी। माता-पिता की इच्छा को ही आदेश मानकर
                            पूरा करना ही मेरे जीवन का उद्देश्य रहेगा।
                        </li>

                        <li>
                            मैं गौ सेवा, गोपालन करवाते हुए अपने जनजातीय आर्थिक रूप से
                            कमजोर बंधुओं के बच्चों की शिक्षा को अपनी जिम्मेदारी समझूं/समझूंगी।
                            अच्छा आर्थिक सहयोग करना मेरी जिम्मेदारी रहेगी।
                        </li>

                    </ol>

                </div>


                <!-- =================================================
                 PHOTO
            ================================================== -->

                <div class="myjat-section-heading myjat-sub-heading">
                    <h3>सदस्य का फोटो</h3>
                </div>


                <div class="myjat-form-group">

                    <label for="photo">फोटो *</label>

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
                        alt="Photo Preview">

                    <small>
                        JPG, PNG या WEBP
                    </small>

                </div>


                <!-- =================================================
                 SANKALP ACCEPTANCE
            ================================================== -->



                <div class="myjat-sankalp-agreement">

                    <label>

                        <input
                            type="checkbox"
                            name="declaration"
                            value="1"
                            required>

                        मैंने ऊपर दिए गए संकल्प को ध्यानपूर्वक पढ़ा है
                        और उसे स्वीकार करता/करती हूँ।

                    </label>

                </div>

                <div class="myjat-pratigya">

                    <h3>प्रतिज्ञा:</h3>

                    <p>
                        मैं, अखिल भारतवर्षीय जाट सभा (रजि०) के विधान एवं नियमावली को
                        पूर्णतः समझ लिया है।
                    </p>

                    <p>
                        मैं, संस्था के विधान एवं नियमावली के सर्वोपरि मानकर पालन करूंगा/करूंगी।
                    </p>

                    <p>
                        मैं, किसी भी प्रकार के नशे एवं सामाजिक कुरीतियों से दूर रहूंगा/रहूंगी।
                    </p>

                    <p>
                        मैं, संस्था से संबंधित सभी प्रकार के क्रियाकलापों के लिए
                        अनुशासन समिति के आदेशों का पालन करूंगा/करूंगी।
                    </p>

                    <p>
                        मैं, संस्था की साधारण/सक्रिय/संरक्षक सदस्यता हेतु शुल्क
                        ₹250/- / ₹500/- / ₹1100/- जमा कर आजीवन सदस्य बना/बनी हूँ।
                    </p>


                </div>

                <div class="myjat-sankalp-agreement">

                    <label>
                        <input
                            type="checkbox"
                            name="declaration"
                            value="1"
                            required>

                        मैंने ऊपर दी गई प्रतिज्ञा को ध्यानपूर्वक पढ़ लिया है
                        और अपनी सहमति प्रदान करता/करती हूँ।
                    </label>

                </div>


                <div class="myjat-form-actions">

                    <button type="button" class="myjat-btn myjat-btn-prev">
                        ← पीछे
                    </button>

                    <button
                        type="submit"
                        name="myjat_membership_submit"
                        value="1"
                        class="myjat-btn myjat-btn-submit">

                        सदस्यता आवेदन जमा करें

                    </button>

                </div>

            </section>

        </form>
    <?php endif; ?>
<?php



    return ob_get_clean();
}
