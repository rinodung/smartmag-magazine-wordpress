jQuery(function($) {
	
	$('.widget-liquid-right').on('change', '[name*="[cats]["]', function() {
		if ($(this).val() == 'tag') {
			$(this).parent().find('.tax_tag').show();
		}
		else {
			$(this).parent().find('.tax_tag').hide();
		}
	});
	
	$('.widget-liquid-right').on('click', '.remove-recent-tab', function() {
		$(this).parent().parent().prev('.title').remove();
		$(this).parent().parent().remove();
	});
	
	$('.widget-liquid-right').on('click', '#add-more-tabs', function(e, data) {

		// current tabs count
		var tabs_count = $(this).parent().data('bunyad_tabs') || 0,
			 max_id =  $(this).parent().data('max_id') || 0;
		
		if (tabs_count === 0) {
			tabs_count = $(this).parent().parent().find('.title').length;
		}

		if (tabs_count >= 6) { 
			alert('Woah, slow down. Do you really wish to be adding that many tabs?');
		}
		
		tabs_count++;
		max_id++;

		// get our template and modify it
		var html = $(this).parent().parent().find('.template-tab-options').html();
		
		
		/**
		 * Editing? - load template values
		 */
		
		// defaults
		var selected;
		if (data !== Object(data)) {
			data = {title: '', selected: 'recent', tax_tag: '', n: max_id, posts: 4};
		}

		// template replace
		for (i in data) {
			html = html.replace(new RegExp('%' + i + '%', 'g'), data[i]);
		}
		
		// set max id if editing 
		if (parseInt(data['n']) > max_id) {
			max_id = parseInt(data['n']);
		}
			
		selected = data.selected;

		html = $(html);
		html.find('label span').html(tabs_count);
		
		// select the pre-provided option
		if (selected) {
			html.find('select').val(selected);
		}
		
		$(this).parent().before(html);
		$(this).parent().parent().find('select').trigger('change');

		// update counter
		$(this).parent().data({'bunyad_tabs': tabs_count, 'max_id': max_id});
		
		e.stopPropagation();
		return false;
	});
});
