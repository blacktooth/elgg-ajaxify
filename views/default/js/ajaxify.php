elgg.provide('elgg.ajaxify');

elgg.ajaxify.init = function() {
	elgg.ajaxify.ajaxLoader = $('<div class=elgg-ajax-loader></div>');

	$('.elgg-menu-item-delete').live('click', function(event) {
		elgg.ajaxify.delete_entity(elgg.ajaxify.getGUIDFromMenuItem(this));
		event.preventDefault();
	});
	//Default actions that have to be invoked after a successful AJAX request
	$(document).ajaxSuccess(function(event, xhr, options) {
		//Check for any system messages
		try {
			var response = jQuery.parseJSON(xhr.responseText);
		} catch(JSONException) {
			console.log('Not a JSON response');
		}
		if (response && response.system_messages) {
			elgg.register_error(response.system_messages.error);
			elgg.system_message(response.system_messages.success);
		}
	});
};

/**
 * Fetch a view via AJAX
 *
 * @example Usage:
 * Use it to fetch a view using /ajax/view
 * can also be used to refresh a view
 * elgg.view('likes/display', {data: {guid: GUID}, target: targetDOM})
 * @param {string} name Viewname
 * @param {Object} options Parameters to the view along with jQuery options {@see jQuery#ajax}
 * @return {void}
 */

elgg.view = function(name, options) {
	elgg.assertTypeOf('string', name);
	//Check to see if its already a normalized url
	if (new RegExp("^(https?://)", "i").test(name)) {
		name = name.split(elgg.config.wwwroot)[1];
	}
	var url = elgg.normalize_url('ajax/view/'+name);
	if (elgg.isNullOrUndefined(options.success)) {
		options.manipulationMethod = options.manipulationMethod || 'html';
		options.success = function(data) {
			$(options.target)[options.manipulationMethod](data);
		}
	}
	elgg.get(url, options);
};

/**
 * Delete an entity
 *
 * @param guid The guid of the entity we want to delete
 * @return {XMLHttpRequest}
 */

elgg.ajaxify.delete_entity = function(guid) {
	guid = parseInt(guid);
	if (guid < 1) {
		return false;
	}
	$('#elgg-object-'+guid).slideUp();
	return elgg.action('entity/delete', {guid: guid});
};

/**
 * Get URL from ElggMenuItem 
 *
 * @param item {Object} List item 
 * @return URL {String}
 */

elgg.ajaxify.getURLFromMenuItem = function(item) {
	var actionURL = $(item).find('a').attr('href');
	return actionURL;
};

/**
 * Parse guid from ElggMenuItem 
 *
 * @param item {Object} List item 
 * @return guid {String}
 */

elgg.ajaxify.getGUIDFromMenuItem = function(item) {
	return elgg.ajaxify.getURLFromMenuItem(item).match(/guid=(\d+)/)[1];
};

/**
 * Parse view name from the current URL of the page 
 *
 * @param value {String} Value to return if no name is available
 * @return viewname {String}
 */

elgg.ajaxify.getViewFromURL = function(value) {
	elgg.assertTypeOf('string', value);
	var viewname = '';
	//Parse the URL to get the viewname
	try {
		viewname = new RegExp(elgg.config.wwwroot+'(.+)').exec(window.location.toString())[1];
		//Strip off any parameters
		viewname = viewname.split('?')[0];
	} catch(exception) {
		viewname = value;
	}
	return viewname;
};

/** 
* Replace parts of the given attribute of a DOM
*
* @param attribute {String} attribute of the DOM to change
* @param match {String} pattern to match
* @param replace {String} string to replace with
* @return {Object}
*/

jQuery.fn.replaceAttr = function(attribute, match, replace) {
    return this.attr(
        attribute,
        function() {
            return jQuery(this).attr(attribute).replace(match, replace);
        }
    );
};

elgg.register_hook_handler('init', 'system', elgg.ajaxify.init);
