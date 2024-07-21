<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$taskid = $ctx->util->paramGet('param');

$model->tags = $ctx->tagsDao->makeLookup('id');

if (!empty($taskid)) {
    $model->task = $ctx->tasksDao->read($taskid);
    $model->taskTags = $ctx->taskService->tagNamesForTask($taskid);
} else {
    $model->task = null;
    $model->taskTags = array();
}

