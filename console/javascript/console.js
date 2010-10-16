function array_diff(a1, a2)
{
  var a=[], diff=[];
  for(var i=0;i<a1.length;i++)
    a[a1[i]]=true;
  for(var i=0;i<a2.length;i++)
    if(a[a2[i]]) delete a[a2[i]];
    else a[a2[i]]=true;
  for(var k in a)
    diff.push(k);
  return diff;
}
var h = true;
$(document).ready(function() {

	
	$('#console').find('textarea').focus();
	
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
						
						var css = {};

						$('link').each(function(index) {
							var href = $(this).attr('href');
							css[href] = true;
						});

						$.each(response.css, function(i, sheet) {
							if(!css[sheet]) {	
								var link = $("<link>");	
								link.attr({
									type: 'text/css',
									rel: 'stylesheet',
					     			href: sheet
								});

								$("head").append(link);
							}
						});

						var js = {};

						$('script').each(function(i) {
							var src = $(this).attr('src');
							js[src] = true;
						});

						$.each(response.js, function(i, sheet) {
							if(!js[sheet]) {	
								var script = document.createElement('script');
								script.src = sheet;
								script.type = 'text/javascript';
								document.body.parentNode.appendChild(script);
							}
						});
					}
					else {
						var content = response.content.replace(/<br \/>/g, "\n");
						$('#preview').text(content).wrap('<pre>');						
					}					
				}
			}
		});
	});	
});
