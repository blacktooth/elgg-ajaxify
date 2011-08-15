elgg.provide('elgg.ajaxify.river');

/**
 * @name elgg.ajaxify.river
 * @namespace
 */

elgg.ajaxify.river;

/**
 * Ping the annotation view to get the latest activity 
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.river.ping_submit = function(hook, type, params, value) {
	elgg.ajaxify.river.requestID = elgg.ajaxify.refresh.getRequestID();
	//@todo Find a way to check offset and update only when the user is on latest page (i.e. pagination: 1)
	value[elgg.ajaxify.river.requestID] = ['view', {
		name: 'river/getactivity',
		vars: {
			type: $.url().param('type') || 'all',
			subtype: $.url().param('subtype') || '',
			page_type: elgg.ajaxify.getViewFromURL('').split('/')[1] || '',
			//Put lower bound on list to last token update time
			posted_time_lower: elgg.security.token.__elgg_ts
		}
	}];
	return value;
};

/**
 * Update the activity river
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.river.ping_success = function(hook, type, params, value) {
	var items = $(value.__elgg_client_results[elgg.ajaxify.river.requestID]).children();
	$(items).css('display', 'none');
	$('.elgg-river').prepend(items);
	$(items).fadeIn('slow');
};

elgg.register_hook_handler('ping:submit', 'system', elgg.ajaxify.river.ping_submit); 
elgg.register_hook_handler('ping:success', 'system', elgg.ajaxify.river.ping_success); 
