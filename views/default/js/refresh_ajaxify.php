elgg.provide('elgg.ajaxify.refresh');

/**
 * @name elgg.ajaxify.refresh
 * @namespace
 */

elgg.ajaxify.refresh;

/**
 * Reduce ping interval to this value if the previous ping fails 
 */
 
elgg.ajaxify.refresh.retryInterval = 1000;

/**
 * It is set to true if the previous ping fails
 */

elgg.ajaxify.refresh.pingError = false;

/**
 * Initialize and start pinging
 */

elgg.ajaxify.refresh.init = function() {
	elgg.ajaxify.refresh.setup(elgg.security.interval);
};

/**
 * Setup the timer and trigger submit, success hooks
 * @param {Integer} interval Ping interval in milliseconds
 * @return {void}
 */

elgg.ajaxify.refresh.setup = function(interval) {
	elgg.ajaxify.refresh.timer = setInterval(function() {
		elgg.view('system/ping', {
			dataType: 'json',
			data: {
				__elgg_client_requests: __elgg_client_requests
			},
			success: function(response) {
				for (var requestID in __elgg_request_handlers) {
					__elgg_request_handlers[requestID](response[requestID]);
				}
			},
			error: function(xhr, textStatus, errorThrown) {
				elgg.ajaxify.refresh.ping_error({
					'textStatus': textStatus,
					'xhr': xhr
				});
			}
		});
	},
	interval);
};

/**
 * Clear old timers and setup new ones with reduced interval in case a ping error occours
 * 
 * @param {Object} error 
 */

elgg.ajaxify.refresh.ping_error = function(error) {
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

/**
 * Reset the timers to normal intervals after a succesful retry
 * 
 * @param {response} response 
 */

elgg.ajaxify.refresh.ping_success = function(response) {
	//Reset the timer to normal state if connection is back
	if (elgg.ajaxify.refresh.pingError) {
		elgg.ajaxify.refresh.pingError = false;
		clearTimeout(elgg.ajaxify.refresh.timer);
		elgg.ajaxify.refresh.setup(elgg.security.interval);
		elgg.system_message(elgg.echo('ping:success'));
	}
};

elgg.register_hook_handler('init', 'system', elgg.ajaxify.refresh.init, 1000);
