elgg.provide('elgg.ajaxify.groups');

/**
 * @name elgg.ajaxify.groups
 * @namespace 
 */

elgg.ajaxify.groups;

/**
 * All groups plugin related initializations
 */

elgg.ajaxify.groups.init = function() {
	$('.elgg-menu-item-groups-join a').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'groups', {'type': 'join'}, {
			'link': $(this)
		});
		elgg.trigger_hook('update:success', 'groups', {'type': 'join'}, {
			'link': $(this)
		});
		return false;
	});
	$('.elgg-menu-item-groups-leave a').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'groups', {'type': 'leave'}, {
			'link': $(this)
		});
		elgg.trigger_hook('update:success', 'groups', {'type': 'leave'}, {
			'link': $(this)
		});
		return false;
	});
	
	elgg.ajaxify.ajaxForm($('#invite_to_group'), 'update', 'groups', {'type': 'invite'});

	//@todo Change the selector to form id when #3535 is fixed
	elgg.ajaxify.ajaxForm($('#group-replies form'), 'create', 'groups', {'type': 'reply'});
};

/**
 * Throw up the AJAXLoader
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.groups.update_submit = function(hook, type, params, value) {
	if (params.type === 'join' || 'leave') {
		$(value.link).after(elgg.ajaxify.ajaxLoader);
	}
	if (params.type === 'invite') {
		$(value.formObj).after(elgg.ajaxify.ajaxLoader);
	}
};

/**
 * Reverse the actions of buttons after a successful update
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.groups.update_success = function(hook, type, params, value) {
	if (params.type === 'join' || 'leave') {
		elgg.action($(value.link).url().attr('source'), {
			success: function() {
				elgg.ajaxify.ajaxLoader.remove();
				if (params.type === 'join') {
					$(value.link).html(elgg.echo('groups:leave'));
					$(value.link).replaceAttr('href', 'groups/join', 'groups/leave');
					$(value.link).parent().replaceAttr('class', 'join', 'leave');
				} else if (params.type === 'leave') {
					$(value.link).html(elgg.echo('groups:join'));
					$(value.link).replaceAttr('href', 'groups/leave', 'groups/join');
					$(value.link).parent().replaceAttr('class', 'leave', 'join');
				}
			}
		});
	}
	if (params.type === 'invite') {
		elgg.ajaxify.ajaxLoader.remove();
		$(value.formObj).resetForm()
	}
};

/**
 * Handle any errors during update
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.groups.update_error = function(hook, type, params, value) {
	if (params.type === 'invite') {
		elgg.register_error(value.reqStatus);
		elgg.ajaxify.ajaxLoader.remove();
	}
};

/**
 * Show the AJAXLoader before submitting the reply form
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.groups.create_submit = function(hook, type, params, value) {
	if (params.type === 'reply') {
		$(value.formObj).before(elgg.ajaxify.ajaxLoader);
	}
};

/**
 * Update the annotation list by appending the posted reply
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.groups.create_success = function(hook, type, params, value) {
	if (params.type === 'reply') {
		var guid = $(value.formObj).find('input[name=entity_guid]').val();
		elgg.view('annotations/getannotations', {
			cache: false,
			data: {
				'limit': 1,
				'annotation_name': 'group_topic_post',
				'guid': guid
			},
			success: function(response) {
				var replies_list = $(value.formObj).prevUntil('', 'ul.elgg-annotation-list');
				var replies_len = $(replies_list).children().length;
				var annotations = $(response).find('.elgg-item');
				elgg.ajaxify.ajaxLoader.remove();
				if (replies_len) {
					$(replies_list).append(annotations);
				} else {
					annotations = $(response);
					$(value.formObj).before(annotations);
				}
				//Reset the form
				$(value.formObj).resetForm();
			}
		});
	}
};

/**
 * Notify the user about the occurred error
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.groups.create_error = function(hook, type, params, value) {
	if (params.type === 'reply') {
		//Restore the form for user to retry 
		elgg.register_error(value.reqStatus);
		elgg.ajaxify.ajaxLoader.remove();
	}
};

elgg.register_hook_handler('create:success', 'groups', elgg.ajaxify.groups.create_success); 
elgg.register_hook_handler('create:submit', 'groups', elgg.ajaxify.groups.create_submit); 
elgg.register_hook_handler('create:error', 'groups', elgg.ajaxify.groups.create_error); 
elgg.register_hook_handler('update:submit', 'groups', elgg.ajaxify.groups.update_submit); 
elgg.register_hook_handler('update:success', 'groups', elgg.ajaxify.groups.update_success); 
elgg.register_hook_handler('update:error', 'groups', elgg.ajaxify.groups.update_error); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.groups.init);
