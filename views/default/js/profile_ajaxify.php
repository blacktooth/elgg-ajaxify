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
};

elgg.ajaxify.profile.update_submit = function(hook, type, params, value) {
	if (params.type === 'addfriend') {
		$(value.link).after(elgg.ajaxify.ajaxLoader);
	}
};

//Incomplete
elgg.ajaxify.profile.update_success = function(hook, type, params, value) {
	if (params.type === 'addfriend') {
		elgg.action($(value.link).url(), {
			data: {
				friend: $(value.link).url().param('friend'),
			},
			success: function() {
				elgg.ajaxify.ajaxLoader.remove();
				$(value.link).html(elgg.echo('friend:remove'));
				elgg.ajaxify.attrReplace($(value.link), 'href', 'friends/add', 'friends/remove');
			},
		});
	}
};

elgg.register_hook_handler('update:submit', 'profile', elgg.ajaxify.profile.update_submit); 
elgg.register_hook_handler('update:success', 'profile', elgg.ajaxify.profile.update_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.profile.init);
