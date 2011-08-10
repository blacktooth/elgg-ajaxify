elgg.provide('elgg.ajaxify');

/**
 * @fileOverview This file contains helper functions that are used in the rest of this plugin
 * @author <a href='mailto:ravindhranath@gmail.com'>Ravindra Nath Kakarla</a>
 * @version 0.2
 */

/**
 * @namespace Core ajaxify package
 */

elgg.ajaxify = elgg.ajaxify || {}

/**
 * AJAX Loader animation
 */

elgg.ajaxify.ajaxLoader = $('<div class=elgg-ajax-loader></div>');

/**
 * Initialization tasks of ajaxify plugin. 
 */

elgg.ajaxify.init = function() {
	elgg.load_registry();

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
 * Bind form(s) to use jQuery ajaxForm plugin.
 * All the three hooks(submit, success, error) are triggered automatically
 * The value argument to the hook contains all the default arguments that are available
 * @see http://jquery.malsup.com/form/#options-object
 * 
 * @param forms {Object} An array of jQuery form objects 
 * @param action {String} {create|read|update|delete}
 * @param type {String} type argument of hook handler
 * @param params {Object} params to hook handler
 *
 * @return void
 */

elgg.ajaxify.ajaxForm = function(forms, action, type, params) {
	$(forms).each(function() {
		$(this).ajaxForm({
			beforeSubmit: function(arr, formObj, options) {
				elgg.trigger_hook(action+':submit', type, params, {
					'arr': arr,
					'formObj': formObj,
					'options': options
				});
			},
			success: function(responseText, statusText, xhr, formObj) {
				elgg.trigger_hook(action+':success', type, params, {
					'responseText': responseText,
					'statusText': statusText,
					'xhr': xhr,
					'formObj': formObj
				});
			},
			error: function(xhr, reqStatus) {
				elgg.trigger_hook(action+':error', type, params, {
					'reqStatus': reqStatus,
					'xhr': xhr
				});
			}
		});
	});
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
