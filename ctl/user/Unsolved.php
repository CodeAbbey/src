<?php

$url = $ctx->util->paramGet('param');
$user = $ctx->userService->byUrl($url);
if (!is_object($user)) {
    $ctx->util->changePage('error404');
    return;
}

$model->userName = $user->username;
$model->userUrl = $user->url;

$userTasks = $ctx->userTasksDao->find("userid = {$user->id} and solved <= 0");
if (!empty($userTasks)) {
    $taskIds = $ctx->miscService->listIds($userTasks, 'taskid');
    $tasks = $ctx->tasksDao->makeLookup('id', "id in ($taskIds)");
}

$neverSolved = $ctx->userTasksDao->neverSolvedIds($user->id);

$model->entries = array();

foreach ($userTasks as $entry) {
    $task = $tasks[$entry->taskid];
    $entry->title = $task->title;
    $entry->taskUrl = url('task_view', 'param', $task->url);
    $entry->url = url('task_solution', 'user', $user->url, 'task', $task->url, 'lang', urlencode($entry->language));
    $entry->ts = $ctx->miscService->formatDate($entry->ts);
    $entry->neverSolved = in_array($entry->taskid, $neverSolved);
}

usort($userTasks, function($a, $b) {
    return $a->taskid - $b->taskid;
});

$model->records = $userTasks;

$ctx->elems->robots = 'noindex,nofollow';
$ctx->elems->title = $user->username . ' - unsuccessful solutions';
$ctx->elems->analytics = true;
