<?php
/**
 * View for serving group entities via AJAX
 */
if (elgg_is_xhr()) {
	$filter = get_input('filter');

	switch ($filter) {
		case 'popular':
			$entities = elgg_list_entities_from_relationship_count(array(
				'type' => 'group',
				'relationship' => 'member',
				'inverse_relationship' => false,
				'full_view' => false,
			));
			if (!$entities) {
				$entities = elgg_echo('groups:none');
			}
			break;
		case 'discussion':
			$entities = elgg_list_entities(array(
				'type' => 'object',
				'subtype' => 'groupforumtopic',
				'order_by' => 'e.last_action desc',
				'limit' => 40,
				'full_view' => false,
			));
			if (!$entities) {
				$entities = elgg_echo('discussion:none');
			}
			break;
		case 'newest':
		default:
			$entities = elgg_list_entities(array(
				'type' => 'group',
				'full_view' => false,
			));
			if (!$entities) {
				$entities = elgg_echo('groups:none');
			}
			break;
	}
	echo $entities;
}
?>
