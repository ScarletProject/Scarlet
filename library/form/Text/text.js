/*
	This is not a sustainable solution, but it definitely does work for now...
	
	-- Allows for AJAX calls to have same JS functionality as loaded elements,
	without reducing modularity
*/

$('input','.form.text').live('focus', function() {
	if(!$(this).data('name'))
		$(this).data('name', $(this).val());
		
	if($(this).val() == $(this).data('name')) {
		$(this).val('');
		$(this).css('color','#484848');
	}
});

$('input','.form.text').live('blur', function() {
	if(!$(this).data('name'))
		$(this).data('name', $(this).val());
	
	if($(this).val() == '') {
		$(this).val($(this).data('name'));
		$(this).css('color','#cccccc');
	}
});