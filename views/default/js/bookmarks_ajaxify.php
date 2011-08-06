elgg.provide('elgg.ajaxify.bookmarks');

/**
 * @namespace 
 */

elgg.ajaxify.bookmarks = elgg.ajaxify.bookmarks || {};

/**
 * All bookmarks related initializations
 */

elgg.ajaxify.bookmarks.init = function() {
	//@todo Change selector to include form id too -- requires #3535 to be fixed
	$('input[name=address]').live('blur', function(event) {
		if ($.trim($(this).val()) !== '') {
			elgg.trigger_hook('update:submit', 'bookmarks', {'type': 'autofill'}, {
				'inputObj': $(this)
			});
		}
	});
};

/**
 * Show AJAXLoader while fetching the title, description and tags from the autofill view.
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.bookmarks.update_submit = function(hook, type, params, value) {
	$(value.inputObj).after(elgg.ajaxify.ajaxLoader);
	elgg.view('bookmarks/autofill', {
		cache: false,
		dataType: 'json',
		data: {
			uri: $(value.inputObj).val()
		},
		success: function(response) {
			if (response) {
				elgg.trigger_hook('update:success', 'bookmarks', {'type': 'autofill'}, {
					responseText: response
				});
			}
		}
	});
};

/**
 * Check the editor in use and update it with the fetched webpage data.
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.bookmarks.update_success = function(hook, type, params, value) {
	elgg.ajaxify.ajaxLoader.remove();
	$('input[name=title]').val(value.responseText.title);
	$('input[name=tags]').val(value.responseText.keywords);

	//Check which editor is in use
	if ($('.mceEditor').length === 1 && value.responseText.description) {
		tinyMCE.execCommand("mceInsertContent", false, value.responseText.description);
	} else {
		$('textarea[name=description]').val(value.responseText.description);
	}
};

elgg.register_hook_handler('update:submit', 'bookmarks', elgg.ajaxify.bookmarks.update_submit); 
elgg.register_hook_handler('update:success', 'bookmarks', elgg.ajaxify.bookmarks.update_success); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.bookmarks.init);
