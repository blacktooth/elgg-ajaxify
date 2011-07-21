elgg.provide('elgg.ajaxify.bookmarks');

elgg.ajaxify.bookmarks.init = function() {
	//@todo Change selector to include form id too -- requires #3535 to be fixed
	$('input[name=address]').live('blur', function(event) {
		elgg.trigger_hook('update:submit', 'bookmarks', {'type': 'autofill'}, {
			'inputObj': $(this)
		});
	});
};

elgg.ajaxify.bookmarks.update_submit = function(hook, type, params, value) {
	$(value.inputObj).after(elgg.ajaxify.ajaxLoader);
	elgg.trigger_hook('update:success', 'bookmarks', {'type': 'autofill'}, {
		uri: $(value.inputObj).val()
	});
};

elgg.ajaxify.bookmarks.update_success = function(hook, type, params, value) {
	elgg.view('bookmarks/autofill', {
		cache: false,
		data: {
			uri: value.uri
		},
		success: function(response) {
			elgg.ajaxify.ajaxLoader.remove();
			//Incomplete
		}
	});
};

elgg.register_hook_handler('update:submit', 'bookmarks', elgg.ajaxify.bookmarks.update_submit); 
elgg.register_hook_handler('update:success', 'bookmarks', elgg.ajaxify.bookmarks.update_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.bookmarks.init);
