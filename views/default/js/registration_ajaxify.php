<script type='text/javascript'>
elgg.provide('elgg.ajaxify.registration');

elgg.ajaxify.registration.init = function() {
	$('.elgg-form-account input[name=password]').pstrength({
		'displayMinChar': false,
		'minChar': 6
	});
	
	$('.elgg-form-account input[name=username]').live('blur', function() {
		var usernameDOM = $(this);
		$(usernameDOM).after(elgg.ajaxify.ajaxLoader);
		elgg.view('users/checkuser', {
			dataType: 'json',
			data: {
				q_username: $(usernameDOM).val()
			},
			success: function(response) {
				elgg.ajaxify.ajaxLoader.remove();
				if (response.user) {
					$(usernameDOM).after("<span class='elgg-message elgg-state-error'>"+ elgg.echo("username:notavailable") +"</span>");
				} else {
					$(usernameDOM).after("<span class='elgg-message elgg-state-success'>"+ elgg.echo("username:available") +"</span>");
				}
			}
		});
	});

	$('.elgg-form-account input[name=username]').live('focus', function() {
		$(this).next('.elgg-message').remove();
	});
};

elgg.register_hook_handler('init', 'system', elgg.ajaxify.registration.init);
</script>
