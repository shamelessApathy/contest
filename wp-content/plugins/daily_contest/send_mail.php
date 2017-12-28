<?php


$require = $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
require($require);

$email = $_POST['email'];
$prize = $_POST['prize'];




$to = $email;
$subject = "You won the GirlsGoneHigh prize-a-day giveaway!";
$message = "This message has been sent to you on behalf of girlsgonehigh.com, your entry into the prize-a-day giveaway has won!!!! Your prize should be shipping out anyday now! You won: $prize";
$headers = '';
$attachments = array();
$happen = wp_mail($to,$subject,$message,$headers,$attachments);


if ($happen)
{
	echo "its mailed!";
}
// wp_mail( string|array $to, string $subject, string $message, string|array $headers = '', string|array $attachments = array() )
?>