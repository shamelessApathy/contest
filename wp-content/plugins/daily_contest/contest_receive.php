<?php
echo "you made it to receiving";

$user_id =  $_POST['user_id'];
$created_at = time();

function PDO_injection($user_id, $created_at)
{
	$servername = "localhost";
	$username = 'root';
	$password = 'Poke8112';
	$dbname = 'contest';
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
			echo "added an entry!!";
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