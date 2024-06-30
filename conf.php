<?php

//ini_set('display_errors', 0);

$ctx->elems->conf->modrewrite = false;

$ctx->elems->conf->mysql = array(
    'host' => '127.0.0.1',
    'port' => '3306',
    'username' => 'causer',
    'password' => 'somepwd',
    'db' => 'ca',
    'prefix' => '', // needed if the hosting DB prepends every table name with some prefix
    'charset' => 'utf8'
);

$ctx->elems->conf->passwordSalt = 'salt#cadabraabra';
$ctx->elems->conf->emailSalt = 'salt#racadabraab';

?>
