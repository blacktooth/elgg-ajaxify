<?php
/**
 * Autofill view for fetching title and metadata of an external webpage
 *
 */

if (elgg_is_xhr()) {


	$uri = get_input('uri');

	if ($uri && !preg_match("#^((ht|f)tps?:)?//#i", $uri)) {
		$uri = "http://$uri";
	}

	if ($uri && filter_var($uri, FILTER_VALIDATE_URL)) {
		//Close connection immediately after fetching the page
		$context = stream_context_create(array('http' => array('header' => 'Connection: close')));
		$page = file_get_contents($uri, false, $context);
		if ($page) {
			
			preg_match("#<title>([^>]*)</title>#is", $page, $title);
			$page_title = $title[1];

			//Parse name attributes of meta tags into $meta_tags[1] and corresponding contents into $meta_tags[2]
			preg_match_all("#<meta[^>]+name=\"([^\"]*)\"[^>]+content=\"([^\"]*)\"[^>]+>#is", $page, $meta_tags, PREG_PATTERN_ORDER);

			$index = 0;
			foreach ($meta_tags[1] as $metadata) {
				$meta[strtolower($metadata)] = $meta_tags[2][$index++];
			}
			$output = array(
				'title' => $page_title,
				'description' => $meta['description'],
				'keywords' => $meta['keywords'],
			);
			echo json_encode($output);
		} 
	}
}
