$.tabs = function(selector, start) {
	$(selector).each(function(i, element) { 
		$($(element).attr('tab')).addClass('hidden');
		
		$(element).click(function() {
			$(selector).each(function(i, element) {
				$(element).removeClass('selected');
				
					$($(element).attr('tab')).addClass('hidden');
			});
			
			$(this).addClass('selected');
			$($(element).attr('tab')).removeClass('hidden'); 
		});
	});
	
	if (!start) {
		start = $(selector + ':first').attr('tab');
	}

	$(selector + '[tab=\'' + start + '\']').trigger('click');
};