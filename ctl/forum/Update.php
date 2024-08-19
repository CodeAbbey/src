<?php

$id = $ctx->util->paramPost('url');
$text = $ctx->util->paramPost('text');

if (!empty($id) && !empty($text) && preg_match('/[0-9A-F]{32}/i', $id)) {
    $id = $ctx->miscService->decryptInt($id);
    if ($id !== null) {
        $post = $ctx->forumPostsDao->read($id);
    }
}

if (empty($post)) {
    $ctx->util->changePage('error404');
    return;
}

$post->post = base64_encode($text);
$ctx->forumPostsDao->save($post);

$topic = $ctx->forumTopicsDao->read($post->topicid);

$ctx->util->redirect(url('forum_topic', 'param', $topic->url));

