
<?php 
$logged_in = is_user_logged_in();
$user_id = get_current_user_id();


?>
<div id='contest-form-holder'>
<?php if ($logged_in):?>
<h3 style='text-align:center; color:#ccc;'>Bong-a-Day Giveaway!</h3>
<?php if($test):?>
	<?php echo "<h3>Thanks for playing come back tomorrow!</h3>";?>
<?php else:?>
<div id='dc-logo-container'>
	<img src="http://girlsgonehigh.com/wp-content/uploads/2017/12/ggh_logo_sm.jpg"/>
	<div id='dc-daily-contest-button-holder'>
		<button id='dc-daily-contest-button' data-user-id="<?php echo $user_id;?>">Click to Enter!!</button>

</div>
</div>

<?php endif;?>
<?php else:?>
<div id='dc-logo-container'>
	<img src="http://girlsgonehigh.com/wp-content/uploads/2017/12/ggh_logo_sm.jpg"/>
	<div id='dc-daily-contest-button-holder'>
<button id='dc-login-button'>Login to Enter Contest</button>
</div>
</div>

<?php endif;?>
</div>


