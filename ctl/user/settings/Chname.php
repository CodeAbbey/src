<?php

$newname = $ctx->util->paramPost('newname');

if (!$ctx->auth->user() || empty($newname)) {
    $ctx->util->changePage('error404');
    return;
}

$user = $ctx->usersDao->findFirst("id = " . $ctx->auth->loggedUser());
$userData = $ctx->userDataDao->findFirst("userid = {$user->id}");

$loginType = substr($user->loginid, 0, 2);
if ($loginType == '!!') {
    if ($userData->solved < $ctx->elems->conf->nameChangeLevel) {
        $ctx->util->changePage('error404');
        return;
    }
}

$checkName = $ctx->userService->checkNewUsername($newname, true);
if ($checkName !== null) {
    $ctx->util->flash("Error: $checkName");
    $ctx->util->redirect('user_profile');
    return;
}

if (!$ctx->cheatService->isSuspended($user->id)) {
    $user->username = $newname;
    $ctx->usersDao->save($user);
}

$ctx->util->flash('Name was changed!');
$ctx->util->redirect('user_settings');

