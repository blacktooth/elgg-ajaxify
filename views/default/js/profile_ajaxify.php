elgg.provide('elgg.ajaxify.profile');

/**
 * @name elgg.ajaxify.profile
 * @namespace
 */

elgg.ajaxify.profile;

/**
 * Binds the links/forms on the profile page to AJAX actions
 */
 
elgg.ajaxify.profile.init = function() {
	$('.elgg-button-action-addfriend').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'profile', {'type': 'addfriend'}, {
			'link': $(this)
		});
		return false;
	});
	$('.elgg-button-action-removefriend').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'profile', {'type': 'removefriend'}, {
			'link': $(this)
		});
		return false;
	});
};

/**
 * Notify the server about the action performed
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.profile.update_submit = function(hook, type, params, value) {
	$(value.link).after(elgg.ajaxify.ajaxLoader);
	elgg.action($(value.link).url().attr('source'), {
		data: {
			friend: $(value.link).url().param('friend')
		},
		success: function() {
			if (params.type === 'addfriend') {
				elgg.trigger_hook('update:success', 'profile', {'type': 'addfriend'}, {
					'link': $(value.link)
				});
			} else if (params.type === 'removefriend') {
				elgg.trigger_hook('update:success', 'profile', {'type': 'removefriend'}, {
					'link': $(value.link)
				});
			}
		}
	});
};

/**
 * Update the DOMs and reverse their actions
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.profile.update_success = function(hook, type, params, value) {
	elgg.ajaxify.ajaxLoader.remove();
	if (params.type === 'addfriend') {
		$(value.link).html(elgg.echo('friend:remove'));
		$(value.link).replaceAttr('href', 'friends/add', 'friends/remove');
		$(value.link).replaceAttr('class', 'action-addfriend', 'action-removefriend');
	} else if (params.type === 'removefriend') {
		$(value.link).html(elgg.echo('friend:add'));
		$(value.link).replaceAttr('href', 'friends/remove', 'friends/add');
		$(value.link).replaceAttr('class', 'action-removefriend', 'action-addfriend');
	}
};

elgg.register_hook_handler('update:submit', 'profile', elgg.ajaxify.profile.update_submit); 
elgg.register_hook_handler('update:success', 'profile', elgg.ajaxify.profile.update_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.profile.init);
