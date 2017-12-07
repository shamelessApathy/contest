<?php


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
			// Set Global
			$_SESSION['current_entries'] = $stuff;
			return $stuff;



?>
