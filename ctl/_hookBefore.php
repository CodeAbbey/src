<?php
date_default_timezone_set('UTC');

$model->httpReferrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
