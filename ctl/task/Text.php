<?php

if (!$ctx->auth->loggedUser()) {
    $ctx->util->changePage('error404');
    return;
}

$url = $ctx->util->paramGet('param');
if (!$ctx->miscService->validUrlParam($url)) {
    $ctx->util->changePage('error404');
    return;
}

$task = $ctx->tasksDao->findFirst("url = '$url'");
if (!is_object($task)) {
    $ctx->util->changePage('error404');
    return;
}

$taskdata = $ctx->taskDataDao->findFirst("taskid = {$task->id} and type = 'text'");

$ctx->util->plainOutput(base64_decode($taskdata->data));
