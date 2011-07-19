elgg.provide('elgg.ajaxify.groups');

elgg.ajaxify.groups.init = function() {
	$('.elgg-menu-item-groups-join a').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'groups', {'type': 'join'}, {
			'link': $(this),
		});
		elgg.trigger_hook('update:success', 'groups', {'type': 'join'}, {
			'link': $(this),
		});
		return false;
	});
	$('.elgg-menu-item-groups-leave a').livequery('click', function() {
		elgg.trigger_hook('update:submit', 'groups', {'type': 'leave'}, {
			'link': $(this),
		});
		elgg.trigger_hook('update:success', 'groups', {'type': 'leave'}, {
			'link': $(this),
		});
		return false;
	});
};

elgg.ajaxify.groups.update_submit = function(hook, type, params, value) {
	$(value.link).after(elgg.ajaxify.ajaxLoader);
};

elgg.ajaxify.groups.update_success = function(hook, type, params, value) {
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
};

elgg.register_hook_handler('update:submit', 'groups', elgg.ajaxify.groups.update_submit); 
elgg.register_hook_handler('update:success', 'groups', elgg.ajaxify.groups.update_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.groups.init);
