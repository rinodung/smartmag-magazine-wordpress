jQuery(function($) {
	
	$('.widget-liquid-right').on('click', '#add-more-tabs', function(e) {

		// current tabs count
		var tabs_count = $(this).parent().data('bunyad_tabs') || 0;
		if (tabs_count === 0) {
			tabs_count = $(this).parent().parent().find('.title').length;
		}

		if (tabs_count >= 6) { 
			alert('Woah, slow down. Do you really wish to be adding that many tabs?');
		}
		
		tabs_count++;

		// get our template and modify it
		var html = $(this).parent().parent().find('.template-tab-options').html();
		
		if (typeof e.data == 'object') {
			// template replace
			//for (i in e.data) {
			//	html.replace('%' + i + '%', e.data[i]);
			//}
		}
		

		html = $(html);
		html.find('label span').html(tabs_count);
		
		$(this).parent().before(html);

		// update counter
		$(this).parent().data('bunyad_tabs', tabs_count);
		
		e.stopPropagation();
		return false;
	});
});
