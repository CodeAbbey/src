<?php

$email = $ctx->util->paramPost('newemail');
$pwd = $ctx->util->paramPost('chkpwd');
$userid = $ctx->auth->loggedUser();

if (empty($userid) || is_null($email) || is_null($pwd)) {
    $ctx->util->changePage('error404');
    return;
}

$user = $ctx->usersDao->findFirst("id = $userid");

if (substr($user->loginid, 0, 2) != '!!') {
    $ctx->util->changePage('error404');
    return;
}

if ($ctx->loginService->hashPassword($pwd) != $user->password) {
    $message = 'Error: Password is wrong!';
} else {
    $chk = $ctx->userService->checkEmail($email);
    if (is_null($chk)) {
        $user->loginid = '!!' . $ctx->loginService->hashEmail($email);
        $ctx->usersDao->save($user);
        $message = 'E-mail updated! ';
    } else {
        $message = "Error: $chk";
    }
}

$ctx->util->flash($message);
$ctx->util->redirect('user_settings');
