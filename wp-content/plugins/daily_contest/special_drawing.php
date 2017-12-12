<?php

		$servername = "localhost";
		$username = 'root';
		$password = 'Poke8112';
		$dbname = 'contest';

			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM wp_daily_contest";
			$stmt = $conn->prepare($sql);
			$result = $conn->query($sql);
			$stuff = $result->fetchAll();

// How many entries are in the array that was returned??
$count = count($stuff);
// adjust for array indices starting at 0
$count = $count -1;

$random = rand(1,$count);

$winner = $stuff[$random];
$time = time();


// From here inserting winner into winner's table
$winner_user_id = $winner['user_id'];
$servername = "localhost";
$username = "root";
$password = "Poke8112";
$dbname = "contest";

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





?>