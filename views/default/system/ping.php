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
				case 'view':
					//view, {name: 'viewname', vars: {var1: 'value1', ...}}
					if (isset($request[1]['name'])) {
						if (!isset($request[1]['vars'])) {
							$request[1]['vars'] = array();
						}
						$__elgg_client_results[$request_id] = elgg_view($request[1]['name'], $request[1]['vars']);
					}
					break;
			}
		}
		echo json_encode($__elgg_client_results);
	}
}
