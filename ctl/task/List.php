<?php

$model->title = 'Problems';

$model->addingAllowed = $ctx->auth->admin();
$model->isUser = $ctx->auth->user();

$param = $ctx->util->paramGet('param');
if (!$ctx->miscService->validUrlParam($param)) {
    $param = '';
}
$sort = $ctx->util->paramGet('sort');
if (!in_array($sort, array('tre1', 'num1', 'id1', 'num0', 'id0'))) {
    if ($ctx->auth->loggedUser()) {
        $sort = $ctx->util->sessionGet('task-list-sort');
    }
    $sort = empty($sort) ? $ctx->elems->conf->defTaskSort : $sort;
} else {
    if ($ctx->auth->loggedUser()) {
        $ctx->util->sessionPut('task-list-sort', $sort);
    }
}
$model->sort = $sort;
$sortlen = strlen($sort) - 1;
$sort = array(substr($sort, 0, $sortlen), substr($sort, $sortlen));

$tasks = null;
if (!empty($param)) {
    $tag = $ctx->tagsDao->findFirst("title = '$param'");
    if (is_object($tag)) {
        $tasks = $ctx->taskService->loadTasks($tag->id);
        $model->filterTag = $tag->title;
        $model->volume = $ctx->elems->conf->taskVolumes[$tag->title] ?? null;
    } else {
        $ctx->util->flash("No such tag");
        $ctx->util->redirect('task_list');
        return;
    }
}
if (empty($tasks)) {
    $tasks = $ctx->taskService->loadTasks(null);
}
$model->c1ids = $ctx->taskTagsDao->makeLookup('taskid',
    "tagid = (select id from {$ctx->tagsDao->getTable()} where title = 'c-1')");
if ($sort[0] == 'id') {
    usort($tasks, function($a, $b) {
        return $a->id - $b->id;
    });
} elseif ($sort[0] == 'tre') {
    $sortCnt = $ctx->tasksDao->getCount() + 1;
    usort($tasks, function($a, $b) use($sortCnt) {
        $av = ($sortCnt - $a->id) / ($a->solved + 1);
        $bv = ($sortCnt - $b->id) / ($b->solved + 1);
        return ($av > $bv) - ($av < $bv);
    });
}

if ($model->isUser) {
    $userid = $ctx->auth->loggedUser();
    $failedTasks = $ctx->userTasksDao->makeLookup('taskid', "userid = $userid and solved < 0 and variant = 1");
    $userTasks = $ctx->userTasksDao->makeLookup('taskid', "userid = $userid and variant = 0");
    foreach ($failedTasks as $taskId => $taskRecord) {
        if (!isset($userTasks[$taskId])) {
            $userTasks[$taskId] = $taskRecord;
        }
    }
}

$translations = $ctx->miscService->getTaggedValues('tran-');

$states = array(-1 => 'tried', 0 => 'waiting', 1 => 'solved');

$headPart = array();
$tailPart = array();

foreach ($tasks as $task) {
    $task->solved = $ctx->strUtils->zeroDash($task->solved);
    if (!$task->shown) {
        if (!$model->addingAllowed) {
            continue;
        }
        $task->solved = 'hid';
    }
    $task->cost = number_format($task->cost, 2);
    $task->shortUrl = $task->url;
    $task->url = url('task_view', 'param', $task->url);
    if ($model->addingAllowed) {
        $task->editurl = url('task_edit', 'id', $task->id);
    }
    if ($model->isUser && isset($userTasks[$task->id])) {
        $task->state = $states[$userTasks[$task->id]->solved];
    } else {
        $task->state = '';
    }
    if ($task->state == 'solved' && $sort[1]) {
        $tailPart[] = $task;
    } else {
        $headPart[] = $task;
    }
    if (isset($translations['tran-' . $task->id])) {
        $tongues = explode(' ', $translations['tran-' . $task->id]);
        foreach ($tongues as $i => $tongue) {
            $urlLocalised = "{$task->url}--$tongue";
            $localeTitle = $ctx->localeService->LOCALES_ARRAY[$tongue];
            $tongueUpper = strtoupper($tongue);
            $tongues[$i] = "<a class=\"locale-$tongue\" title=\"$localeTitle\" href=\"$urlLocalised\">$tongueUpper</a>";
        }
        $task->translations = implode(' ', $tongues);
    } else {
        $task->translations = '';
    }
}

$model->tasks = array_merge($headPart, $tailPart);

$ctx->elems->title = $model->title;
$ctx->elems->description = 'List of programming problems and exercises from beginner to advanced level';
$ctx->elems->analytics = true;
