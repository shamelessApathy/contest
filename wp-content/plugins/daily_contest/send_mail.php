<?php


$require = $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
require($require);




$to = "bcm811@gmail.com";
$subject = "Test Email from wordpress plugin";
$message = "This is a test message from a wordpress contest plugin";
$headers = '';
$attachments = array();
$happen = wp_mail($to,$subject,$message,$headers,$attachments);


var_dump($happen);
// wp_mail( string|array $to, string $subject, string $message, string|array $headers = '', string|array $attachments = array() )
?>