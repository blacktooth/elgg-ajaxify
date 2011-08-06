elgg.provide('elgg.ajaxify.messages');

/**
 * @namespace
 */

elgg.ajaxify.messages = elgg.ajaxify.messages || {}

/**
 * All messages plugin related initializations
 */

elgg.ajaxify.messages.init = function() {
	elgg.ajaxify.messages.msg_counter = $('.elgg-menu-item-messages').find('.messages-new');
	elgg.ajaxify.ajaxForm($('#messages-inbox-form'), 'update', 'messages', {'type': 'inbox'});
};

/**
 * Mark the selected messages as 'read' before notifying the server about the change
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.messages.update_submit = function(hook, type, params, value) {
	if (params.type === 'inbox') {
		var checked_msgs = $(value.formObj).find('input:checked').closest('.unread');
		//Keeping track of marked messages
		elgg.ajaxify.messages.affected = checked_msgs.length;

		checked_msgs.each(function() {
			$(this).removeClass('unread');
		});
	}
};

/**
 * Update the new message notifier icon after notifying the server about the marked messages
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.messages.update_success = function(hook, type, params, value) {
	if (params.type === 'inbox') {
		var msg_count = parseInt($(elgg.ajaxify.messages.msg_counter).html());
		var diff = msg_count - elgg.ajaxify.messages.affected;
		if (diff > 0) {
			$(elgg.ajaxify.messages.msg_counter).html(String(diff));
		} else {
			$(elgg.ajaxify.messages.msg_counter).remove();
		}
	}
};

/**
 * Revert the marked messages to 'unread' state if the update fails
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.messages.update_error = function(hook, type, params, value) {
	if (params.type === 'inbox') {
		$('#messages-inbox-form').find('input:checked').closest('.message').each(function() {
			$(this).addClass('unread');
		});
	}
};

/**
 * Ping the message counter view to get updates on new messages
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.messages.ping_submit = function(hook, type, params, value) {
	elgg.ajaxify.messages.requestID = elgg.ajaxify.refresh.getRequestID();
	value[elgg.ajaxify.messages.requestID] = ['view', {
		name: 'messages/count'
	}];
	return value;
};

/**
 * Update the message notifier icon if a new message arrives during the ping
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.messages.ping_success = function(hook, type, params, value) {
	var unread = value.__elgg_client_results[elgg.ajaxify.messages.requestID];
	if (elgg.ajaxify.messages.msg_counter.length > 0) {
		$('.elgg-menu-item-messages').find('.messages-new').replaceWith(unread);
	} else {
		$('.elgg-menu-item-messages a').append(unread);
	}
	
};

elgg.register_hook_handler('update:submit', 'messages', elgg.ajaxify.messages.update_submit); 
elgg.register_hook_handler('update:success', 'messages', elgg.ajaxify.messages.update_success); 
elgg.register_hook_handler('update:error', 'messages', elgg.ajaxify.messages.update_error); 
elgg.register_hook_handler('ping:submit', 'system', elgg.ajaxify.messages.ping_submit); 
elgg.register_hook_handler('ping:success', 'system', elgg.ajaxify.messages.ping_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.messages.init);
