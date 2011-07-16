elgg.provide('elgg.ajaxify.likes');

elgg.ajaxify.likes.init = function(hook, type, params, value) {
	elgg.ajaxify.likes.thumbsUp = 'elgg-icon-thumbs-up';
	elgg.ajaxify.likes.thumbsUpAlt = 'elgg-icon-thumbs-up-alt';

	$('.elgg-menu-item-likes a').live('click', function(event) {
		elgg.trigger_hook('update:submit', 'likes', {'type': 'entity_menu'}, {
			'link': $(this),
		});
		elgg.trigger_hook('update:success', 'likes', {'type': 'entity_menu'}, {
			'link': $(this),
		});
		return false;
	});
};

elgg.ajaxify.likes.update_submit = function(hook, type, params, value) {
	var like_icon = $(value.link).find('span');
	elgg.ajaxify.likes.toggleClass(like_icon);
};

elgg.ajaxify.likes.update_success = function(hook, type, params, value) {
	var url = $(value.link).url().attr('source');
	elgg.action(url, {
		success: function(response) {
			try {
				//Get the like counter for updation
				var likes_counter = $(value.link).parents().siblings('.elgg-menu-item-likes-count').children('a');
				if (likes_counter.length !== 0) {
					var likes_count = parseInt($(likes_counter).html().match(/\d+/));
				}
				if (url.search('likes/add') !== -1) {
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
		},
		error: function() {
			elgg.trigger_hook('update:error', 'likes', {'type': 'entity_menu'}, {
				'link': $(value.link),
			});
		},		
	});
};

elgg.ajaxify.likes.update_error = function(hook, type, params, value) {
	var like_icon = $(value.link).find('span');
	//Revert back
	elgg.ajaxify.likes.toggleClass(like_icon);
};

elgg.ajaxify.likes.toggleClass = function(like_icon) {
	if ($(like_icon).hasClass(elgg.ajaxify.likes.thumbsUp)) {
		 $(like_icon).removeClass(elgg.ajaxify.likes.thumbsUp);
		 $(like_icon).addClass(elgg.ajaxify.likes.thumbsUpAlt);
	} else {
		 $(like_icon).removeClass(elgg.ajaxify.likes.thumbsUpAlt);
		 $(like_icon).addClass(elgg.ajaxify.likes.thumbsUp);
	}
};

elgg.register_hook_handler('update:submit', 'likes', elgg.ajaxify.likes.update_submit);
elgg.register_hook_handler('update:success', 'likes', elgg.ajaxify.likes.update_success);
elgg.register_hook_handler('update:error', 'likes', elgg.ajaxify.likes.update_error);
elgg.register_hook_handler('init', 'system', elgg.ajaxify.likes.init);
