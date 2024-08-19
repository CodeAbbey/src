<?php

$newpwd = $ctx->util->paramPost('newpwd');
$reppwd = $ctx->util->paramPost('reppwd');
$oldpwd = $ctx->util->paramPost('oldpwd');
$userid = $ctx->auth->loggedUser();

if (empty($userid) || is_null($newpwd) || is_null($reppwd) || is_null($oldpwd)) {
    $ctx->util->changePage('error404');
    return;
}

$user = $ctx->usersDao->findFirst("id = $userid");

$wrong = true;
$providedHash = $ctx->loginService->hashPassword($oldpwd);
if ($providedHash == $user->password) {
    $wrong = false;
} else {
    $tempPwd = $ctx->miscService->getTaggedValue("pwd-{$user->id}");
    if ($tempPwd) {
        list($ts, $tempHash) = explode(' ', $tempPwd);
        if ($tempHash == $providedHash) {
            $wrong = false;
        }
    }
}

if ($wrong) {
    $message = 'Error: Password is wrong!';
} else {
    $chk = $ctx->userService->checkPassword($newpwd, $reppwd);
    if (is_null($chk)) {
        $user->password = $ctx->loginService->hashPassword($newpwd);
        $ctx->usersDao->save($user);
        $message = 'Password updated! ';
        $ctx->miscService->logAction($userid, 'pwd changed');
    } else {
        $message = "Error: $chk";
    }
}

$ctx->util->flash($message);
$ctx->util->redirect('user_settings');
