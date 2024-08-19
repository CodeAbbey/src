<?php

$model->records = $ctx->chatViewDao->find();

$oldestTime = time() - 86400;
for ($i = 0; $i < count($model->records); $i++) {
    $rec = $model->records[$i];
    $time = strtotime($rec->created);
    if ($time < $oldestTime) {
        array_splice($model->records, $i);
        break;
    }
    $rec->message = $ctx->markdown->parse(base64_decode($rec->message));
    $rec->created = date('H:i:s', $time);
    $rec->rank = $ctx->userService->rank($rec->solved, $rec->username);
}

$model->stats = $ctx->userTasksDao->solvingStats(date('Y-m-d', time() - 86400 * 15));
if ($ctx->auth->admin()) {
    $statsAvg = 0;
    for ($i = 1; $i < count($model->stats); $i++) {
        $statsAvg += $model->stats[$i]->cnt;
    }
    $model->statsAvg = number_format($statsAvg / (count($model->stats) - 1), 1);
}

$model->rights = $ctx->userService->messAllowed();

$ctx->elems->robots = 'noindex,nofollow';
$ctx->elems->title = 'Mess Hall';
$ctx->elems->analytics = true;

