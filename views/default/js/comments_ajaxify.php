elgg.provide('elgg.ajaxify.comments');

/**
 * @namespace
 */

elgg.ajaxify.comments = elgg.ajaxify.comments || {};

/**
 * Limit the number of comments which are fetched on clicking 'more'. This avoids flooding of whole page if there is a long list.
 */

elgg.ajaxify.comments.more_limit = 50;

/**
 * Bind all comments related forms and links to ajax actions
 */

elgg.ajaxify.comments.init = function() {

	elgg.ajaxify.ajaxForm($('form[id^=comments-add-]'), 'create', 'comments', {'type': 'river'});
	elgg.ajaxify.ajaxForm($('form[name=elgg_add_comment]'), 'create', 'comments', {'type': 'plugin'});

	$('.elgg-river-more a').live('click', function(event) {
		elgg.trigger_hook('read:submit', 'comments', {'type': 'river'}, {
			'link': $(this)
		});
		elgg.trigger_hook('read:success', 'comments', {'type': 'river'}, {
			'link': $(this)
		});
		return false;
	});
	
	$('li[id^=item-annotation-]').find('.elgg-requires-confirmation').livequery('click', function(event) {
		elgg.trigger_hook('delete:submit', 'comments', null, {
			'link': $(this)
		});
		return false;
	});
};

