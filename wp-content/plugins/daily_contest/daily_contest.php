
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



// Enqueue the styles for admin
/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function wpdocs_enqueue_custom_admin_style() {
        wp_register_style( 'dc-admin-styles', plugins_url('/daily_contest/css/dc-admin-styles.css')); 
        wp_enqueue_style( 'dc-admin-styles' );
        wp_register_script('dc-admin-js', plugins_url('/daily_contest/js/dc-admin-js.js'));
        wp_enqueue_script('dc-admin-js');
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );



// Enqeue the javascript
add_action( 'wp_enqueue_scripts', 'register_my_script' );

function register_my_script() 
{
wp_register_script( 'daily-contest', plugins_url( '/js/daily_contest.js' , __FILE__ ), array(), '1.0.0', true );
wp_register_style('daily-contest-styles', "/wp-content/plugins/daily_contest/css/daily-contest-styles.css");
}

$func = function() 
{
	$user_id = get_current_user_id();
	$test = require_once('/var/www/contest/wp-content/plugins/daily_contest/test_if_entered.php');
		require_once('shortcode/front.contest_main.php');
		wp_enqueue_script( 'daily-contest' );
		wp_enqueue_style('daily-contest-styles');
};
add_shortcode( 'display-contest',$func);
 




function getWinners()
{
	global $wpdb;
	/*$servername = $wpdb->dbhost;
	$username = $wpdb->dbuser;
	$password = $wpdb->dbpassword;
	$dbname = $wpdb->dbname;
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM wp_daily_contest_winners";
	$stmt = $conn->prepare($sql);
	$result = $conn->query($sql);
	$stuff = $result->fetchAll();
	// Set Global
	$_SESSION['dc-winners'] = $stuff;

	return $stuff;*/
	$winners = $wpdb->get_results('SELECT * FROM wp_daily_contest_winners',ARRAY_A);
	return $winners;
}





function pick_a_winner()
{
	global $wpdb;
	$day = 84600;
	$time = time();
	$beg = $time - $day;
	$end = $time;
	$servername = $wpdb->dbhost;
	$username = $wpdb->dbuser;
	$password = $wpdb->dbpassword;
	$dbname = $wpdb->dbname;
	$time = time();
	$day = 86400;
	$beginning = $time - $day;
	$end = $time;
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM wp_daily_contest
			WHERE `created_at` >= $beginning AND `created_at` <= $end
			";
	$stmt = $conn->prepare($sql);
	$result = $conn->query($sql);
	$stuff = $result->fetchAll();

	// How many entries are in the array that was returned??
	$count = count($stuff);

	// adjust for array indices starting at 0
	$count = $count -1;

	$random = rand(0,$count);

	$winner = $stuff[$random];

	$winner_id = $winner['user_id'];
	add_winner($winner_id);
}


function add_winner($user_id) 
{

	// From here inserting winner into winner's table
	$winner_user_id = $user_id;
	$time = time();
	$servername = $wpdb->dbhost;
	$username = $wpdb->dbuser;
	$password = $wpdb->dbpassword;
	$dbname = $wpdb->dbname;

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 
	$sql = "INSERT INTO wp_daily_contest_winners (user_id, created_at)
	VALUES ($winner_user_id, $time)";

	if ($conn->query($sql) === TRUE) {
		echo $winner_user_id;
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();
}




function get_all_entries()
{
	global $wpdb;
	$allEntries = $wpdb->get_Results('SELECT * FROM wp_daily_contest', ARRAY_A);
	return $allEntries;
}

// End admin area menu
// Add the admin panel page here
function admin_page()
{
	global $wpdb;
	$entries = getEntries();
	$winners = getWinners();
	$allEntries = get_all_entries();

	$path = ABSPATH . 'wp-content/plugins/daily_contest/css/dc-admin-styles.css';

	require_once('shortcode/admin.main.php');
}
add_action('admin_menu', 'daily_contest_menu');
 
function daily_contest_menu(){
        add_menu_page( 'Daily Contest Plugin Page', 'Daily-Contest Plugin', 'manage_options', 'daily-contest', 'admin_page' );
}

// Function  to get all DB entries in daily_contest

function getEntries()
{
	global $wpdb;
		$servername = $wpdb->dbhost;
		$username = $wpdb->dbuser;
		$password = $wpdb->dbpassword;
		$dbname = $wpdb->dbname;
		$time = time();
		$day = 86400;
		$beginning = $time - $day;
		$end = $time;
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM wp_daily_contest
					WHERE `created_at` >= $beginning AND `created_at` <= $end
					";
			$stmt = $conn->prepare($sql);
			$result = $conn->query($sql);
			$stuff = $result->fetchAll();
			// Set Global
			$_SESSION['dc-entries'] = $stuff;

			return $stuff;	
}



	// run install scripts when plugin is activated
	//register_activation_hook(__FILE__, 'createTables');
register_activation_hook( __FILE__, 'my_plugin_create_db' );

	function my_plugin_create_db() {

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
register_activation_hook(__FILE__, 'my_plugin_create_db2');
	function my_plugin_create_db2() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'daily_contest_winners';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created_at int NOT NULL,
		user_id smallint(5) NOT NULL UNIQUE,
		shipped smallint(1) NULL,
		email smallint(1) NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

/*add_action( 'phpmailer_init', 'configure_smtp' );
function configure_smtp( PHPMailer $phpmailer ){
    $phpmailer->isSMTP(); //switch to smtp
    $phpmailer->Host = 'mail.mydomain.com';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 25;
    $phpmailer->Username = 'Username Here';
    $phpmailer->Password = 'myemailpassword';
    $phpmailer->SMTPSecure = false;
    $phpmailer->From = 'From Email Here';
    $phpmailer->FromName='Sender Name';
}*/

?>