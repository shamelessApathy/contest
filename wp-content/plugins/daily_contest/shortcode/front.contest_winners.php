<div class='wrap'>
<table id='dc-frontend-winners'>
	<th>User ID</th>
	<th>Screen Name</th>
	<th> Date Won </th>
<?php foreach ($winners as $winner):?>
<?php
	$data = get_userdata($winner['user_id']);
	$created_at = $winner['created_at'];	
	$date = date("m.d.y", $created_at);
?>
<tr>
<td><?php echo $winner['user_id'];?></td>
<td><?php echo $data->user_nicename;?></td>
<td><?php echo $date;?></td>
</tr>
<?php endforeach; ?>
</table>
</div>