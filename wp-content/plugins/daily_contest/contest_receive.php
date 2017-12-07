<?php
echo "you made it to receiving";

$email =  $_POST['email'] ?? null;
$first_name = $_POST['first-name'] ?? null;
$last_name = $_POST['last-name'] ?? null;
$street_address = $_POST['street-address'] ?? null;
$city = $_POST['city'];
$state = $_POST['state'];
$zipcode = $_POST['zipcode'];


echo "Email: ". $email . "\n";
echo "First Name: ". $first_name . "\n";
echo "Last Name: ". $last_name . "\n";
echo "Street Address: ". $street_address . "\n";
echo "City: ". $city . "\n";
echo "State: ". $state . "\n";
echo "Zipcode: ". $zipcode . "\n";


function PDO_injection($email,$first_name,$last_name,$street_address,$city,$state,$zipcode)
{
	$servername = "localhost";
	$username = 'root';
	$password = 'Poke8112';
	$dbname = 'contest';
	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO daily_contest (`email`, `first_name`, `last_name`, `street_address`, `city`, `state`, `zipcode`)
				VALUES (:email, :first_name, :last_name, :street_address, :city, :state, :zipcode)
					";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(":email", $email, PDO::PARAM_STR);
		$stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
		$stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
		$stmt->bindParam(":street_address", $street_address, PDO::PARAM_STR);
		$stmt->bindParam(":city", $city, PDO::PARAM_STR);
		$stmt->bindParam(":state", $state, PDO::PARAM_STR);
		$stmt->bindParam(":zipcode", $zipcode, PDO::PARAM_STR);
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
PDO_injection($email,$first_name,$last_name,$street_address,$city,$state,$zipcode);


?>