elgg.provide('elgg.ajaxify.messages');

elgg.ajaxify.messages.init = function() {
	elgg.ajaxify.messages.msg_counter = $('.elgg-menu-item-messages').find('.messages-new');
	elgg.ajaxify.ajaxForm($('#messages-inbox-form'), 'update', 'messages', {'type': 'inbox'});
};

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

elgg.ajaxify.messages.update_error = function(hook, type, params, value) {
	if (params.type === 'inbox') {
		$('#messages-inbox-form').find('input:checked').closest('.message').each(function() {
			$(this).addClass('unread');
		});
	}
};

elgg.ajaxify.messages.ping_submit = function(hook, type, params, value) {
	elgg.ajaxify.messages.requestID = elgg.ajaxify.refresh.getRequestID();
	value[elgg.ajaxify.messages.requestID] = ['view', {
		name: 'messages/count'
	}];
	return value;
};

elgg.ajaxify.messages.ping_success = function(hook, type, params, value) {
	var unread = value.__elgg_client_results[elgg.ajaxify.messages.requestID];
	if (elgg.ajaxify.messages.msg_counter.length > 0) {
		$(elgg.ajaxify.messages.msg_counter).replaceWith(unread);
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
