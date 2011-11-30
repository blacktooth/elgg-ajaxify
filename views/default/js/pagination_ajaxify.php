elgg.provide('elgg.ajaxify.pagination');

/**
 * @name elgg.ajaxify.pagination
 * @namespace
 */

elgg.ajaxify.pagination;

/**
 * Get context from the viewname (activity, thewire, blog etc.).
 * (Why?) Activity river stream and entity lists use different DOMs.
 */

elgg.ajaxify.pagination.context = elgg.ajaxify.getViewFromURL('activity').split('/')[0];

/**
 * Bind all pagination related selectors to AJAX actions
 */

elgg.ajaxify.pagination.init = function() {
	$('.elgg-pagination a').live('click', function(event) {
		elgg.trigger_hook('read:submit', 'pagination', {'type': elgg.ajaxify.pagination.context}, {
			'link': $(this)
		});
		elgg.trigger_hook('read:success', 'pagination', {'type': elgg.ajaxify.pagination.context}, {
			'link': $(this)
		});
		return false;
	});
};

/**
 * Throw up the AJAX Loader while fetching the new page contents
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.pagination.read_submit = function(hook, type, params, value) {
	$('.elgg-pagination').before(elgg.ajaxify.ajaxLoader);
};

/**
 * Replace the current list with newly fetched items and scroll to the top
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.pagination.read_success = function(hook, type, params, value) {
	//Activity river can be filtered using elgg-river-selector, the filter selected has to be preserved when a next page is requested, 
	//sending type, subtype, page_type helps in getting the exact content under the applied filters
	if (params.type === 'activity') {
		elgg.view('river/getactivity', {
			cache: false,
			data: {
				type: $.url().param('type') || 'all',
				subtype: $.url().param('subtype') || '',
				page_type: elgg.ajaxify.getViewFromURL('').split('/')[1] || '',
				offset: $(value.link).url().param('offset') || '' 
			},
			success: function(response) {
				var newRiver = $(response)[0];
				var newPagination = $(response)[1];
				$('.elgg-river').replaceWith(newRiver);
				$('.elgg-pagination').replaceWith(newPagination);
				$(elgg.ajaxify.ajaxLoader).remove();
				$('body').animate({scrollTop: 0}, 'slow');
			}
		});
	} else {
		elgg.view('entities/getentity', {
			cache: false,
			data: {
				//Groups have different type
				type: (elgg.ajaxify.pagination.context === 'groups')?'group':'object',
				subtype: (elgg.ajaxify.pagination.context === 'groups')?undefined:elgg.ajaxify.pagination.context,
				limit: 15,
				page_type: elgg.ajaxify.getViewFromURL('').split('/')[1] || '',
				pagination: 'TRUE',
				offset: $(value.link).url().param('offset') || ''
			},
			success: function(response) {
				var newList = $(response)[0];
				var newPagination = $(response)[1];
				$('.elgg-list-entity').replaceWith(newList);
				$('.elgg-pagination').replaceWith(newPagination);
				$(elgg.ajaxify.ajaxLoader).remove();
				$('body').animate({scrollTop: 0}, 'slow');
			}
		});
	}
};

elgg.register_hook_handler('read:submit', 'pagination', elgg.ajaxify.pagination.read_submit); 
elgg.register_hook_handler('read:success', 'pagination', elgg.ajaxify.pagination.read_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.pagination.init);
