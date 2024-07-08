<?php

$url = $ctx->util->paramGet('param');

if ($url == null || !$ctx->miscService->validUrlParam($url)) {
    $ctx->util->changePage('error404');
    return;
}

list($url, $locale) = $ctx->localeService->parseLocale($url);

$model->task = $ctx->tasksDao->findFirst("url = '$url'");

if (!is_object($model->task)) {
    $ctx->util->changePage('error404');
    return;
}

$model->tags = $ctx->taskService->tagNamesForTask($model->task->id);

$userid = $ctx->auth->loggedUser();

$model->task->statement = $ctx->taskService->loadStatement($model->task->id, $locale);

if (empty($model->task->statement)) {
    $ctx->util->changePage('error404');
    return;
}

$model->textDirection = $ctx->localeService->direction($locale);

$alternativeLocales = $ctx->localeService->availableTaskLocales($model->task->id);
$model->task->locales = empty($locale) ? $alternativeLocales : array('en' => 'English');

if (!empty($alternativeLocales)) {
    $ctx->elems->locales = array('en' => $ctx->util->fullUrl(
            url('task_view', 'param', $model->task->url)));
    foreach ($alternativeLocales as $localeCode => $localeName) {
        $ctx->elems->locales[$localeCode] = $ctx->util->fullUrl(
                url('task_view', 'param', $model->task->url . "--$localeCode"));
    }
}

if ($ctx->auth->user()) {
    $user = $ctx->usersDao->findFirst("id = $userid");
    $userdata = $ctx->userdataDao->findFirst("userid = $userid");
    $usertasks = $ctx->userTasksDao->find("taskid = {$model->task->id} and userid = $userid");
    $model->codes = array();
    foreach ($usertasks as $ut) {
        $model->codes[$ut->language] = url('task_solution', 'user', $user->url, 'task', $model->task->url, 'lang', urlencode($ut->language));
    }

    $model->languages = $ctx->langService->languagesArray();

    $model->testData = $ctx->taskService->prepareData($model->task->id);

    if ($model->task->author == $user->url) {
        $model->checkerCode = str_replace('<?php', '', $ctx->taskService->loadChecker($model->task->id));
    }

    $ctx->elems->scripts[] = 'task/_view';
    $ctx->elems->scripts[] = '_ace/ace';

}

if (!$userid) {
    $ctx->miscService->headerLastModified($model->task->lastmod);
}

$ctx->elems->title = $model->task->title;

$model->challenge = $ctx->challengeService->challengeExists($model->task->id);
if ($model->challenge) {
    $model->arena = $ctx->challengeService->arenaExists($model->task->id);
}

if (!empty($locale) && $locale != 'en') {
    $titlematch = array();
    if (preg_match('/\<!\-\-\s*#(.*?)\s*\-\-\>/',
        $model->task->statement, $titlematch)) {
        $model->task->title = $titlematch[1];
    }
    $ctx->elems->title .= " ({$alternativeLocales[$locale]})";
}
$ctx->elems->keywords = $model->tags;
$ctx->elems->styles[] = 'jsmonoterm';
$ctx->elems->scripts[] = 'jsmonoterm';
$ctx->elems->analytics = true;
