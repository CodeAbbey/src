<?php

$taskurl = $ctx->util->paramGet('param');
$language = $ctx->util->paramGet('lang');

if (!$ctx->miscService->validUrlParam($taskurl)
    || (!empty($language) && !array_key_exists($language, $ctx->elems->conf->languages))) {
    $ctx->util->changePage('error404');
    return;
}

$task = $ctx->tasksDao->findFirst("url = '$taskurl'");

if (!is_object($task)) {
    $ctx->util->flash($taskurl);
    $ctx->util->changePage('error404');
    return;
}

if ($ctx->challengeService->challengeExists($task->id)) {
    $ctx->util->redirect(url('task_chlng_stats', 'param', $task->url));
    return;
}

$criteria = "solved = 1 and taskid = {$task->id}";
if (!empty($language)) {
    $criteria .= " and language = '$language'";
}
$ids = $ctx->userTasksDao->solvers($criteria, !$ctx->util->paramGet('withblanks'));
shuffle($ids);
if (count($ids) > 15) {
    $limit = $ctx->util->paramGet('limit');
    $ids = array_slice($ids, 0, !!$limit ? min(100, intval($limit)) : 15);
}

$res = array();
foreach ($ids as $utid) {
    $ut = $ctx->userTasksDao->read($utid);
    $user = $ctx->usersDao->read($ut->userid);
    $entry = new stdClass();
    $entry->url = $user->url;
    $entry->username = $user->username;
    $entry->language = $ut->language;
    $res[] = $entry;
}

$model->task = $task;
$model->users = $res;

$loadNotes = true;
if (!$ctx->taskService->isAdminOrAuthor($task)) {
    if (!$ctx->auth->loggedUser()) {
        $loadNotes = false;
    } else {
        $usertask = $ctx->userTasksDao->findFirst("solved = 1 and taskid = {$task->id} and userid = " . $ctx->auth->loggedUser());
        $loadNotes = is_object($usertask);
    }
}

if ($loadNotes) {
    $model->notes = $model->notes = $ctx->taskService->loadNotes($task->id);
} else {
    $model->notes = 'You should solve the problem to see these hints!';
}

$model->language = $language;
$model->languages = $ctx->langService->languagesArray();

$ctx->elems->robots = 'noindex,nofollow';
$ctx->elems->title = 'Solution list';
$ctx->elems->analytics = true;

