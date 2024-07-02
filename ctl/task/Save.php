<?php

if (!$ctx->auth->admin()) {
    $ctx->util->redirect('task_list');
    return;
}

$taskid = $ctx->util->paramPost('id');

$task = null;
if (is_numeric($taskid)) {
    $task = $ctx->tasksDao->read($taskid);
}

$justCreated = false;
if (!is_object($task)) {
    $taskid = null;
    $task = new stdClass();
    $justCreated = true;
}

$task->id = $taskid;
$task->title = $ctx->util->paramPost('title');
$task->url = $ctx->util->paramPost('url');
$task->author = $ctx->util->paramPost('author');
$task->lastmod = date('Y-m-d');
$task->shown = $ctx->util->paramPost('shown') ? 1 : 0;
$statement = $ctx->util->paramPost('statement');
$statement = $ctx->util->sillyDecode($statement);
$checker = $ctx->util->paramPost('checker');
$checker = $ctx->util->sillyDecode($checker);

if (!$task->id) {
    unset($task->id);
}
if (!$task->author) {
    unset($task->author);
}
if (!$task->volumeid) {
    unset($task->volumeid);
}

if (!$task->title || !$statement || !$checker) {
    $ctx->util->changePage('message');
    $model->msg = 'Insufficient data';
    return;
}

$taskid = $ctx->tasksDao->save($task);

$data = $ctx->taskDataDao->findFirst("taskid = $taskid and type = 'text'");
if (!is_object($data)) {
    $data = new stdClass();
    $data->taskid = $taskid;
    $data->type = 'text';
}
$data->data = base64_encode($statement);
$ctx->taskDataDao->save($data);

$data = $ctx->taskDataDao->findFirst("taskid = $taskid and type = 'checker'");
if (!is_object($data)) {
    $data = new stdClass();
    $data->taskid = $taskid;
    $data->type = 'checker';
}
$data->data = base64_encode($checker);
$ctx->taskDataDao->save($data);

if ($justCreated) {
    $tagObject = $ctx->tagsDao->findFirst("title = 'unlabeled'");
    $assignment = new \stdClass();
    $assignment->tagid = $tagObject->id;
    $assignment->taskid = $taskid;
    $ctx->taskTagsDao->save($assignment);
}

$ctx->util->redirect(url('task_view', 'param', $task->url));
