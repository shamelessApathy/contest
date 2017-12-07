
jQuery(function(){


function DailyContest()
{
	this.form = document.getElementById('contest-form');
	this.button_submit = document.getElementById('contest-submit');
	state = this; 
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
*
*
*
*/

DailyContest.prototype.addListener = function(element, action, func)
{
	jQuery(element).on(action, func);
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


});


