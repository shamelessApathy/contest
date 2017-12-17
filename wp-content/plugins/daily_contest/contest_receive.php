<?php

$user_id =  $_POST['user_id'];
$created_at = time();

require_once('creds/dc_constants.php');

function PDO_injection($user_id, $created_at)
{
	$servername = DC_SERVER;
	$username = DC_USER;
	$password = DC_PASS;
	$dbname = DC_DB_NAME;
	$time = time();
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO wp_daily_contest (`user_id`, `created_at`)
				VALUES (:user_id, :created_at)
					";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
		$stmt->bindParam(":created_at", $created_at, PDO::PARAM_INT);
		if ($stmt->execute())
		{
			echo "true";
		}

		}
	catch(PDOException $e)
	    {
	    echo $sql . "<br>" . $e->getMessage();
	    }
	    $conn = null;
}

$test_if_entered = require_once('test_if_entered.php');

function sendMail() 
{
	$require = $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
	require($require);
	$user_data = get_userdata($user_id);
	$email = $user_data->user_email;




	$to = $email;
	$subject = "Your bong-a-day giveaway entry has been received";
	$message = "Make sure you log back on tomorrow at http://girlsgonehigh.com and click the button again to enter!!	";
	$headers = '';
	$attachments = array();
	$happen = wp_mail($to,$subject,$message,$headers,$attachments);
}

if (!$test_if_entered)
{
	PDO_injection($user_id, $created_at);
	sendMail();
}
else
{
	"echo already voted";
}





?>