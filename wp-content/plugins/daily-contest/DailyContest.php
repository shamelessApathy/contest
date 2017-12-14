<?php defined('ABSPATH') or exit;

/**
 * Plugin Name: Daily Contest
 * Plugin URI: https://stevie.io
 * Description: Allows you to run a daily contest.
 * Version: 1.0.0
 * Author: Stevie McComb
 * Author URI: https://stevie.io
 * Text Domain: daily-contest
 */

class DailyContest
{
    private $entries_table;

    private $tokens_table;

    public function __construct()
    {
        global $wpdb;

        $this->entries_table = $wpdb->prefix . 'daily_contest_entries';
        $this->winners_table = $wpdb->prefix . 'daily_contest_winners';

        register_activation_hook(__FILE__, array($this, 'migrate'));

        add_shortcode('daily-contest', array($this, 'shortcode_callback'));
        // Adding menu page
        function admin_page()
        {
           require_once('shortcode/admin.main.php');
        }
        add_action('admin_menu', 'daily_contest_menu');
 
        function daily_contest_menu()
        {
            add_menu_page( 'Daily Contest Plugin Page', 'Daily-Contest Plugin', 'manage_options', 'daily-contest', 'admin_page' );
        }
        add_action('wp_footer', array($this, 'enqueueAssets'));

        function my_theme_send_email() 
        {

            if ( isset( $_POST['make-entry'] ))
            {

               file_put_contents('test.txt', 'it worked \n', FILE_APPEND);

            } 

        } 
            add_action( 'init', 'my_theme_send_email' );
    }
    public function enqueueAssets()
    {
        wp_register_script( 'daily-contest', plugins_url( '/js/daily-contest.js' , __FILE__ ), array(), '1.0.0', true );
        wp_enqueue_script('daily-contest');
    }
    // function to get the enries for the admin menu page
    public function get_admin_entries()
    {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM $this->entries_table", OBJECT );
        return $results;
    }

    public function migrate()
    {
        ob_start();

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $entries_sql = "
            CREATE TABLE IF NOT EXISTS `{$this->entries_table}` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` varchar(255) NOT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (`id`)
            ) $charset_collate;
        ";

        $winners_sql = "
            CREATE TABLE IF NOT EXISTS `{$this->winners_table}` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `user_id` int(10) UNSIGNED NOT NULL UNIQUE,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (`id`)
            ) $charset_collate;
        "; 

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($entries_sql);
        dbDelta($winners_sql);

        $output = ob_get_contents();

        ob_end_clean();

        if (!empty($output)) file_put_contents(plugin_dir_path(__FILE__) . '/activation-errors.txt', $output);
    }


    public function shortcode_callback()
    {

        ob_start();

        require_once plugin_dir_path(__FILE__) . '/form.php';


        $html = ob_get_contents();

        ob_end_clean();

        return $html;
    }

    private function submit_form()
    {
        global $wpdb;
        $user_id = get_current_user_id();

        $entry_data = array(
            'user_id' => $user_id
        );
        $wpdb->insert($this->entries_table, $entry_data);
    }

    private function verify_email()
    {
        global $wpdb;

        $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
        $token = filter_var($_GET['token'], FILTER_SANITIZE_STRING);

        if (empty($email)) die('The provided email is invalid.');

        $token_record = $wpdb->get_row("SELECT * FROM `{$this->tokens_table}` WHERE `email` = '$email' ORDER BY `created_at` DESC LIMIT 1;");

        if ($token_record->token !== $token) die('The provided token is invalid.');
        if (date('Y-m-d H:i:s', strtotime($token_record->expires_at)) <= date('Y-m-d H:i:s', strtotime('now'))) die('The provided token has expired.');

        $entry_verified = $wpdb->update($this->entries_table, array('verified' => 1), array('id' => $token_record->entry_id));

        $entry_verified ? die('Your entry has been verified. You have now officially been entered into the contest!') : die('There was a problem verifying your entry, please contact the site\'s administrator to resolve the issue.');
    }

    private function generate_token()
    {
        $token = '';
        $chars = str_split('abcdefghijklmnopqrstuvwxyz');

        while (strlen($token) < 16) {
            $token .= $chars[rand(0, count($chars) - 1)];
        }

        return $token;
    }
}

new DailyContest();
