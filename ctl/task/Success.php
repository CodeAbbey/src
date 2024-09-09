<?php

if (!$ctx->auth->user()) {
    $ctx->util->changePage('error404');
    return;
}

$inputObj = base64_decode($ctx->util->paramPost('obj'));
$input = json_decode($inputObj);

$model->solved = $input->solved;
$model->gainedPoints = $input->gainedPoints;
$model->userPoints = $input->userPoints;
$model->task = $input->task;
$taskid = $model->task->id;

$userid = $ctx->auth->loggedUser();
$userData = $ctx->userDataDao->findFirst("userid = $userid");

$rndPrimes = array(13, 17, 19, 23, 29, 31, 37);
$model->userRnd = $rndPrimes[$userid % count($rndPrimes)];
$model->numSolved = $userData->solved;


$model->challengeResult = $input->challengeResult ?? null;

$model->notes = $ctx->taskService->loadNotes($taskid);

$model->nextTasks = $ctx->tasksDao->find(
    "solved < {$model->task->solved} order by solved desc", 5);
if (count($model->nextTasks) > 3) {
    $model->nextTasks = array_slice($model->nextTasks, rand(0, 2), 3);
}
$recom = $ctx->miscService->getTaggedValue("recom-$taskid");
if ($recom) {
    $recom = str_replace(' ', ',', $recom);
    $recomTasks = $ctx->tasksDao->find("id in ($recom)");
    array_splice($model->nextTasks, 0, 0, $recomTasks);
}

$ctx->elems->scripts[] = 'task/attempt';
