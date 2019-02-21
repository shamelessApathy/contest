<?php

/**
 * Add the Crypto Wallet Calculator widget to the dashboard.
 */
add_action( 'wp_dashboard_setup', 'cryptocurrency_add_dash_affiliate_widget' );
function cryptocurrency_add_dash_affiliate_widget() {
  if ( current_user_can( 'manage_options' ) ) {
    add_meta_box('cryptocurrency_dash_affiliate_widget', 'Zwaply Affiliate Panel', 'cryptocurrency_dash_affiliate_widget_function', 'dashboard', 'side', 'high');
  }
}

/**
 * Output the contents of our Crypto Wallet widget.
 */
function cryptocurrency_dash_affiliate_widget_function() {
  $scrolling = 'yes';
  include_once dirname( __FILE__ ) . '/../parts/cryptocurrency-affiliate-panel.php';
}
