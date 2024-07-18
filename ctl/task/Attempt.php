<?php

if (!$ctx->auth->user()) {
    $ctx->util->changePage('error404');
    return;
}


$taskid = $ctx->util->paramPost('taskid');
$answer = $ctx->util->paramPost('answer');
$solution = $ctx->util->paramPost('solution');
$lang = $ctx->util->paramPost('lang');
if ($ctx->util->paramPost('b64enc') == '1') {
    if ($solution[0] == '-') {
        $solution = strrev(substr($solution, 1));
    }
    $solution = str_replace(array('.','_','-'), array('+','/','='), $solution);
    $solution = base64_decode($solution);
}

if (!is_numeric($taskid) || !$solution || !$answer) {
    $ctx->util->changePage('message');
    $model->msg = 'Insufficient data A';
    return;
}

$lastSolved = $ctx->util->sessionGet('lastSolved');
if ($lastSolved) {
    $ctx->util->sessionDel('lastSolved');
    $last = array_map('intval', explode(' ', $lastSolved));
    $secRem = ($last[0] == $taskid ? $last[2] : max($last[2], $last[1])) - time();
    if ($secRem > 0) {
        $ctx->util->changePage('message');
        $model->msg = "You could not submit new tasks for $secRem seconds more";
        return;
    }
}

$languages = $ctx->langService->languagesArray();
$lang = isset($languages[$lang]) ? $languages[$lang] : "";

$task = $ctx->tasksDao->findFirst("id = $taskid");

if (!is_object($task)) {
    $ctx->util->changePage('message');
    $model->msg = 'Unknown task';
    return;
}

$userid = $ctx->auth->loggedUser();
$userData = $ctx->userDataDao->findFirst("userid = $userid");

if ($ctx->cheatService->isSuspended($userid)) {
    $ctx->util->changePage('message');
    $model->msg = 'Submission not accepted from this account';
    return;
}

$ctx->util->sessionPut('last_subm', time());

$res = $ctx->taskService->processSolution($task, $userid, $answer, $solution, $lang);
$model->solved = $res[0];
$model->gainedPoints = number_format($res[1], 2);
$model->userPoints = number_format($res[2], 2);
$model->task = $task;

if ($res[1] > 0 && !$ctx->elems->conf->calcPointsSecret) {
    $ctx->miscService->calcPoints();
}

$rndPrimes = array(13, 17, 19, 23, 29, 31, 37);
$model->userRnd = $rndPrimes[$userid % count($rndPrimes)];
$model->numSolved = $userData->solved;

$expectedAnswer = $ctx->taskService->deleteAnswer($taskid);
if (strlen($expectedAnswer) > 2) {
    $eaPrefix = substr($expectedAnswer, 0, 2);
    if ($eaPrefix === "' " || $eaPrefix == '. ') {
        $expectedAnswer = substr($expectedAnswer, 2);
    }
}

if ($model->solved) {
    if ($ctx->challengeService->challengeExists($taskid)) {
        $model->challengeResult = explode(' ', $expectedAnswer, 3);
    }
    $model->answer = '';
    $model->notes = $ctx->taskService->loadNotes($taskid);
    $model->nextTasks = $ctx->tasksDao->find(
        "solved < {$task->solved} order by solved desc", 5);
    if (count($model->nextTasks) > 3) {
        $model->nextTasks = array_slice($model->nextTasks, rand(0, 2), 3);
    }
    $recom = $ctx->miscService->getTaggedValue("recom-$taskid");
    if ($recom) {
        $recom = str_replace(' ', ',', $recom);
        $recomTasks = $ctx->tasksDao->find("id in ($recom)");
        array_splice($model->nextTasks, 0, 0, $recomTasks);
    }
} else {
    $model->answer = $expectedAnswer;
    $model->submittedAnswer = base64_encode($answer);
    $model->inputData = $ctx->taskService->deleteInputData($taskid);
    $model->editorial = '';
}

$url = url('task_view', 'param', $task->url);
$msg = $model->solved
        ? "I'm proud to tell I've just solved [{$task->title}]($url)!"
        : "I'm sorry to say I've failed [{$task->title}]($url)... :(";
$ctx->miscService->postToMessHall($userid, $msg);
$ctx->miscService->logAction($userid, ($model->solved ? 'solved' : 'failed') . " {$task->id}");

if ($model->solved && $model->gainedPoints > 0) {
    $nowSolved = $userData->solved + 1; //we haven't reloaded UD after update
    $model->numSolved = $nowSolved;
    $rank = $ctx->userService->rankAsNumber($nowSolved);
    $rankOld = $ctx->userService->rankAsNumber($userData->solved);
    if ($rank != $rankOld) {
        $rank = $ctx->userService->rank($nowSolved);
        $ctx->miscService->postToMessHall($userid,
            "<span class=\"strong\">I'm excited to announce I've achieved "
            . "$rank rank!!!</span>");
    }
}

