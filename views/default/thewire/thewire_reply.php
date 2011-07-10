<?php
/**
 * Wire add form body for replying via xhr
 *
 * @uses $vars['guid']
 * @todo eliminate the need for this file
 */

gatekeeper();
elgg_load_js('elgg.thewire');

$guid = get_input('guid');

$post = get_entity($guid);

$content = elgg_view_form('thewire/reply', array("id" => "thewire-form-reply-$guid", "action" => "action/thewire/add"), array('post' => $post));
$content .= elgg_view('input/urlshortener');
echo "$content<br /><br />";
