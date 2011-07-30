<?php

if (elgg_is_logged_in()) {
	$num_messages = (int) messages_count_unread();
	if ($num_messages != 0) {
		echo "<span class=\"messages-new\">$num_messages</span>";
	}
}

?>
