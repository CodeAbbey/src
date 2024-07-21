<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$title = $ctx->util->paramGet('url');

if (!empty($title)) {
    $model->wikipage = $ctx->wikiDao->findFirst("url = '$title'");
    if (!is_object($model->wikipage)) {
        $ctx->util->changePage('error404');
        return;
    }
    $model->wikipage->data = base64_decode($model->wikipage->data);
} else {
    $model->wikipage = new stdClass();
    $model->wikipage->title = '';
    $model->wikipage->url = '';
    $model->wikipage->data = '';
}

array_push($ctx->elems->styles, 'codemirror');
array_push($ctx->elems->scripts, '_cm/codemirror');
array_push($ctx->elems->scripts, '_cm/markdown');
array_push($ctx->elems->scripts, '_cm/xml');
