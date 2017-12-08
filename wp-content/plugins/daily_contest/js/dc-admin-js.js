jQuery(function(){


var pick_a_winner = function()
{
	var file_url = '/wp-content/plugins/daily_contest/pick_a_winner.php';
	jQuery.ajax({
		type:"POST",
		url: file_url,
		success: function(results)
		{
			console.log(results);
		}
	})
}

var button = jQuery('#dc-pick-a-winner');
jQuery(button).on('click', pick_a_winner);

});