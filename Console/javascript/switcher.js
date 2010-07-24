$(document).ready( function(){ 
    $(".cb-enable").click(function(){
        var parent = $(this).parents('.switch');
        $('.cb-disable',parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox',parent).attr('checked', true);
		if(!h) {
			h = true;
			$('#preview').html($('#preview').text());
		}
    });
    $(".cb-disable").click(function(){
        var parent = $(this).parents('.switch');
        $('.cb-enable',parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox',parent).attr('checked', false);
		if(h) {
			h = false;
			$('#preview').text($('#preview').html()).wrap('<pre>');
		}
    });
});