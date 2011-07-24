elgg.provide('elgg.ajaxify.search');

elgg.ajaxify.search.init = function() {
	elgg.ajaxify.search.fetch_limit = 10;
	elgg.ajaxify.search.context = elgg.ajaxify.getViewFromURL('all').split('/')[0];
	elgg.ajaxify.search.strip_desc = 40;

	$('#elgg-search-autocomplete').autocomplete({
		minLength: 2,
		html: true,
		max: elgg.ajaxify.search.fetch_limit,
		source: function(req, res) {
			elgg.get('livesearch', {
				cache: false,
				data: {
					'term': req.term,
					'match_on': elgg.ajaxify.search.context
				},
				dataType: 'json',
				success: function(response) {
					res($.map(response, function(result) {
						return {
							label: function() { 
								return elgg.trigger_hook('update:submit', 'search', {'type': elgg.ajaxify.search.context}, {
									result: result
								});
							},
							attributes: result
						}
					}));
				}
			});
		},
		select: function(event, ui) {
			elgg.trigger_hook('read:submit', 'search', {'type': elgg.ajaxify.search.context}, {
				ui: ui
			});
			return false;
		}
	});
};

//Handler for modifying the markup of results that are shown in the search popup
elgg.ajaxify.search.update_submit = function(hook, type, params, value) {
	var guid = value.result.guid;
	var name = value.result.name;
	var desc = value.result.desc;
	var icon = value.result.icon;
	var entity_type = value.result.type;
	var user_icon = value.result.user_icon;
	var user_name = value.result.user_name;
	var label = '';
	switch (params.type) {
		case 'blog': 
		case 'pages':
			label = user_icon+"<h4>"+name+"</h4>"+desc.substr(0, elgg.ajaxify.search.strip_desc)+"...";
			break;
		case 'thewire':
			label = user_icon+" "+user_name+"<h6>"+desc.substr(0, elgg.ajaxify.search.strip_desc)+"...</h6>";
			break;
		case 'file':
			label = icon+"<h4>"+name+"</h4>";
			break;
		case 'bookmarks':
			label = user_icon+"<h4>"+name+"</h4>";
			break;
		case 'friends':
		case 'members':
			label = icon+"<h4>"+name+"</h4>"
			break;
		case 'groups':
			label = icon+" "+name+"<h6>"+desc.substr(0, elgg.ajaxify.search.strip_desc)+"...</h6>";
			break;
		case 'messages':
			label = user_icon+" <h6>"+name;
			break;
		case 'all':
			switch (entity_type) {
				case 'user': 
					label = icon+"<h4>"+name+"</h4>"
					break;
				case 'group':
					label = icon+" "+name+"<h6>"+desc.substr(0, elgg.ajaxify.search.strip_desc)+"...</h6>";
					break;
			}	
			break;
		default:
			label = icon+" "+name;
	}
	return label;
};

//Handler for specifying forward url to which the user has to be redirected upon clicking a result
elgg.ajaxify.search.read_submit = function(hook, type, params, value) {
	var forward_url = '';
	var guid = value.ui.item.attributes.guid;
	var name = value.ui.item.attributes.name;
	var desc = value.ui.item.attributes.desc;
	var entity_type = value.ui.item.attributes.type;
	switch (params.type) {
		case 'blog':
		case 'pages':
		case 'file':
		case 'bookmarks':
			forward_url = params.type+'/view/'+guid;
			break;
		case 'thewire':
			forward_url = params.type+'/thread/'+guid;
			break;
		case 'friends':
		case 'members': 
			forward_url = '/profile/'+desc;
			break;
		case 'groups':
			forward_url = params.type+'/profile/'+guid;
			break;
		case 'messages':
			forward_url = params.type+'/read/'+guid;
			break;
		case 'all':
			switch (entity_type) {
				case 'user': 
					forward_url = '/profile/'+desc;
					break;
				case 'group':
					forward_url = 'groups/profile/'+guid;
					break;
			}	
			break;
		default:
			forward_url = 'search?q='+name;
	}
	elgg.forward(elgg.normalize_url(forward_url));
};

elgg.register_hook_handler('update:submit', 'search', elgg.ajaxify.search.update_submit); 
elgg.register_hook_handler('read:submit', 'search', elgg.ajaxify.search.read_submit); 
elgg.register_hook_handler('init', 'system', elgg.ajaxify.search.init);
