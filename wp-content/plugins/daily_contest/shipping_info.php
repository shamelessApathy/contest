<?php

echo "at the shipping info page";

$user_id = $_GET['id'];

		$user_data = get_userdata($entry['user_id']);
		print_r($user_data);
		$email = $user_data->user_email;
		$street_address = get_cimyFieldValue($user_id, 'street_address');
		$city = get_cimyFieldValue($user_id, 'city');
		$state = get_cimyFieldValue($user_id, 'state');
		$zipcode = get_cimyFieldValue($user_id, 'zipcode');

?>



