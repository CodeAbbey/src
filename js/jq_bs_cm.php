<?php
header('Content-Type: application/javascript');
echo file_get_contents('jq.js') . "\n\n";
echo file_get_contents('bootstrap.min.js') . "\n\n";
echo file_get_contents('common.js') . "\n\n";
