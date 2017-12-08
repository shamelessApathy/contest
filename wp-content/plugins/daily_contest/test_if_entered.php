<?php
if (!isset($user_id))
{
	$user_id = $_POST['user_id'];
}

// Define time parameters

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

			
			


?>
