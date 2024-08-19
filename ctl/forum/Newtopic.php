<?php

$url = $ctx->util->paramGet('param');

if (empty($url) || !$ctx->miscService->validUrlParam($url)) {
    $url = 'general';
}

$forum = $ctx->forumsDao->findFirst("url = '$url'");

if (!is_object($forum)) {
    $ctx->util->changePage('error404');
    return;
}

$model->forum = $forum;
$model->newTopic = true;

$ctx->forumService->resourcesForCodeMirror();

$ctx->elems->robots = 'noindex,nofollow';
$ctx->elems->title = 'Create forum topic';
$ctx->elems->analytics = false;

