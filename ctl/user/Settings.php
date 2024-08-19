<?php

if (!$ctx->auth->user()) {
    $ctx->util->changePage('error404');
    return;
}

$user = $ctx->usersDao->read($ctx->auth->loggedUser());

$model->username = $user->username;

$userdata = $ctx->userDataDao->findFirst("userid = {$user->id}");

$model->country = $userdata->country;

$model->countries = $ctx->countriesDao->find();
foreach ($model->countries as $land) {
    $land->title = $land->code . ' - ' . $land->title;
}

$loginType = substr($user->loginid, 0, 2);
$model->usesPwd = (strlen($user->password) >= 8);
$model->nameForChange = (!$model->usesPwd || $userdata->solved >= $ctx->elems->conf->nameChangeLevel)
    ? $user->username : null;
$model->email = ($loginType == '!!'
    ? preg_replace('/(.)[^\@]+(\@.+)/', '\1***\2', substr($user->loginid, 2))
    : null);
$model->ghname = ($loginType == 'gh')
    ? preg_replace('/.*\./', '', $user->loginid) : '';

$model->avatar = $userdata->avatar;

$personalInfo = $ctx->taskDataDao->findFirst("taskid = {$user->id} and type = 'uinfo'");
$model->personalInfo = is_object($personalInfo)
    ? base64_decode($personalInfo->data) : "";

