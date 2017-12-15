<?php


$require = $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
require($require);

$email = $_POST['email'];




$to = $email;
$subject = "You won the bong-a-day giveaway!";
$message = "This message has been sent to you on behalf of girlsgonehigh.com, your entry into the bong-a-day giveaway has won!!!! Your new bong should be shipping out anyday now!";
$headers = '';
$attachments = array();
$happen = wp_mail($to,$subject,$message,$headers,$attachments);


var_dump($happen);

var_dump($email);
// wp_mail( string|array $to, string $subject, string $message, string|array $headers = '', string|array $attachments = array() )
?>