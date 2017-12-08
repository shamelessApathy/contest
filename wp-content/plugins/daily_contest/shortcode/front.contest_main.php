
<?php 
$logged_in = is_user_logged_in();
$user_id = get_current_user_id();


?>
<div id='contest-form-holder'>
<?php if ($logged_in):?>
<h3 style='text-align:center;'>Daily Contest</h3>
<?php if($test):?>
	<?php echo "<h3>Thanks for playing come back tomorrow!</h3>";?>
<?php else:?>
<div id='dc-daily-contest-button-holder'>
<button id='dc-daily-contest-button' data-user-id="<?php echo $user_id;?>">Click to Enter!!</button>
</div>
<?php endif;?>
<?php else:?>
<div id='dc-daily-contest-button-holder'>
<button id='dc-login-button'>Login to Enter Contest</button>
</div>
<?php endif;?>
</div>


