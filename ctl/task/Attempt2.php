<?php

if (!$ctx->auth->user()) {
    $ctx->util->changePage('error404');
    return;
}

$ctx->util->plainOutput('');

$inputPlain = file_get_contents('php://input');
$input = json_decode($inputPlain);

$taskid = $input->taskid;
$answer = $input->answer;
$solution = $input->solution;
$lang = $input->lang;
if ($input->b64enc ?? 0) {
    if ($solution[0] == '-') {
        $solution = strrev(substr($solution, 1));
    }
    $solution = str_replace(array('.','_','-'), array('+','/','='), $solution);
    $solution = base64_decode($solution);
}

if (!is_numeric($taskid) || !$solution || !$answer) {
    echo json_encode(['msg'=>'Insufficient data A', 'data'=>$inputPlain]);
    return;
}

$lastSolved = $ctx->util->sessionGet('lastSolved');
if ($lastSolved) {
    $ctx->util->sessionDel('lastSolved');
    $last = array_map('intval', explode(' ', $lastSolved));
    $secRem = ($last[0] == $taskid ? $last[2] : max($last[2], $last[1])) - time();
    if ($secRem > 0) {
        echo json_encode(['msg'=>"You could not submit new tasks for $secRem seconds more"]);
        return;
    }
}

$languages = $ctx->langService->languagesArray();
$lang = isset($languages[$lang]) ? $languages[$lang] : "";

$task = $ctx->tasksDao->findFirst("id = $taskid");

if (!is_object($task)) {
    echo json_encode(['msg'=>'Unknown task']);
    return;
}

$userid = $ctx->auth->loggedUser();
$userData = $ctx->userDataDao->findFirst("userid = $userid");

if ($ctx->cheatService->isSuspended($userid)) {
    echo json_encode(['msg'=>'Submission not accepted from this account']);
    return;
}

$ctx->util->sessionPut('last_subm', time());

$res = $ctx->taskService->processSolution($task, $userid, $answer, $solution, $lang);
$json = new \stdClass();
$json->solved = $res[0];
$json->gainedPoints = number_format($res[1], 2);
$json->userPoints = number_format($res[2], 2);
$json->task = $task;

if ($res[1] > 0 && !$ctx->elems->conf->calcPointsSecret) {
    $ctx->miscService->calcPoints();
}

$rndPrimes = array(13, 17, 19, 23, 29, 31, 37);
$json->userRnd = $rndPrimes[$userid % count($rndPrimes)];
$json->numSolved = $userData->solved;

$expectedAnswer = $ctx->taskService->deleteAnswer($taskid);
if (strlen($expectedAnswer) > 2) {
    $eaPrefix = substr($expectedAnswer, 0, 2);
    if ($eaPrefix === "' " || $eaPrefix == '. ') {
        $expectedAnswer = substr($expectedAnswer, 2);
    }
}

if ($json->solved) {
    if ($ctx->challengeService->challengeExists($taskid)) {
        $json->challengeResult = explode(' ', $expectedAnswer, 3);
    }
} else {
    $json->answer = $expectedAnswer;
    $json->submittedAnswer = base64_encode($answer);
    $json->inputData = $ctx->taskService->deleteInputData($taskid);
}

$url = url('task_view', 'param', $task->url);
$msg = $json->solved
        ? "I'm proud to tell I've just solved [{$task->title}]($url)!"
        : "I'm sorry to say I've failed [{$task->title}]($url)... :(";
$ctx->miscService->postToMessHall($userid, $msg);
$ctx->miscService->logAction($userid, ($json->solved ? 'solved' : 'failed') . " {$task->id}");

if ($json->solved && $json->gainedPoints > 0) {
    $nowSolved = $userData->solved + 1; //we haven't reloaded UD after update
    $json->numSolved = $nowSolved;
    $rank = $ctx->userService->rankAsNumber($nowSolved);
    $rankOld = $ctx->userService->rankAsNumber($userData->solved);
    if ($rank != $rankOld) {
        $rank = $ctx->userService->rank($nowSolved);
        $ctx->miscService->postToMessHall($userid,
            "<span class=\"strong\">I'm excited to announce I've achieved "
            . "$rank rank!!!</span>");
    }
}

echo json_encode($json);
