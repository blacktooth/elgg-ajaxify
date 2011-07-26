<?php

if (elgg_is_xhr()) {
	$username = get_input("q_username");
	if (get_user_by_username($username)) {
		$response = TRUE;
	} else {
		$response = FALSE;
	}
	echo json_encode(array("user" => $response));
}
?>
