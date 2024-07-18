<?php

$requestUri = $_SERVER['REQUEST_URI'];

if (strpos($requestUri, 'main') !== false) {
    $ctx->util->redirect(preg_replace('/index.*$/', '', $requestUri));
}

$model->rank = $ctx->userService->topOfWeek();

$taskIds = $ctx->tasksDao->findIds('shown <> 0');
$taskIds = array_slice($taskIds, sizeof($taskIds) - 7);
$taskIds = implode(',', $taskIds);
$model->lastTasks = $taskIds ? array_reverse($ctx->tasksDao->find("id in ($taskIds)")) : [];

$model->lastForum =
    array_slice($ctx->forumService->recentList(), 0, 5);

$ctx->elems->analytics = true;

$model->logged = ($ctx->auth->loggedUser() !== null);
