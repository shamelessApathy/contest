<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link        https://zwaply.com
 * @author      Zwaply <info@zwaply.com>
 */
?>

<div class="wrap">
	<iframe src="https://zwaply.com/affiliate-login?username=<?php echo esc_attr( get_option( 'cryptocurrency-prices-zwaply-affiliate-id' ) ); ?>" frameborder="0" style="width: 100%;height: 100%;" scrolling="<?php echo isset($scrolling) && 'yes' === $scrolling ? 'yes' : 'no'; ?>"></iframe>
</div>
