/**
 * Experimental ajaxy features/API!
 */

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
 * Deletes an annotation
 *
 * @param id The annotation's id
 * @return {XMLHttpRequest}
 */
elgg.delete_annotation = function(id) {
	if (id < 1 || !confirm(elgg.echo('delete:confirm'))) {
		return false;
	}

	$('.annotation[data-id='+id+']').slideUp();

	return elgg.action('ajax/annotation/delete', {annotation_id: id});
};

/**
 * Delete an entity
 *
 * @param guid The guid of the entity we want to delete
 * @return {XMLHttpRequest}
 */
elgg.delete_entity = function(guid) {
	if (guid < 1 || !confirm(elgg.echo('deleteconfirm'))) {
		return false;
	}

	$('.entity[data-guid='+guid+']').slideUp();

	return elgg.action('entity/delete', {guid: guid});
};

/**
 * Make an API call
 *
 * @example Usage:
 * <pre>
 * elgg.api('system.api.list', {
 *     success: function(data) {
 *         console.log(data);
 *     }
 * });
 * </pre>
 *
 * @param {String} method The API method to be called
 * @param {Object} options {@see jQuery#ajax}
 * @return {XmlHttpRequest}
 */
elgg.api = function(method, options) {
	if (!method) {
		throw new TypeError("method must be specified");
	} else if (typeof method != 'string') {
		throw new TypeError("method must be a string");
	}

	var defaults = {
		dataType: 'json',
		data: {}
	};

	options = elgg.ajax.handleOptions(method, options);
	options = $.extend(defaults, options);

	options.url = 'services/api/rest/' + options.dataType + '/';
	options.data.method = method;

	return elgg.ajax(options);
};

/**
 * @param {string} selector a jQuery selector
 * @param {Function} complete A function to execute when the refresh is done
 * @return {XMLHttpRequest}
 */
elgg.refresh = function(selector, complete) {
	$(selector).html('<div align="center" class="ajax_loader"></div>');
	return $(selector).load(location.href + ' ' + selector + ' > *', complete);
};

/**
 * @param {string} selector a jQuery selector (usually an #id)
 * @param {number} interval The refresh interval in seconds
 * @param {Function} complete A function to execute when the refresh is done
 * @return {number} The interval identifier
 */
elgg.feed = function(selector, interval, complete) {
	return setInterval(function() {
		elgg.refresh(selector, complete);
	}, interval);
};
