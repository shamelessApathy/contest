
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

<?php 

endif;?>


</div>
<!-- Potential share site stuff, not working right now just going to do the follow me links -->
<!--
<div class='dc-share-toolbar'>
<div class='dc-share-icon dc-tool-left'>
<a href="https://www.facebook.com/sharer/sharer.php?u=girlsgonehigh.com"><img alt='FB LOGO' src="/wp-content/uploads/2017/12/share_fb_logo.png"/></a>
<p style='color:#a4d06f;'>Share on Facebook</p>
</div>
<div class='dc-share-icon dc-tool-right	'>
<a href="https://twitter.com/home?status=http%3A//girlsgonehigh.com"><img alt='TWITTER LOGO' src="/wp-content/uploads/2017/12/share_twitter_logo.png"/></a>
<p style='color:#a4d06f;'>Share on Twitter</p>
</div>
</div>-->	
</div>
<h4 style='color:#a4d06f; text-align:center;'>Follow us on social media!</h4>



