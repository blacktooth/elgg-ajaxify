elgg.provide('elgg.ajaxify.thewire');

elgg.ajaxify.thewire.init = function() {
	elgg.ajaxify.ajaxForm($('.thewire-form'), 'create', 'thewire', {'type': 'thewire/add'});
	$('.elgg-menu-item-reply').livequery(function() {
		$(this).toggle(function() {
			elgg.ajaxify.thewire.show_replyForm(this);
			$(this).find('a').html('Close');
		}, function() {
			$(this).find('a').html('Reply');
			$(this).closest('.elgg-item').find('div[id^=elgg-reply-div]').slideUp('fast');
		});
	});
		
};

elgg.ajaxify.thewire.create_success = function(hook, type, params, value) {
	if (params.type === 'thewire/add') {
		elgg.view('entities/getentity', {
			cache: false,
			data: {
				limit: '1',
				guid: value.responseText.output.guid,
				subtype: 'thewire',
			},
			success: function(entities_list) {
				$('#thewire-textarea').val('');
				var entities = $(entities_list).find('.elgg-item');
				$('.elgg-list-entity').prepend(entities);
				elgg.ajaxify.ajaxLoader.remove();
				elgg.thewire.textCounter($('#thewire-textarea'), $('#thewire-characters-remaining span'), 140);
			},
		});
	}
	if (params.type == 'thewire/reply') {
		elgg.view('entities/getentity', {
			cache: false,
			data: {
				limit: '1',
				guid: value.responseText.output.guid,	
				subtype: 'thewire',
			},
			success: function(entities_list) {
				var entities = $(entities_list).find('.elgg-item');
				$('.elgg-list-entity').prepend(entities);
				//Simulate click event on close
				$(value.formObj).closest('.elgg-item').find('li.elgg-menu-item-reply').click();
				$(replyDiv).remove();
				elgg.ajaxify.ajaxLoader.remove();
			},
		});
	}
};

elgg.ajaxify.thewire.create_submit = function(hook, type, params, value) {
	if (params.type === 'thewire/add') {
		$('.elgg-list-entity').prepend(elgg.ajaxify.ajaxLoader);
	}
	if (params.type === 'thewire/reply') {
		replyDiv = $(value.formObj).closest('div[id^=elgg-reply-div]');
		$(replyDiv).before(elgg.ajaxify.ajaxLoader);
		$(replyDiv).hide('fast');
	}
};

elgg.ajaxify.thewire.create_error = function(hook, type, params, value) {
	if (params.type === 'thewire/reply') {
		elgg.ajaxify.ajaxLoader.remove();
		elgg.register_error(value.reqStatus);
		$(value.replyDiv).show('fast');
	}

};

elgg.ajaxify.thewire.show_replyForm = function(item) {
	var parent_guid = $(item).find('a').url().segment(-1);
	elgg.view('thewire/thewire_reply', {
		data: {
			'guid': parent_guid,
		},
		success: function(response) {
			
			//Makeup for jQuery slideDown effect
			var replyDiv = document.createElement('div');
			$(replyDiv).attr('id', 'elgg-reply-div-'+parent_guid);
			$(replyDiv).html(response);

			//A temporary workaround -- Need 'class' for thewire-characters-remaining
			$('#thewire-textarea-reply-'+parent_guid).live('keydown', function() {
				elgg.thewire.textCounter(this, $('#thewire-characters-remaining-'+parent_guid+' span'), 140);
			});
			$('#thewire-textarea-reply-'+parent_guid).live('keydown', function() {
				elgg.thewire.textCounter(this, $('#thewire-characters-remaining-'+parent_guid+' span'), 140);
			});
			
			$(replyDiv).css('display', 'none');
			$(item).closest('.elgg-item').append(replyDiv);

			$(replyDiv).slideDown('fast');
			$(replyDiv).find('textarea').focus();

			//Makes the reply form submit via XHR
			elgg.ajaxify.ajaxForm($(replyDiv).find('form[id^=thewire-form-reply]'), 'create', 'thewire', {'type': 'thewire/reply'});
		},
	});
};

elgg.register_hook_handler('create:success', 'thewire', elgg.ajaxify.thewire.create_success); 
elgg.register_hook_handler('create:submit', 'thewire', elgg.ajaxify.thewire.create_submit); 
elgg.register_hook_handler('create:error', 'thewire', elgg.ajaxify.thewire.create_error); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.thewire.init);
