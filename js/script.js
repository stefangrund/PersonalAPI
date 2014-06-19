/*
 *	Personal API JavaScript/jQuery functions
 *	Used for animations and user input checks
 */

// Displays/hides a module on the module admin page
function display(module) {

	var config = '.'+module+' .config';

	if ($(config).is(':visible')) {
		$(config).slideUp(600);
	}
	else {
		$(config).slideDown(600);
	}
	
}

// Fade out tip box for saved changes/wrong password
$(window).load(function(){

	setTimeout(function(){ 
		$('#tip').fadeOut()
	}, 3000);

});
