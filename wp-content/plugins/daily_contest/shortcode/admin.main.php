<div class='wrap'>
<div id='dc-plugin-logo'>Daily Contest Plugin</div>

<h4>These are all the entries from the past 24 hours</h4>
<div id='dc-entry-container'>
	<table class='dc-entries-table'>
		<th>User Id</th>
		<th>Email</th>
		<th>Street Address</th>
		<th>City</th>
		<th>State</th>
		<th>Zipcode</th>
		<th>Timestamp</th>
	<?php foreach ($entries as $entry):?>
	<?php
		$user_data = get_userdata($entry['user_id']);
		$email = $user_data->user_email;
		$street_address = get_cimyFieldValue($entry['user_id'], 'street_address');
		$city = get_cimyFieldValue($entry['user_id'], 'city');
		$state = get_cimyFieldValue($entry['user_id'], 'state');
		$zipcode = get_cimyFieldValue($entry['user_id'], 'zipcode');
	?>
		<tr>
			<td><?php echo $entry['user_id'];?></td>
			<td><?php echo $email;?></td>
			<td><?php echo $street_address;?></td>
			<td><?php echo $city;?></td>
			<td><?php echo $state;?></td>
			<td><?php echo $zipcode;?></td>
			<td><?php echo $entry['created_at'];?></td>
		</tr>		
	<?php endforeach;?>
	</table>
</div>


<button id='dc-pick-a-winner'><strong>Pick a winner!</strong></button>
</div>


