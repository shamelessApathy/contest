<?php

class CPAjax {
  public function __construct() {
    add_action( 'wp_ajax_cryptocurrency-prices-ticker', array( &$this, 'ticker' ) );
    add_action( 'wp_ajax_nopriv_cryptocurrency-prices-ticker', array( &$this, 'ticker' ) );
  }

  public function ticker() {
    if ( CPCommon::cp_get_coinmarketcap_api_key() === CPCommon::$default_coinmarketcap_api_key ) {
      $transient_ttl = 30 * MINUTE_IN_SECONDS;
    } else {
      $transient_ttl = 2 * MINUTE_IN_SECONDS;
    }

    $nonce = '';
    if ( isset( $_GET['nonce'] ) ) {
      $nonce = $_GET['nonce'];
    }

    $requestsSource = ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';


    if( ! wp_verify_nonce( $nonce, 'zwaply-ticker' )
    || $_SERVER['HTTP_HOST'] !== parse_url($requestsSource, PHP_URL_HOST) ) {
      echo '[]';
      die;
    }
    if ( false === ( $coins_data = get_transient( 'cp_ticker_coins_data' ) ) ) {
      $coins_data = CPCommon::cp_fetch_ticker(array('limit' => 500));
      set_transient( 'cp_ticker_coins_data', $coins_data, $transient_ttl );
    }
    header('Content-Type: application/json');
    echo json_encode($coins_data);
    die;
  }
}

new CPAjax();