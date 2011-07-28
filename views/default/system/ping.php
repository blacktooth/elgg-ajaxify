<?php
/*
 * Responds to ping requests from js/refresh_ajaxify
 *
 *
 */
if (elgg_is_xhr()) {
	$__elgg_client_requests = get_input('__elgg_client_requests', false);
	$__elgg_client_results = array();

	if ($__elgg_client_requests) {
		foreach ($__elgg_client_requests as $request_id => $request) {
			switch ($request[0]) {
				case 'securitytokens':
					$ts = time();
					$token = generate_action_token($ts);
					$__elgg_client_results[$request_id] = array('__elgg_ts' => $ts, '__elgg_token' => $token);
					break;
			}
		}
		echo json_encode($__elgg_client_results);
	}
}
