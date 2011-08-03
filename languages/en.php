<?php
/**
 * ajaxify English language file
 */

$english = array(
	"entity:delete:success" => "The %s post has been deleted successfully!",
	"entity:delete:error" => "There is some error while deleting your %s post!",
	"username:available" => "Available",
	"username:notavailable" => "Not Available",
	"time:minutes" => "minutes",
	"time:seconds" => "seconds",
	"ping:error" => "Oops...Error contacting ". elgg_get_site_url() .". Please check your Internet connection! Next attempt in %d %s!",
	"ping:success" => "We are back!",
);

add_translation('en', $english);
