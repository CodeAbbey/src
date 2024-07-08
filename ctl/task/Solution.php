<?php

$taskurl = $ctx->util->paramGet('task');
$userurl = $ctx->util->paramGet('user');
$language = $ctx->util->paramGet('lang');
$key = $ctx->util->paramGet('key');

$langArray = $ctx->langService->languagesArray();

if (!$ctx->miscService->validUrlParams($taskurl, $userurl) || !in_array($language, $langArray)) {
    $ctx->util->changePage('error404');
    return;
}

$currentUserId = $ctx->auth->loggedUser();
 
if (!$currentUserId && !$key) {
    $ctx->util->changePage('message');
    $model->msg = "Excuse us, but you need to be logged in and to solve the task to view other's solution!";
    return;
}

$ts = $key ? substr($key, 24) : base_convert(time(), 10, 16);
$h = substr(hash('sha256', "$ts+$taskurl+$userurl+$language"), 0, 24);
if ($key) {
    $tsd = base_convert($ts, 16, 10);
    if ($h . $ts != $key || $tsd < time() - 86400*7) {
        $ctx->util->changePage('message');
        $model->msg = "Link is either broken or expired...";
        return;
    }
}


$task = $ctx->tasksDao->findFirst("url = '$taskurl'");
$user = $ctx->usersDao->findFirst("url = '$userurl'");

if (!is_object($task) || !is_object($user)) {
    $ctx->util->changePage('error404');
    return;
}

$model->key = ($currentUserId == $user->id) ? $h . $ts : '';

if ($ctx->util->paramGet('plain') && $currentUserId == $user->id) {
    try {
        list($usertask, $solution) = $ctx->taskService->loadSolution($task->id, $user->id, $language);
        $solution = $solution->solution;
    } catch (\Exception $e) {
        $solution = 'Code loading failed :(';
    }
    $ctx->util->plainOutput($solution);
    return;
}

if ($ctx->challengeService->challengeExists($task->id)
        && ($currentUserId != $user->id && !$ctx->auth->admin())) {
    $ctx->util->redirect(url('task_chlng_stats', 'param', $task->url));
    $ctx->util->flash("You can't see solutions for Challenges!");
    return;
}

$model->solution = $ctx->taskService->viewSolution($task, $user, $language);

$model->commentAllowed = ($currentUserId != $user->id);
if ($model->solution->error || !$ctx->userService->commentingAllowed()
        || $ctx->commentService->commentExists('sol', $model->solution->userTaskId, $currentUserId)) {
    $model->commentAllowed = false;
}

$model->replyAllowed = ($currentUserId === $user->id);
$model->changeLangAllowed = ($currentUserId === $user->id || $ctx->auth->admin());

$model->highlight = (!empty($model->solution->language) && $model->solution->language != 'other')
        ? strtolower($model->solution->language) : '';

if ($model->changeLangAllowed && !$model->solution->error) {
    $model->language = strtolower($model->solution->language);
    $model->languages = $ctx->langService->languagesArray();
}

if (!$model->solution->error) {
    $userTaskId = $model->solution->userTaskId;
    $model->comments = $ctx->commentService->listComments('sol', $userTaskId);
    if ($currentUserId != $user->id && !$key) {
        $model->codelike = $ctx->codelikesDao->getCount("userid = $currentUserId and codeid = $userTaskId");
    } else {
        $model->codelike = 1;
    }
} else {
    $model->comments = array();
    $model->codelike = 0;
}

$ctx->elems->styles[] = 'jqui';
$ctx->elems->styles[] = 'highlight';
$ctx->elems->scripts[] = 'jqui';
$ctx->elems->scripts[] = 'highlight';

$ctx->elems->robots = 'noindex,follow';
$ctx->elems->title = 'View solution';
$ctx->elems->analytics = true;

