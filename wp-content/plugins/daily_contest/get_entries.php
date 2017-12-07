<?php


		$servername = "localhost";
		$username = 'root';
		$password = 'Poke8112';
		$dbname = 'contest';
		$time = time();
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT * FROM daily_contest";
			$stmt = $conn->prepare($sql);
			$result = $conn->query($sql);
			$stuff = $result->fetchAll();
			return $stuff;


?>
