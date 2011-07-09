elgg.provide('elgg.ajaxify.profile');

elgg.ajaxify.profile.init = function() {
	$('.elgg-button-action-addfriend').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'profile', {'type': 'addfriend'}, {
			'link': $(this),
		});
		elgg.trigger_hook('update:success', 'profile', {'type': 'addfriend'}, {
			'link': $(this),
		});
		return false;
	});
	$('.elgg-button-action-removefriend').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'profile', {'type': 'removefriend'}, {
			'link': $(this),
		});
		elgg.trigger_hook('update:success', 'profile', {'type': 'removefriend'}, {
			'link': $(this),
		});
		return false;
	});
};

elgg.ajaxify.profile.update_submit = function(hook, type, params, value) {
	$(value.link).after(elgg.ajaxify.ajaxLoader);
};

elgg.ajaxify.profile.update_success = function(hook, type, params, value) {
	elgg.action($(value.link).url().attr('source'), {
		data: {
			friend: $(value.link).url().param('friend'),
		},
		success: function() {
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
		},
	});
};

elgg.register_hook_handler('update:submit', 'profile', elgg.ajaxify.profile.update_submit); 
elgg.register_hook_handler('update:success', 'profile', elgg.ajaxify.profile.update_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.profile.init);