/**
 * Show the AJAXLoader before submitting the form
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.create_submit = function(hook, type, params, value) {
	if (params.type === 'river') {
		$(value.formObj).before(elgg.ajaxify.ajaxLoader);
		$(value.formObj).hide('fast');
	}
	if (params.type === 'plugin') {
		$(value.formObj).before(elgg.ajaxify.ajaxLoader);
	}
};

/**
 * Add the newly added comment to the annotation list. Also update the comment counter in case of river.
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.create_success = function(hook, type, params, value) {
	if (params.type === 'river') {
		var guid = $(value.formObj).find('input[name=entity_guid]').val();
		elgg.view('annotations/getannotations', {
			cache: false,
			data: {
				'limit': 1,
				'annotation_name': 'generic_comment',
				'guid': guid
			},
			success: function(response) {
				var comments_list = $(value.formObj).prevUntil('', 'ul.elgg-river-comments');
				var comments_len = $(comments_list).children().length;
				var annotations = $(response).find('.elgg-item');
				elgg.ajaxify.ajaxLoader.remove();
				if (comments_len) {
					if (comments_len < 3) {
						$(comments_list).append(annotations);
					} else {
						//Update the more counter
						var more_counter = $(value.formObj).prev('.elgg-river-more').find('a');
						if (more_counter.length !== 0) {
							var count  = parseInt($(more_counter).html().match(/\+(\d+)/)[1]) + 1;
							$(more_counter).html($(more_counter).html().replace(/\d+/, String(count)));
							$(comments_list).find('li:first').slideUp('fast');
							$(comments_list).find('li:first').remove();
						}
						$(comments_list).append(annotations);
					}
				} else {
					annotations = $(response);
					$(annotations).first().addClass('elgg-river-comments');
					$(value.formObj).before(annotations);
				}
				//Reset the form
				$(value.formObj).resetForm();
			}
		});
	}
	if (params.type === 'plugin') {
		var guid = $(value.formObj).find('input[name=entity_guid]').val();
		elgg.view('annotations/getannotations', {
			cache: false,
			data: {
				'limit': 1,
				'annotation_name': 'generic_comment',
				'guid': guid
			},
			success: function(response) {
				var comments_list = $(value.formObj).prevUntil('', 'ul.elgg-annotation-list');
				var comments_len = $(comments_list).children().length;
				var annotations = $(response).find('.elgg-item');
				elgg.ajaxify.ajaxLoader.remove();
				if (comments_len) {
					$(comments_list).append(annotations);
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
 * Error handler hook  
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.create_error = function(hook, type, params, value) {
	if (params.type === 'river') {
		//Restore the form for user to retry 
		$(elgg.ajaxify.ajaxLoader).next().show('fast');
		elgg.register_error(value.reqStatus);
		elgg.ajaxify.ajaxLoader.remove();
	}
};

/**
 * Show AJAXLoader before fetching the old comments.
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.read_submit = function(hook, type, params, value) {
	if (params.type === 'river') {
		$(value.link).parent('.elgg-river-more').before(elgg.ajaxify.ajaxLoader);
		$(value.link).parent('.elgg-river-more').hide();
	}
};

/**
 * Update the annotation list with fetched old comments and decrement the comments counter
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.read_success = function(hook, type, params, value) {
	if (params.type === 'river') {
		var guid = $(value.link).parent('.elgg-river-more').next('form[id^=comments-add-]').find('input[name=entity_guid]').val();
		var count  = parseInt($(value.link).html().match(/\+(\d+)/)[1]);
		elgg.view('annotations/getannotations', {
			cache: false,
			data: {
				//Calculate offset and limit 
				'offset': (count < elgg.ajaxify.comments.more_limit)?0:count - elgg.ajaxify.comments.more_limit,
				'limit': (count < elgg.ajaxify.comments.more_limit)?count:elgg.ajaxify.comments.more_limit,
				'annotation_name': 'generic_comment',
				'guid': guid,
				'order': 'asc'
			},
			success: function(response) {
				var annotations = $(response).find('.elgg-item');
				$(value.link).parent('.elgg-river-more').prevUntil('', '.elgg-river-comments').prepend(annotations);

				if (count > elgg.ajaxify.comments.more_limit) {
					$(value.link).html($(value.link).html().replace(/\d+/, String(count - elgg.ajaxify.comments.more_limit)));
					$(value.link).parent('.elgg-river-more').show();
				} else {
					$(value.link).remove();
				}
				elgg.ajaxify.ajaxLoader.remove();
			},
			error: function() {
				$(value.link).parent('.elgg.river-more').show();
				elgg.ajaxify.ajaxLoader.remove();
				window.location = $(value.link).attr('href');
			}
		});
	}
};

/**
 * Hide the comment from annotation list before actually deleting it.
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.delete_submit = function(hook, type, params, value) {
	var confirmText = $(value.link).attr('rel') || elgg.echo('question:areyousure');
	if (!confirm(confirmText)) {
		return false;
	}

	$(value.link).closest('li[id^=item-annotation-]').slideUp('fast');
	elgg.action($(value.link).url().attr('source'), {
		success: function() {
			elgg.trigger_hook('delete:success', 'comments', null, {
				'link': $(value.link)
			});
		},
		error: function(xhr, textStatus, errorThrown) {
			elgg.trigger_hook('delete:error', 'comments', null, {
				'textStatus': textStatus,
				'xhr': xhr,
				'link': $(value.link)
			});
		}
	});
};

/**
 * Use this to perform any post delete actions.
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.delete_success = function(hook, type, params, value) {
};

/**
 * Restore the comment if delete action fails.
 *
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.comments.delete_error = function(hook, type, params, value) {
	elgg.register_error(value.textStatus);
	$(value.link).closest('li[id^=item-annotation-]').slideDown('slow');
};

elgg.register_hook_handler('create:success', 'comments', elgg.ajaxify.comments.create_success); 
elgg.register_hook_handler('create:submit', 'comments', elgg.ajaxify.comments.create_submit); 
elgg.register_hook_handler('create:error', 'comments', elgg.ajaxify.comments.create_error); 
elgg.register_hook_handler('read:success', 'comments', elgg.ajaxify.comments.read_success); 
elgg.register_hook_handler('read:submit', 'comments', elgg.ajaxify.comments.read_submit); 
elgg.register_hook_handler('delete:submit', 'comments', elgg.ajaxify.comments.delete_submit); 
elgg.register_hook_handler('delete:success', 'comments', elgg.ajaxify.comments.delete_success); 
elgg.register_hook_handler('delete:error', 'comments', elgg.ajaxify.comments.delete_error); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.comments.init);
