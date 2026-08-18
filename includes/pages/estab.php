<?php
if (!defined('ABSPATH')) exit;
function myjat_Estab_home()
{
    ob_start(); ?>
    <section class="myjat-home myjat-glassborder">

    <div class="myjat-orb myjat-orb-1"></div>
    <div class="myjat-orb myjat-orb-2"></div>
    <div class="myjat-orb myjat-orb-3"></div>
    
    <div class="menu-para myjat-glass">
            <h1 class="head-h1">
            स्थापना
        </h1>
    <hr class="menu-line">
    <h3 class="menu-bullet">वर्ष 1905 </h3>
    <p>स्थान <strong>मुजफ्फरनगर</strong> के नुमाइश मैदान स्थित जाट आश्रम में प्रबंधन व्यवस्था के गठन हेतु प्रथम बैठक बुलाई गई। इसी बैठक में चार सदस्यीय सभा के गठन का विचार प्रस्तुत किया गया।<br><br></p>
    
    <strong>सभा के सदस्य:</strong><br>
    <p>
    • चौधरी मामराज सिंह जी – शामली<br>
    • श्री हरिराम सिंह जी – कुरमाली<br>
    • श्री खूबचंद सिंह जी – कुडाना<br>
    • श्री मुख्तियार सिंह जी – मेरठ<br>
    • श्री शादी राम वर्मा जी – मेरठ<br>
</p>
    <hr class="menu-line">
    <h3 class="menu-bullet">वर्ष 1906</h3>

   <p> दूसरी बैठक देवता समाज, बिजनौर में गंगा स्नान के अवसर पर आयोजित की गई। इस बैठक में समिति का गठन किया गया, जिसमें:<br>

    • कुंवर कल्याण सिंह जी – अध्यक्ष<br>
    • ठाकुर तेज सिंह जी – मंत्री<br>

    इसके पश्चात 18–20 फरवरी को बुलंदशहर के नुमाइश मैदान में शिविर लगाकर आम सभा आयोजित की गई। इस सभा में निम्न पदाधिकारियों का चयन किया गया:<br><br>

    • राम बहादुर गिरजा सिंह जी – प्रधान<br>
    • श्री तेज सिंह जी – मंत्री<br>
    • कुंवर करना सिंह जी (ऊंचा गांव) – कोषाध्यक्ष<br>

    इसी अधिवेशन में पहली मासिक पत्रिका <strong>“जाट हितकारी”</strong> प्रकाशित करने का निर्णय लिया गया।<br>
    </p>

    <hr class="menu-line">
    <h3 class="menu-bullet">वर्ष 1907</h3>
<p>
    प्रथम अधिवेशन राजस्थान के पुष्कर मेला प्रांगण में आयोजित किया गया।<br>
</p>
    <hr class="menu-line">
    <h3 class="menu-bullet">वर्ष 1908</h3>
<p>
    राजा दत्त प्रसाद जी – मुरमान।
</p>
</h3>
    </div>


    </section>
<?php
    return ob_get_clean();
}
add_shortcode('myjat_Estab', 'myjat_Estab_home');
