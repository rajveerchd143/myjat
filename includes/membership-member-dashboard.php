<?php

if(!defined('ABSPATH')){
	exit;
}

add_shortcode('myjat_member_dashboard','myjat_member_dashboard_shortcode');

function myjat_member_dashboard_shortcode(){

	if(!is_user_logged_in()){
		return '<div class="myjat-warning-message">Please login first.</div>';
	}

	global $wpdb;

	$table=$wpdb->prefix.'membership_applications';

	$user_id=get_current_user_id();

	$app=$wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE wp_user_id=%d LIMIT 1",
			$user_id
		)
	);

	if(!$app){
		return '<div class="myjat-warning-message">No membership record found.</div>';
	}

	ob_start();

	?>

	<div class="myjat-dashboard">

		<h2>Welcome, <?php echo esc_html($app->full_name); ?></h2>

		<div class="myjat-dashboard-grid">

			<div class="myjat-dashboard-card">

				<h3>Membership Status</h3>

				<p><strong><?php echo esc_html(ucfirst($app->status)); ?></strong></p>

			</div>

			<div class="myjat-dashboard-card">

				<h3>Membership Number</h3>

				<p><?php echo esc_html($app->membership_no ?: 'Pending'); ?></p>

			</div>

			<div class="myjat-dashboard-card">

				<h3>PVC Card</h3>

				<?php if($app->status==='approved'){ ?>

					<a class="button" href="<?php echo esc_url(home_url('/member-card/?id='.$app->id)); ?>">
						View PVC Card
					</a>

				<?php }else{ ?>

					<p>Available after approval.</p>

				<?php } ?>

			</div>

			<div class="myjat-dashboard-card">

				<h3>Application</h3>

				<a class="button" href="<?php echo esc_url(home_url('/application-status')); ?>">
					View Status
				</a>

			</div>

		</div>

	</div>

	<?php

	return ob_get_clean();

}