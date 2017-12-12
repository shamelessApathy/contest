<?php



require_once('creds/dc_constants.php');
$entry_id = $_POST['entry_id'];
$timestamp = $_POST['timestamp'];



$table = "wp_daily_contest_winners";


$tableName = 'wp_daily_contest_winners';


$servername = "localhost";
$username = DC_USER;
$password = DC_PASS;
$dbname = 'contest';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// sql to delete a record
$sql = "DELETE FROM wp_daily_contest_winners WHERE user_id=$entry_id AND created_at = $timestamp";

if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();


?>  