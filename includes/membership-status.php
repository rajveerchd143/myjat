<?php

if(!defined('ABSPATH')){
	exit;
}

add_shortcode('myjat_application_status','myjat_application_status_shortcode');

function myjat_application_status_shortcode(){

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
		return '<div class="myjat-warning-message">No membership application found.</div>';
	}

	ob_start();
	?>

	<div class="myjat-status-card">

		<h2>Membership Application Status</h2>

		<table class="myjat-status-table">

			<tr>
				<th>Name</th>
				<td><?php echo esc_html($app->full_name); ?></td>
			</tr>

			<tr>
				<th>Membership No.</th>
				<td><?php echo esc_html($app->membership_no ?: 'Pending'); ?></td>
			</tr>

			<tr>
				<th>Status</th>
				<td>
					<strong><?php echo esc_html(ucfirst($app->status)); ?></strong>
				</td>
			</tr>

			<tr>
				<th>Application Date</th>
				<td><?php echo esc_html($app->application_date); ?></td>
			</tr>

		</table>

	</div>

	<?php

	return ob_get_clean();

}