<?php

$url = $ctx->util->paramGet('param');

if (!$url) {
    $ctx->util->changePage('wiki/index');
    $model->pages = $ctx->wikiDao->find();
    $ctx->elems->title = 'Wiki: index';
    $model->lastmod = '2014-01-01 00:00:00';
} else {
    $url = $ctx->strUtils->nameToUrl($url);
    $wikipage = $ctx->wikiDao->findFirst("url = '$url'");

    if (!is_object($wikipage)) {
        $ctx->util->changePage('error404');
        return;
    }

    $model->title = $wikipage->title;
    $model->url = $wikipage->url;
    $model->text = $ctx->markDown->parse(base64_decode($wikipage->data));
    $model->lastmod = $wikipage->lastmod;

    $ctx->elems->title = 'Wiki: ' . $wikipage->title;
    if (strpos($model->text, '<!--noindex-->') !== false) {
        $ctx->elems->robots = 'noindex,nofollow';
    }
}

$ctx->miscService->headerLastModified($model->lastmod);

$ctx->elems->styles[] = 'jsmonoterm';
$ctx->elems->scripts[] = 'jsmonoterm';
$ctx->elems->analytics = true;


