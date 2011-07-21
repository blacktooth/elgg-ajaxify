<?php
/**
 * Autofill view for fetching title and description of an external webpage
 *
 */

if (elgg_is_xhr()) {


	$uri = get_input('uri');

	if ($uri && !preg_match("#^((ht|f)tps?:)?//#i", $uri)) {
		$uri = "http://$uri";
	}

	if ($uri && filter_var($uri, FILTER_VALIDATE_URL)) {
		// Close connection immediately
		$context = stream_context_create(array('http' => array('header' => 'Connection: close')));
		$page = file_get_contents($uri, false, $context);
		if ($page) {
			$temp = tempnam();
			$meta_tags = get_meta_tags($temp);
			if (preg_match("#<title>(.*)</title>.*#", $page, $title)) {
				$page_title = $title[1];
			}
			$page_description = $meta_tags['description'];
			$output = array(
				'title' => $page_title,
				'description' => $page_description,
			);
			echo json_encode($output);
		} 
	}
}
