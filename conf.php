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

$ctx->elems->conf->projectName = 'CodeAbbey';
$ctx->elems->conf->title = "{$ctx->elems->conf->projectName} - programming problems";
$ctx->elems->conf->descr = 'Collection of free programming puzzles';
$ctx->elems->conf->descrSuffix = 'Programming problems for beginners';
$ctx->elems->conf->author = 'Rodion Gorkovenko';
$ctx->elems->conf->mainImage = aurl('img/facade.gif');
$ctx->elems->conf->copyright = '&copy; 2013 - ' . date('Y') . ', Rodion Gorkovenko';

$ctx->elems->conf->custFrag = array(
    'adblock' => ''
);

$ctx->elems->conf->passwordSalt = 'salt#cadabraabra';
$ctx->elems->conf->emailSalt = 'salt#racadabraab';

$ctx->elems->conf->calcPointsSecret = null;

?>
