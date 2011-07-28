elgg.provide('elgg.ajaxify.refresh');

elgg.ajaxify.refresh.init = function() {
	//refresh registered views and security tokens every 5 minutes
	//this is set in the js/elgg PHP view.
	setInterval(function() {
		__elgg_client_requests = elgg.trigger_hook('ping:submit', 'system', null, {});
		elgg.view('system/ping', {
			dataType: 'json',
			data: {
				__elgg_client_requests: __elgg_client_requests
			},
			success: function(response) {
				elgg.trigger_hook('ping:success', 'system', null, {
					__elgg_client_results: response
				});
			}
		});
	},
	elgg.security.interval);
};

elgg.ajaxify.refresh.getRequestID = function() {
	var ID = "";
	var universe = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	for (var i = 0; i < 8; i++) {
		ID += universe.charAt(Math.floor(Math.random() * universe.length));
	}
	return ID;
};

elgg.register_hook_handler('init', 'system', elgg.ajaxify.refresh.init);
