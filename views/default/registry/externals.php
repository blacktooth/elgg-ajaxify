<?php

global $CONFIG;

$js_externals = $CONFIG->externals['js'];
$css_externals = $CONFIG->externals['css'];

echo json_encode(array('js' => $js_externals, 'css' => $css_externals));
