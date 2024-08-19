<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$newName = $ctx->util->paramPost('newname');
$url = $ctx->util->paramPost('url');

$newName = trim($newName);
$topic = $ctx->forumService->loadTopicByUrl($url);

if (!is_object($topic) || strlen($newName) < 3) {
    $ctx->util->changePage('error404');
    return;
}

if ($newName[0] != '/') {
    $newName = $ctx->forumService->stripTopicTitle($newName);
    if (!is_object($topic) || strlen($newName) < 5) {
        $ctx->util->flash('Bad title?');
        $ctx->util->redirect('error');
        return;
    }
    $topic->title = $newName;
} else {
    $forumUrl = substr($newName, 1);
    $forum = $ctx->forumService->loadForumByUrl($forumUrl);
    if (!is_object($forum)) {
        $ctx->util->flash('No such forum?');
        $ctx->util->redirect('error');
        return;
    }
    $topic->forumid = $forum->id;
}

$ctx->forumTopicsDao->save($topic);
$ctx->util->redirect(url('forum_topic', 'param', $url));

