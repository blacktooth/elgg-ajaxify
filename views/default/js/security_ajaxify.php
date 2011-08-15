elgg.provide('elgg.ajaxify.security');

/**
 * @name elgg.ajaxify.security
 * @namespace
 */

elgg.ajaxify.security;

/**
 * Update the security tokens everywhere
 * 
 * @param {String} tokens Fresh tokens from ping response 
 */

elgg.ajaxify.security.updateTokens = function(tokens) {
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

elgg.request('securitytokens', null, elgg.ajaxify.security.updateTokens);
