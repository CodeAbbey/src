<?php
header('Content-Type: text/css');
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
echo file_get_contents('bootstrap.min.css') . "\n\n";
echo file_get_contents('bootstrap-theme.min.css') . "\n\n";
echo file_get_contents('common_bs.css') . "\n\n";
