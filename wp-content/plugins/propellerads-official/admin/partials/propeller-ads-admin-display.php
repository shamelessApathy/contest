<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */

if (isset($_GET['settings-updated'])) {
	Propeller_Ads_Messages::add_message(__('Settings Updated', 'propeller-ads'));
}
Propeller_Ads_Messages::show_messages();
?>

<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

	<form class="propeller-ads" action="options.php" method="post">
		<?php if ($this->setting_helper->get_anti_adblock_token()): ?>
			<p class="submit">
				<a href="<?php echo $this->plugin_url() ?>&update-publisher-zones" id="update-zones"
				   class="button button-primary">Update zones
					list</a>
			</p>
		<?php else: ?>
			<p class="submit">
				<a href="<?php echo $this->token_url() ?>" id="get-token"
				   class="button button-primary">Connect to PropellerAds SSP</a>
			</p>
		<?php endif; ?>

		<?php settings_fields($this->plugin_name); ?>
		<div class="card-wrapper">
			<?php $this->setting_helper->do_settings_sections($this->plugin_name) ?>
		</div>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>

