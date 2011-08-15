elgg.provide('elgg.ajaxify.likes');

/**
 * @name elgg.ajaxify.likes
 * @namespace
 */

elgg.ajaxify.likes;

/**
 * Initialize all ajaxify like/unlike actions
 */

elgg.ajaxify.likes.init = function() {
	elgg.ajaxify.likes.thumbsUp = 'elgg-icon-thumbs-up';
	elgg.ajaxify.likes.thumbsUpAlt = 'elgg-icon-thumbs-up-alt';

	$('.elgg-menu-item-likes a').live('click', function(event) {
		elgg.trigger_hook('update:submit', 'likes', {'type': 'entity_menu'}, {
			'link': $(this)
		});
		return false;
	});
};

/**
 * Update the DOM instantaneously and trigger success hook to notify the server about the update.
 * Triggers error hook to revert the DOM in case the update fails.
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.likes.update_submit = function(hook, type, params, value) {
	$(value.link).find('span').toggleClass('elgg-icon-thumbs-up elgg-icon-thumbs-up-alt');

	var url = $(value.link).url().attr('source');
	elgg.action(url, {
		success: function() {
			elgg.trigger_hook('update:success', 'likes', {'type': 'entity_menu'}, {
				'link': $(value.link),
				'url': url
			});
		},
		error: function() {
			elgg.trigger_hook('update:error', 'likes', {'type': 'entity_menu'}, {
				'link': $(value.link)
			});
		}
	});
};

/**
 * Update the likes counter after a successful update.
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.likes.update_success = function(hook, type, params, value) {
	try {
		//Get the like counter for updation
		var likes_counter = $(value.link).parents().siblings('.elgg-menu-item-likes-count').children('a');
		if (likes_counter.length !== 0) {
			var likes_count = parseInt($(likes_counter).html().match(/\d+/));
		}
		if (value.url.search('likes/add') !== -1) {
			$(value.link).replaceAttr('href', 'likes/add', 'likes/delete');
			$(likes_counter).html($(likes_counter).html().replace(/\d+/, String(++likes_count)));
		} else {
			$(value.link).replaceAttr('href', 'likes/delete', 'likes/add');
			$(likes_counter).html($(likes_counter).html().replace(/\d+/, String(--likes_count)));
			if (likes_count === 0) {
				$(likes_counter).remove();
			}
		}
	} catch(e) {}
};

/**
 * Hook to handle errors when update fails. Reverts the DOM to previous state.
 * 
 * @param {String} hook {create|read|update|delete|ping}:{submit|success|error}
 * @param {String} type 
 * @param {Object} params Parameters to pass to the hook
 * @param {Object} value return value that can be manipulated by the hook
 */

elgg.ajaxify.likes.update_error = function(hook, type, params, value) {
	var like_icon = $(value.link).find('span');
	//Revert back
	$(value.link).find('span').toggleClass('elgg-icon-thumbs-up elgg-icon-thumbs-up-alt');
};

elgg.register_hook_handler('update:submit', 'likes', elgg.ajaxify.likes.update_submit);
elgg.register_hook_handler('update:success', 'likes', elgg.ajaxify.likes.update_success);
elgg.register_hook_handler('update:error', 'likes', elgg.ajaxify.likes.update_error);
elgg.register_hook_handler('init', 'system', elgg.ajaxify.likes.init);
