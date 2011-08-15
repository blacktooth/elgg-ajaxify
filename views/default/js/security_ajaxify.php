elgg.provide('elgg.ajaxify.security');

/**
 * @name elgg.ajaxify.security
 * @namespace
 */

elgg.ajaxify.security;

/**
 * Request new security tokens from the server
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.security.ping_submit = function(hook, type, params, value) {
	elgg.ajaxify.security.requestID = elgg.ajaxify.refresh.getRequestID();
	value[elgg.ajaxify.security.requestID] = ['securitytokens', null];
	return value;
};

/**
 * Update the security tokens everywhere
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.security.ping_success = function(hook, type, params, value) {
	var tokens = value.__elgg_client_results[elgg.ajaxify.security.requestID];
	if (!tokens || !(tokens.__elgg_ts && tokens.__elgg_token)) {
		elgg.register_error(elgg.echo('js:security:token_refresh_failed', [elgg.get_site_url()]));
		elgg.security.tokenRefreshFailed = true;
		return;
	}
	if (elgg.security.tokenRefreshFailed) {
		elgg.system_message(elgg.echo('js:security:token_refreshed', [elgg.get_site_url()]));
		elgg.security.tokenRefreshFailed = false;
	}
		
	elgg.security.setToken(tokens);
};

elgg.register_hook_handler('ping:submit', 'system', elgg.ajaxify.security.ping_submit, 1000); 
elgg.register_hook_handler('ping:success', 'system', elgg.ajaxify.security.ping_success, 1000); 
