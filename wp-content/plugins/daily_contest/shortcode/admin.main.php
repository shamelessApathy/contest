<div class='wrap'>
<div id='dc-plugin-logo'>Daily Contest Plugin</div>

<h4>These are all the entries from the past 24 hours</h4>
<div id='dc-entry-container'>
	<table class='dc-entries-table'>
		<th>Email</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Street Address</th>
		<th>City</th>
		<th>State</th>
		<th>ZipCode</th>
		<th>Timestamp</th>
	<?php foreach ($entries as $entry):?>
		<tr>
			<td><?php echo $entry['email'];?></td>
			<td><?php echo $entry['first_name'];?></td>
			<td><?php echo $entry['last_name'];?></td>
			<td><?php echo $entry['street_address'];?></td>
			<td><?php echo $entry['city'];?></td>
			<td><?php echo $entry['state'];?></td>
			<td><?php echo $entry['zipcode'];?></td>
			<td><?php echo $entry['created_at'];?></td>
		</tr>		
	<?php endforeach;?>
	</table>
</div>


<button id='dc-pick-a-winner'><strong>Pick a winner!</strong></button>
</div>