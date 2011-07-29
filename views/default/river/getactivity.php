<?php
/**
 * AJAX view for dispatching items for Activity river
 *
 *
 */

$options = array();

$type = elgg_extract('type', $vars, 'all');
$subtype = elgg_extract('subtype', $vars, '');
$page_type = elgg_extract('page_type', $vars, '');
$posted_time_lower = (int) elgg_extract('posted_time_lower', $vars, false); 
$posted_time_upper = (int) elgg_extract('posted_time_upper', $vars, false);

if ($subtype) {
	$selector = "type=$type&subtype=$subtype";
} else {
	$selector = "type=$type";
}

if ($type != 'all') {
	$options['type'] = $type;
	if ($subtype) {
		$options['subtype'] = $subtype;
	}
}

if ($posted_time_lower) {
	$options['posted_time_lower'] = $posted_time_lower;
}

if ($posted_time_upper) {
	$options['posted_time_upper'] = $posted_time_upper;
}

switch ($page_type) {
	case 'owner':
		$title = elgg_echo('river:mine');
		$page_filter = 'mine';
		$options['subject_guid'] = elgg_get_logged_in_user_guid();
		break;
	case 'friends':
		$title = elgg_echo('river:friends');
		$page_filter = 'friends';
		$options['relationship_guid'] = elgg_get_logged_in_user_guid();
		$options['relationship'] = 'friend';
		break;
	default:
		$title = elgg_echo('river:all');
		$page_filter = 'all';
		break;
}

$activity = elgg_list_river($options);

echo $activity;
