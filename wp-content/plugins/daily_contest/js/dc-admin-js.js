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


var special_drawing = function()
{
	var file_url = '/wp-content/plugins/daily_contest/special_drawing.php';
	jQuery.ajax({
		type:"POST",
		url: file_url,
		success: function(results)
		{
			alert("User ID: " + results + " has won today!");
		}
	})
}
var button = jQuery('#dc-pick-a-winner');
jQuery(button).on('click', pick_a_winner);

var specialButton = jQuery('#dc-special-draw-button');
jQuery(specialButton).on('click', special_drawing);


});