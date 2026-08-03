<?php
// =========================================================
// MYJAT Membership Management System
// Module: Organization Settings
// =========================================================

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function myjat_settings_page() {

    echo '<div class="wrap">';
    echo '<h1>Organization Settings</h1>';

    echo '<p>Logo, signatures aur organization details yahan se manage honge.</p>';

    echo '<table class="widefat striped">';

   
   echo '<tr>
<th>Organization Logo</th>
<td>
<img src="https://myjat.in/wp-content/uploads/2026/06/jat-final-logo-AGAIN-DONE-6-01-01.png"
class="myjat-settings-logo">
</td>
</tr>';
   
   
echo '<tr>
<th>President Signature</th>
<td>
<img src="https://myjat.in/wp-content/uploads/2026/06/president-sign.png.jpg"
class="myjat-settings-signature">
</td>
</tr>';

 echo '<tr>
<th>General Secretary Signature</th>
<td>
<img src="https://myjat.in/wp-content/uploads/2026/06/general-secretary-sign.png"
class="myjat-settings-signature">
</td>
</tr>';

    echo '</table>';

    echo '</div>';
}



