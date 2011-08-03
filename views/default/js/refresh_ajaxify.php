elgg.provide('elgg.ajaxify.refresh');

elgg.ajaxify.refresh.init = function() {
	elgg.ajaxify.refresh.setup(elgg.security.interval);
	elgg.ajaxify.refresh.retryInterval = 1000;
	elgg.ajaxify.refresh.pingError = false;
};

elgg.ajaxify.refresh.setup = function(interval) {
	elgg.ajaxify.refresh.timer = setInterval(function() {
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
			},
			error: function(xhr, textStatus, errorThrown) {
				elgg.trigger_hook('ping:error', 'system', null, {
					'textStatus': textStatus,
					'xhr': xhr
				});
			}
		});
	},
	interval);
};

elgg.ajaxify.refresh.ping_error = function(hook, type, params, value) {
	elgg.ajaxify.refresh.pingError = true;

	//Set saturation time to stop exponential backoff
	if (elgg.ajaxify.refresh.retryInterval < 5 * 60 * 1000) {
		elgg.ajaxify.refresh.retryInterval *= 2;
		//Clear old timer and setup new timer
		clearTimeout(elgg.ajaxify.refresh.timer);
		elgg.ajaxify.refresh.setup(elgg.ajaxify.refresh.retryInterval);
	}

	if (elgg.ajaxify.refresh.retryInterval < 2 * 60 * 1000) {
		elgg.register_error(elgg.echo('ping:error', [elgg.ajaxify.refresh.retryInterval / 1000, elgg.echo('time:seconds')]));
	} else {
		elgg.register_error(elgg.echo('ping:error', [Math.floor(elgg.ajaxify.refresh.retryInterval / 1000 / 60), elgg.echo('time:minutes')]));
	}
};

elgg.ajaxify.refresh.ping_success = function(hook, type, params, value) {
	//Reset the timer to normal state if connection is back again
	if (elgg.ajaxify.refresh.pingError) {
		elgg.ajaxify.refresh.pingError = false;
		clearTimeout(elgg.ajaxify.refresh.timer);
		elgg.ajaxify.refresh.setup(elgg.security.interval);
		elgg.system_message(elgg.echo('ping:success'));
	}
};

elgg.ajaxify.refresh.getRequestID = function() {
	var ID = "";
	var universe = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	for (var i = 0; i < 8; i++) {
		ID += universe.charAt(Math.floor(Math.random() * universe.length));
	}
	return ID;
};

elgg.register_hook_handler('ping:error', 'system', elgg.ajaxify.refresh.ping_error);
elgg.register_hook_handler('ping:success', 'system', elgg.ajaxify.refresh.ping_success, 0);
elgg.register_hook_handler('init', 'system', elgg.ajaxify.refresh.init);
