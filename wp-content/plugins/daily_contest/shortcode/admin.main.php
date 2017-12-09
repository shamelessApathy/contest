<div class='wrap'>
<div id='dc-plugin-logo'>Daily Contest Plugin</div>

<h4>These are all the entries from the past 24 hours</h4>
<div id='dc-entry-container'>
	<?php if (isset($entries) && $entries != null):?>
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
	<button id='dc-pick-a-winner'><strong>Pick a winner!</strong></button>
<?php else:?>
	<h3>Currently there are no contest entrants in the past 24 hours!</h3>
	<h4>Use special draw from entire pool button?</h4>
	<button type='button' id='dc-special-draw-button'>Special Drawing</button>
<?php endif;?>
</div>



<div id='dc-winners-table'>
	<h2 style='text-align:center;'>Winners Table</h2>
	<table class='dc-winners-table'>
		<th>User Id</th>
		<th>Email</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Street Address</th>
		<th>City</th>
		<th>State</th>
		<th>Zipcode</th>
		<th>Timestamp</th>
		<th>Shipped?</th>
		<?php foreach ($winners as $winner):?>
		<?php
		$user_data = get_userdata($winner['user_id']);
		$email = $user_data->user_email;
		$first_name = get_cimyFieldValue($winner['user_id'], 'first_name');
		$last_name = get_cimyFieldValue($winner['user_id'], 'last_name');
		$street_address = get_cimyFieldValue($winner['user_id'], 'street_address');
		$city = get_cimyFieldValue($winner['user_id'], 'city');
		$state = get_cimyFieldValue($winner['user_id'], 'state');
		$zipcode = get_cimyFieldValue($winner['user_id'], 'zipcode');
		$timestamp = $winner['created_at'];
		$date = date("F j, Y, g:i a", $timestamp);
		$shipped = $winner['shipped'];
		?>
		<tr>
			<td><?php echo $winner['user_id'];?></td>
			<td><?php echo $email;?></td>
			<td><?php echo $first_name;?></td>
			<td><?php echo $last_name;?></td>
			<td><?php echo $street_address;?></td>
			<td><?php echo $city;?></td>
			<td><?php echo $state;?></td>
			<td><?php echo $zipcode;?></td>
			<td><?php echo $date;?></td>
			<td>
				<?php 
				if ($shipped === 1) 
				{
					echo "Shipped";
				}
				else
				{
					echo "<button type=button>Mark as Shipped</button>";
				}
				?>
			</td>
		</tr>
		<?php endforeach;?>
	</table>
</div>


</div>


