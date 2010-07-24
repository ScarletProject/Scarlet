$(document).ready(function() {
	$('#console').find('textarea').focus();
	var h = true;
	$('#console').find('textarea').keyup(function(e) {
		$.ajax({
			url: '?action=render',
			type: 'POST',
			data: {text:$(this).val()},

			success: function(response) {
				if(response != '') {
					response = $.parseJSON(response);
								
					if(h) {
						$('#preview').html(response.content);
					}
					else {
						$('#preview').text(response.content);
					}
					
					var assets = response.assets.split(' ');
				
					var html = [];
					for(var i = 0; i < assets.length; i++) {
						html[i] = '<li>'+assets[i]+'</li>';
					}
				
					html = html.join('');
				
					$('#assets').find('ul').html(html);
				}
			},

			error: function() {
				//called when there is an error
			}
		});
	});	
});
