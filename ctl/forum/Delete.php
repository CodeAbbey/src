<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$url = $ctx->util->paramGet('param');

$topic = $ctx->forumService->loadTopicByUrl($url);
if (!is_object($topic)) {
    $ctx->util->changePage('error404');
    return;
}

$posts = $ctx->forumPostsDao->find("topicid = {$topic->id}");

$ctx->forumTopicsDao->delete($topic->id);

foreach ($posts as $p) {
    $ctx->forumPostsDao->delete($p->id);
}

$ctx->util->flash('Topic deleted');
$ctx->util->redirect('forum_view');
