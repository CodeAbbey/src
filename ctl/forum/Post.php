<?php

$url = $ctx->util->paramPost('url');
$topicTitle = $ctx->util->paramPost('topictitle');
$text = $ctx->util->paramPost('text');
$taskId = $ctx->util->paramPost('task');
$userId = $ctx->auth->loggedUser();

if ((empty($url) && empty($topicTitle)) || empty($text) || strlen($text) < 20 || strlen($text) > 5000) {
    $ctx->util->changePage('message');
    $model->msg = 'Message post should have from 20 to 5000 characters length!';
    return;
}

if (!$ctx->miscService->validUrlParam($url) || !$userId || !$ctx->userService->forumAllowed()) {
    $ctx->util->redirect('error');
    return;
}

if (!empty($topicTitle)) {
    $topicTitle = $ctx->forumService->stripTopicTitle($topicTitle);
    if (strlen($topicTitle) < 10 || strlen($topicTitle) > 100) {
        $ctx->util->changePage('message');
        $model->msg = 'Topic title should have from 10 to 100 characters length!';
        return;
    }
    if (!empty($taskId)) {
        if (!preg_match('/\d+/', $taskId)) {
            $ctx->util->changePage('message');
            $model->msg = 'Task number for private forum seems to be incorrect!';
            return;
        }
        $taskId = intval($taskId);
        if (!$ctx->auth->admin() && !$ctx->userService->haveSolved($userId, $taskId)) {
            $ctx->util->changePage('message');
            $model->msg = 'You can not create private forum for task which you have not solved yet!';
            return;
        }
    } else {
        $taskId = 0;
    }

    $forum = $ctx->forumsDao->findFirst("url = '$url'");
    if (is_object($forum)) {
        $topic = new \stdClass();
        $topic->forumid = $forum->id;
        $topic->userid = $userId;
        $topic->taskId = $taskId;
        $topic->title = $topicTitle;
        $topic->url = md5($topicTitle . $forum->title . rand());
        $topic->created = $ctx->miscService->curTimeStr();
        $url = $topic->url;
        $ctx->forumTopicsDao->save($topic);
    } else {
        $url = '';
    }
}

$topic = $ctx->forumTopicsDao->findFirst("url = '$url'");

if (!is_object($topic)) {
    $ctx->util->changePage('error404');
    return;
}

$post = new \stdClass();
$post->userid = $userId;
$post->topicid = $topic->id;
$post->post = base64_encode($text);
$post->created = $ctx->miscService->curTimeStr();
$postId = $ctx->forumPostsDao->save($post);

$topic->posts += 1;
$topic->lastpost = $ctx->miscService->curTimeStr();
$topic->lastpostid = $postId;
$ctx->forumTopicsDao->save($topic);

$ctx->util->redirect(url('forum_topic', 'param', $url));

