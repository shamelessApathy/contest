
<?php
   /*
   Plugin Name: Daily Contest
   Plugin URI: none
   Description: a plugin to do daily contest entry
   Version: 1.0
   Author: Brian Moniz
   Author URI: http://slcutahdesign.com
   License: GPL2
   */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Daily_Contest
{

	private $entries_table;
 
    private $tokens_table;

    public function __construct()
    {
        global $wpdb;

        $this->entries_table = $wpdb->prefix . 'daily_contest_entries';
        $this->winners_table = $wpdb->prefix . 'daily_contest_winners';
        // This will create wp_daily_contest_entries table
		//register_activation_hook( __FILE__, array($this,'my_plugin_create_db') );
		// This will create thw inners table
		//register_activation_hook(__FILE__, array($this, 'my_plugin_create_db2') );
        register_activation_hook(__FILE__, array($this, 'migrate'));

        add_shortcode('daily-contest', array($this, 'shortcode_callback'));

        $this->init_api();

        //  add_action stays inside the __construct function, but in order to get access to the outside functions, "$this" must be passed as 
        // The first argument in an array for when you enqueue scripts
		add_action( 'admin_enqueue_scripts', array($this,'wpdocs_enqueue_custom_admin_style'));
		add_action( 'wp_enqueue_scripts', array($this,'register_my_script'));
		add_action('admin_menu', array($this,'daily_contest_menu'));
		// Add admin page
		$this->daily_contest_menu();

    }
    private function init_api()
    {
        if (empty($_REQUEST['context']) || $_REQUEST['context'] !== 'daily-contest' || empty($_REQUEST['action'])) return false;

        switch ($_REQUEST['action']) {
            case 'submit-form':
                $this->submit_form();
                break;

            case 'verify-email':
                $this->verify_email();
                break;

            default:
                break;
        }

        exit;
    }

    /**
    * Register the scripts and styles for the ADMIN area of the plugin
    *
    *
    */

    public function wpdocs_enqueue_custom_admin_style() 
    {
	    wp_register_style( 'dc-admin-styles', plugins_url('/daily_contest/css/dc-admin-styles.css')); 
	    wp_enqueue_style( 'dc-admin-styles' );
	    wp_register_script('dc-admin-js', plugins_url('/daily_contest/js/dc-admin-js.js'));
	    wp_enqueue_script('dc-admin-js');
	}

	/**
	* Register the scripts and styles for the FRONTEND of the plugin
	* 
	*
	*/
	public function register_my_script() 
	{
		wp_register_script( 'daily-contest', plugins_url( '/js/daily_contest.js' , __FILE__ ), array(), '1.0.0', true );
		wp_register_style('daily-contest-styles', "/wp-content/plugins/daily_contest/css/daily-contest-styles.css");
	}

	/**
	* This function is the shortcode callback function for displaying the contest  on the front-end wherever the shortcode is
	*
	*
	*
	*/
	public function shortcode_callback()
	{
		$user_id = get_current_user_id();
		$test = require_once('/var/www/contest/wp-content/plugins/daily_contest/test_if_entered.php');
		require_once('shortcode/front.contest_main.php');
		wp_enqueue_script( 'daily-contest' );
		wp_enqueue_style('daily-contest-styles');
		
		ob_start();

        require_once(plugin_dir_path(__FILE__) . 'shortcode/front.contest_main.php');

        $html = ob_get_contents();

        ob_end_clean();

        return $html;
	}


	/**
	* Checks to see if the user_id is already in the winners table
	* @return BOOLEN true/false
	*	
	*
	*/
	public function test_if_winner()
	{
		global $wpdb;
		$servername = "localhost";
		$username = 'root';
		$password = 'Poke8112';
		$dbname = 'contest';
		$time = time();
		$day = 86400;
		$beginning = $time - $day;
		$end = $time;
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT `user_id` FROM wp_daily_contest
					WHERE `created_at` >= $beginning AND `created_at` <= $end
					";
			$stmt = $conn->prepare($sql);
			$result = $conn->query($sql);
			$stuff = $result->fetchAll();
			// Set Global
			$_SESSION['dc-entries'] = $stuff;


			$result = false;
			foreach ($stuff as $entry)
			{
				if ($entry['user_id'] === $user_id)
				{
					$result = true;
				}
			}
			// for ajax access
			echo "$result";

			$_SESSION['voted'] = $result;
			return $result;
	}

	// need adjustment
	public function getWinners()
	{
		$winners = require_once('get_winners.php');
		return $winners;
	}

	public function admin_page()
	{
		$entries = $this->getEntries();
		$winners = $this->getWinners();
		$path = ABSPATH . 'wp-content/plugins/daily_contest/css/dc-admin-styles.css';

		require_once('shortcode/admin.main.php');
	}

	 
	public function daily_contest_menu(){
	        add_menu_page( 'Daily Contest Plugin Page', 'Daily-Contest Plugin', 'manage_options', 'daily-contest', array($this,'admin_page') );
	}

	// Function  to get all DB entries in daily_contest

	public function getEntries()
	{
		$entries = require_once('get_entries.php');
		return $entries;	
	}




	public function my_plugin_create_db() 
	{

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'daily_contest';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			created_at int NOT NULL,
			user_id smallint(5) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public function my_plugin_create_db2() 
	{

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'daily_contest_winners';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			created_at int NOT NULL,
			user_id smallint(5) NOT NULL,
			shipped smallint(1) NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}


    public function migrate()
    {
        ob_start();

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $entries_sql = "
            CREATE TABLE IF NOT EXISTS `{$this->entries_table}` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` int(10) NOT NULL,
                `email` varchar(255) NOT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
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


}
?>