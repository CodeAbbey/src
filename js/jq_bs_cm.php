<?php
header('Content-Type: application/javascript');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
echo file_get_contents('jq.js') . "\n\n";
echo file_get_contents('bootstrap.min.js') . "\n\n";
echo file_get_contents('common.js') . "\n\n";
