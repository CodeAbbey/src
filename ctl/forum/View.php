<?php

$url = $ctx->util->paramGet('param');

if (empty($url) || !$ctx->miscService->validUrlParam($url)) {
    $forum = new \stdClass();
    $forum->id = null;
    $forum->info = '';
} else {
    $forum = $ctx->forumsDao->findFirst("url = '$url'");
}

if (!is_object($forum)) {
    $ctx->util->changePage('error404');
    return;
}

$model->topics = $ctx->forumService->loadTopics($forum->id);

foreach ($model->topics as $topic) {
    if ($topic->taskid) {
        $topic->title = "[Task#{$topic->taskid}] {$topic->title}";
    }
}

if (!empty($forum->info)) {
    $forum->info = $ctx->markdown->parse(base64_decode($forum->info));
}

$model->forum = $forum;

$model->otherForums = !empty($url) ? $ctx->forumsDao->find("url != '$url'") : array();

$model->showCreateLink = $ctx->userService->forumAllowed();

$ctx->elems->title = 'Forum';
if (!empty($forum->title)) {
    $ctx->elems->title .= ": {$forum->title}";
}
$ctx->elems->analytics = true;

