<?php

$info = $ctx->util->paramPost('info');

if (!$ctx->userService->personalInfoAllowed()) {
    $ctx->util->changePage('error404');
    return;
}

$userid = $ctx->auth->loggedUser();

$userInfo = $ctx->taskDataDao->findFirst("taskid = $userid and type = 'uinfo'");
if (!is_object($userInfo)) {
    $userInfo = new \stdClass();
    $userInfo->taskid = $userid;
    $userInfo->type = 'uinfo';
}
$userInfo->data = base64_encode($info);
$ctx->taskDataDao->save($userInfo);

$ctx->util->redirect('user_profile');
