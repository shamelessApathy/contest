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
			location.reload();
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
			location.reload();	
		}
	})
}


// This delete's an entry from the  WINNERS table ( in case of duplicate or whatever)
var delete_entry = function(e)
{
	var target = e.target;
	var entry_id = target.getAttribute('data-winner-id');
	var timestamp = target.getAttribute('data-timestamp');
	entry_id = {'entry_id': entry_id, 'timestamp': timestamp};
	var url = '/wp-content/plugins/daily_contest/delete.php'
	jQuery.ajax({
		type:"POST",
		url: url,
		data: entry_id,
		success: function(results)
		{
			console.log(results);
		}
	})
}

// This marks an item as shipped

var mark_as_shipped = function(e)
{
	var target = e.target;
	var user_id = target.getAttribute('data-id');
	var data = {'user_id' : user_id};
	var url = "/wp-content/plugins/daily_contest/mark_as_shipped.php";
	jQuery.ajax({
		method:"POST",
		url:url,
		data: data,
		success: function(results)
		{
			console.log(results);
			//location.reload();
		}
	})
}


var button = jQuery('#dc-pick-a-winner');
jQuery(button).on('click', pick_a_winner);

var specialButton = jQuery('#dc-special-draw-button');
jQuery(specialButton).on('click', special_drawing);



var deleteButtons = document.getElementsByClassName('dc-delete');
console.log(deleteButtons);

for  (var i = 0; i < deleteButtons.length; i++)
{
	deleteButtons[i].addEventListener('click',  delete_entry);
}



var shipButtons = document.getElementsByClassName('mark-as-shipped');
for (var i = 0; i < shipButtons.length; i++)
{
	shipButtons[i].addEventListener('click', mark_as_shipped);
}
});


