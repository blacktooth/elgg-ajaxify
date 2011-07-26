elgg.provide('elgg.ajaxify.registration');

elgg.ajaxify.registration.init = function() {
	$('input[name=password]').pstrength({
		'displayMinChar': false,
		'minChar': 6
	});
};

elgg.register_hook_handler('init', 'system', elgg.ajaxify.registration.init);
