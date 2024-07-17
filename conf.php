<?php

//ini_set('display_errors', 0);

$ctx->elems->conf->modrewrite = true;

$ctx->elems->conf->mysql = array(
    'host' => '127.0.0.1',
    'port' => '3306',
    'username' => 'causer',
    'password' => 'somepwd',
    'db' => 'ca',
    'prefix' => 'pfx_',
    'charset' => 'utf8'
);

$ctx->elems->conf->custFrag = array(
    'adblock' => ''
);

$ctx->elems->conf->passwordSalt = 'salt#cadabraabra';
$ctx->elems->conf->emailSalt = 'salt#racadabraab';

$ctx->elems->conf->calcPointsSecret = null;

?>
