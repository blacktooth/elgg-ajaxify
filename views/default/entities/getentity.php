<?php
/**
 * View for serving entities via AJAX
 */
if (elgg_is_xhr()) {
	$type = get_input('type', 'object');
	$subtype = get_input('subtype', NULL);
	$guid = get_input('guid');
	$limit =  get_input('limit');
	$pagination = (boolean) get_input('pagination', FALSE);
	$offset = get_input('offset');
	$page_type = get_input('page_type');
	$full_view = get_input('full_view', FALSE);

	switch ($page_type) {
		case 'friends': 
			$entities = list_user_friends_objects(elgg_get_logged_in_user_guid(), $subtype, $limit);
			break;
		case 'owner':
			$owner_guid = elgg_get_logged_in_user_guid();
		default:
			$options = array(
				'type' => $type,
				'subtype' => $subtype,
				'limit' => $limit,
				'guid' => $guid,
				'owner_guid' => $owner_guid,
				'pagination' => $pagination,
				'offset' => $offset,
				'full_view' => $full_view,
			);
			$entities = elgg_list_entities($options);
			break;
	}
	echo $entities;
}
?>
