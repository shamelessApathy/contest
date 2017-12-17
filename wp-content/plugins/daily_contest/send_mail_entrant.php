<?php


$require = $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
require($require);

$email = $_POST['email'];




$to = $email;
$subject = "Your bong-a-day giveaway entry has been received";
$message = "Make sure you log back on tomorrow at http://girlsgonehigh.com and click the button again to enter!!	";
$headers = '';
$attachments = array();
$happen = wp_mail($to,$subject,$message,$headers,$attachments);