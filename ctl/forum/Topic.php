<?php

$url = $ctx->util->paramGet('param');

if (empty($url) || !$ctx->miscService->validUrlParam($url)) {
    $ctx->util->changePage('error404');
    return;
}

$topic = $ctx->forumTopicsDao->findFirst("url = '$url'");

if (!is_object($topic)) {
    $ctx->util->changePage('error404');
    return;
}

if ($topic->taskid) {
    $model->forTask = $ctx->tasksDao->read($topic->taskid);
    $restrict = $ctx->forumService->privateTopicRestriction($topic->taskid);
    if ($restrict) {
        $ctx->util->changePage('forum_noaccess');
        $ctx->elems->robots = 'noindex,nofollow';
        $ctx->elems->title = 'Private Forum';
        $ctx->elems->analytics = false;
        return;
    }
}

$model->topic = $topic;
$model->forum = $ctx->forumsDao->read($topic->forumid);
$model->posts = $ctx->forumService->loadPosts($topic);
$model->posts[count($model->posts) - 1]->lastPost = true;

$model->showForm = $ctx->userService->forumAllowed();
if ($model->showForm) {
    $ctx->forumService->resourcesForCodeMirror();
}

$ctx->elems->title = "{$model->forum->title}: {$topic->title}";
$ctx->elems->analytics = true;

