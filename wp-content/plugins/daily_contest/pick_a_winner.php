<?php


// Set seconds in a day
$day = 84600;
$time = time();
$beg = $time - $day;
$end = $time;


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
			$sql = "SELECT * FROM daily_contest
					WHERE `created_at` <= $beginning <= $end
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

print_r($winner);

?> 