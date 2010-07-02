$(document).ready(function() {
	$('input','.search.live').each(function() {
		var box = $(this).parent().parent();
		var id = $(box).attr('id');

		var action = tp[id].action;
		var controller = tp[id].controller;

		function up() {
		
		}	
	
		var query = '';
		var key = '';
		$(this).keyup(function(e) {
			key = e.keyCode;
			query = $(this).val();
			if(!query) return;
			console.log(controller+'?action='+action);
			$.ajax({
				url: controller+'?action='+action,
				type: 'POST',
				data: {query : query},
			
				success: function(response) {
					$('.results',box).html(response);
				},
			
				error: function(error) {
					alert(error);
				}
			
			});
		});
	
	
		// No longer works.. oh well. 
		$(this).blur(function() {
			if($(this).val() == '')
				$('table', box).fadeOut('fast');
		});
	
	});
});
