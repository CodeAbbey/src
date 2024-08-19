<?php

$url = $ctx->util->paramGet('param');

if (!empty($url) && preg_match('/[0-9A-F]{32}/i', $url)) {
    $id = $ctx->miscService->decryptInt($url);
    if ($id !== null) {
        $post = $ctx->forumPostsDao->read($id);
    }
}

if (empty($post)) {
    $ctx->util->changePage('error404');
    return;
}

$model->postText = base64_decode($post->post);
$model->url = $url;

$ctx->forumService->resourcesForCodeMirror();

$ctx->elems->robots = 'noindex,nofollow';
$ctx->elems->title = 'Editing forum post';
$ctx->elems->analytics = false;

