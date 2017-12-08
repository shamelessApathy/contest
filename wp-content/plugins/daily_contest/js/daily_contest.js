
jQuery(function(){


function DailyContest()
{
	this.form = document.getElementById('contest-form');
	this.dc_daily_contest_button = document.getElementById('dc-daily-contest-button');
	this.login_button = document.getElementById('dc-login-button');
	this.login_form = document.getElementById('dc-login-form');
	state = this; 
}



/**
* DailyContest.prototype.showLogin
*  Shows login form after login button is clicked on front page
*
*
*/
DailyContest.prototype.showLogin = function()
{
	console.log('inside showLogin()');
	jQuery(state.login_form).css({"display":'block'});
}


/**
* DailyContest.prototype.validateForm
*	Makes sure all form fields are filled
* @param form - the form you want to check
* @return true/false
*/

DailyContest.prototype.validateForm = function() 
{
  var isValid = true;
  jQuery('.form-field').each(function() {
    if ( jQuery(this).val() === '' )
        isValid = false;
  });
  return isValid;
}

/**
* Adds a listener
*
*
*/

DailyContest.prototype.addListener = function(element, action, func)
{
	jQuery(element).on(action, func);
}

/**
*
* DailyContest.prototype.addDailyEntry
* Takes user_id and submits it to the daily_contest table
*
*/

DailyContest.prototype.addDailyEntry = function()
{
	var user_id = jQuery(state.dc_daily_contest_button).attr('data-user-id');
	console.log(user_id);
	url = "/wp-content/plugins/daily_contest/contest_receive.php";
	jQuery.ajax({
		type:"POST",
		url: url,
		data: {'user_id':user_id},
		success: function(data)
		{
			console.log(data);
		}
	})
}

/**
* DailyContest.prototype.submitForm 
* begins the form submission process
*
*
*/
DailyContest.prototype.submitForm = function()
{
	console.log('inside submit form button');
	var form_valid = state.validateForm();
	if (form_valid)
	{
		var serial = jQuery(state.form).serialize();
		var url = "wp-content/plugins/daily_contest/contest_receive.php"; // the script where you handle the form input.

	    jQuery.ajax({
	           type: "POST",
	           url: url,
	           data: serial, // serializes the form's elements.
	           success: function(data)
	           {
	               console.log(data); // show response from the php script.
	           }
	         });
	}
	jQuery('#contest-form-holder').html('<h3>Thank you for entering today!</h3>');
}


var dc = new DailyContest();
var button = document.getElementById('contest-submit');
dc.addListener(button, 'click', dc.submitForm);

// Add listener for login button and function
var loginButton = document.getElementById('dc-login-button');
dc.addListener(loginButton,'click', dc.showLogin);

var enterButton = document.getElementById('dc-daily-contest-button');
dc.addListener(enterButton, 'click', dc.addDailyEntry);

});


