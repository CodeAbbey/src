<?php

$username = $ctx->util->paramPost('username');
$password = $ctx->util->paramPost('password');
$password2 = $ctx->util->paramPost('password2');
$email = $ctx->util->paramPost('email');

if (!empty($username) && !empty($password) && strlen($password) > 5) {
    if (!empty($email) && !empty($password2)) {
        $res = $ctx->userService->checkRegisterData($username, $password, $password2, $email);
        if ($res !== null) {
            $ctx->util->flash("Error: $res");
        } else {
            $emailHash = $ctx->loginService->hashEmail($email);
            $user = $ctx->userService->register($username, "!!$emailHash", $password);
            if (!is_object($user)) {
                $ctx->util->redirect('error');
                return;
            }
            $ctx->loginService->login($user->username, $password);
            $ctx->util->flash("Thanks for registering!");
            $ctx->util->redirect('task_list');
        }
    } else {
        if ($ctx->loginService->login($username, $password)) {
            $ctx->util->flash("Hello, glad to see you!");
            $ctx->util->redirect('task_list');
        } else {
            $ctx->util->flash("Username or password is wrong?");
        }
    }
}

$model->logged = $ctx->auth->loggedUser() !== null;
$ctx->elems->robots = 'noindex,nofollow';

