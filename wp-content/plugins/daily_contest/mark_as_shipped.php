<?php
$created_at = time();
$user_id = $_POST['user_id'];
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
		$sql = "UPDATE wp_daily_contest_winners
				SET shipped = 1
				WHERE `user_id` = :user_id
					";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
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


PDO_injection($user_id, $created_at);




?>