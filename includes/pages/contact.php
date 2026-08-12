<?php
if (!defined('ABSPATH')) exit;
function myjat_contact_home()
{
    ob_start(); ?>
    <section class="myjat-home">
        <div class="myjat-orb myjat-orb-1"></div>
        <div class="myjat-orb myjat-orb-2"></div>
        <div class="myjat-orb myjat-orb-3"></div>

        <div class="menu-para">

            <h1 class="head-h1">
                संपर्क करें !
            </h1>

            

            <strong>हम आपके सुझाव, सहयोग एवं सहभागिता का सदैव हार्दिक स्वागत करते हैं।</strong>

            <hr class="menu-line">

            <h3 class="menu-bullet">प्रधान कार्यालय</h3>

            <p>
                <strong>प्रेम महाविद्यालय</strong><br>
                के.सी. घाट, वृन्दावन,<br>
                जनपद मथुरा – 281121,<br>
                उत्तर प्रदेश, भारत।
            </p>

            <hr class="menu-line">

            <h3 class="menu-bullet">शाखा कार्यालय</h3>

            <p>
                <strong>चौधरी गार्डन</strong><br>
                उत्सव वाटिका,<br>
                जट्टारी, जनपद अलीगढ़ – 202137,<br>
                उत्तर प्रदेश, भारत।
            </p>

            <hr class="menu-line">

            <h3 class="menu-bullet">संपर्क विवरण</h3>

            <p>
                <strong>मोबाइल / व्हाट्सएप :</strong><br>
                +91 94122 74738
            </p>

            <p>
                <strong>ई-मेल :</strong><br>
                <a href="mailto:shersingh37499@gmail.com">
                    shersingh37499@gmail.com
                </a>
            </p>

            <hr class="menu-line">

            <h3 class="menu-bullet">हमसे जुड़ें</h3>

            <p>
                यदि आप अखिल भारतीय जाट महासभा के सदस्य बनना चाहते हैं, समाज सेवा से संबंधित किसी विषय पर सुझाव देना चाहते हैं, किसी कार्यक्रम की जानकारी प्राप्त करना चाहते हैं अथवा किसी अन्य प्रकार की सहायता या संवाद स्थापित करना चाहते हैं, तो ऊपर दिए गए संपर्क माध्यमों के द्वारा हमसे संपर्क कर सकते हैं। आपके सुझाव, सहयोग एवं सहभागिता हमारे लिए अत्यंत महत्वपूर्ण हैं और उनका सदैव सम्मान किया जाएगा।
            </p>
        </div>


    </section>
<?php
    return ob_get_clean();
}
add_shortcode('myjat_contact', 'myjat_contact_home');
