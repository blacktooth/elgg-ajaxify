elgg.provide('elgg.ajaxify.registration');

elgg.ajaxify.registration.init = function() {
	$('input[name=password]').pstrength({
		'displayMinChar': false,
		'minChar': 6
	});
	
	$('input[name=username]').live('blur', function() {
		elgg.trigger_hook('read:submit', 'registration', {type: 'checkusername'}, {
			usernameDOM: $(this)
		});
	});

	$('input[name=username]').live('focus', function() {
		$('.username-status').remove();
	});
};

elgg.ajaxify.registration.read_submit = function(hook, type, params, value) {
	if (params.type === 'checkusername') {
		$(value.usernameDOM).after(elgg.ajaxify.ajaxLoader);
		elgg.view('users/checkuser', {
			dataType: 'json',
			data: {
				q_username: $(value.usernameDOM).val()
			},
			success: function(response) {
				elgg.trigger_hook('read:success', 'registration', {type: 'checkusername'}, {
					usernameDOM: $(value.usernameDOM),
					responseText: response
				});
			}
		});
	}
};

elgg.ajaxify.registration.read_success = function(hook, type, params, value) {
	if (params.type === 'checkusername') {
		elgg.ajaxify.ajaxLoader.remove();
		if (value.responseText.user) {
			$(value.usernameDOM).after("<span class='username-status username-unavailable'>Not Available</span>");
		} else {
			$(value.usernameDOM).after("<span class='username-status username-available'>Available</span>");
		}
	}
};

elgg.register_hook_handler('read:submit', 'registration', elgg.ajaxify.registration.read_submit); 
elgg.register_hook_handler('read:success', 'registration', elgg.ajaxify.registration.read_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.registration.init);
