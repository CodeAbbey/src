<?php

$taskid = $ctx->util->paramGet('param');

$userid = $ctx->auth->loggedUser();

if (!$userid || !is_numeric($taskid)) {
    $ctx->util->changePage('error404');
    return;
}

$data = $ctx->taskService->prepareData($taskid);

$ctx->util->plainOutput($data);
