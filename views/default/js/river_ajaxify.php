elgg.provide('elgg.ajaxify.river');

/**
 * @name elgg.ajaxify.river
 * @namespace
 */

elgg.ajaxify.river;

elgg.ajaxify.river.init = function() {
	elgg.request('view', {
		name: 'river/getactivity', 
		vars: {
			type: $.url().param('type') || 'all',
			subtype: $.url().param('subtype') || '',
			page_type: elgg.ajaxify.getViewFromURL('').split('/')[1] || '',
			//Put lower bound on list to last token update time
			posted_time_lower: elgg.security.token.__elgg_ts
		}
	}, function(items) {
		$(items).css('display', 'none');
		$('.elgg-river').prepend(items);
		$(items).fadeIn('slow');
	});  
};

elgg.register_hook_handler('init', 'system', elgg.ajaxify.river.init);
