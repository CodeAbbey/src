<?php

$userid = null;
$loggedUser = $ctx->auth->user() ? $ctx->auth->loggedUser() : null;

$url = $ctx->util->paramGet('param');
if ($url) {
    if (is_numeric($url) && $url != '1') {
        $user = $ctx->usersDao->read($url);
        $ctx->util->redirect(url('user_profile', 'param', $user->url));
        return;
    }
    $user = $ctx->userService->byUrl($url);
    if (is_object($user)) {
        $userid = $user->id;
    }
} elseif ($loggedUser) {
    $user = $ctx->usersDao->read($loggedUser);
    $ctx->util->redirect(url('user_profile', 'param', $user->url));
    return;
}

if (!is_numeric($userid)) {
    $userid = $loggedUser;
}

if (!is_numeric($userid)) {
    $ctx->util->changePage('error404');
    return;
}

$model->friendType = ($loggedUser !== null && $loggedUser !== $userid) ? $ctx->friendService->getFriendship($loggedUser, $userid) : null;
$model->followedByCount = $ctx->friendService->followedByCount($userid);
$model->followingCount = $ctx->friendService->followingCount($userid);

if (empty($user) || !is_object($user)) {
    $user = $ctx->usersDao->read($userid);
}
$userdata = $ctx->userDataDao->findFirst("userid = $userid");

if (!is_object($user) || !is_object($userdata)) {
    $ctx->util->changePage('error404');
    return;
}

$regType = substr($user->loginid, 0, 2);

switch ($regType) {
    case 'gh':
        $user->socialnet = 'GitHub';
        $user->socialurl = 'https://github.com/' . preg_replace('/^.+\.(.+)$/', '\1', $user->loginid);
        break;
    default:
        $user->socialurl = null;
}

$userdata->created = $ctx->miscService->formatDate($userdata->created);
$userdata->lastlogin = $ctx->miscService->formatDate($userdata->lastlogin, true);
$model->data = $userdata;
$model->user = $user;
$model->cheat = $ctx->cheatService->status($user->id);
$model->data->rank = $ctx->userService->rank($userdata->solved);
$model->tasks = $ctx->userService->solvedTasks($user->id);
$model->authored = $ctx->tasksDao->find("author = '{$user->url}'");
$model->c1tagged = $ctx->userService->solvedTaggedTasks($user->id);
$model->awards = $ctx->certService->forUser($user->id);
$model->total = $ctx->userRankDao->getCount('solved > 0');

$model->countries = null;
$model->isCurrentUser = ($userid === $ctx->auth->loggedUser());
$model->bannerUrl = $ctx->util->fullUrl(url('user_banner', 'param', $user->url . '.png'), 'https://');

$personalInfo = $ctx->taskDataDao->findFirst("taskid = $userid and type = 'uinfo'");
if (is_object($personalInfo)) {
    $ctx->markdown->no_markup = true;
    $model->personalInfo = trim($ctx->markdown->parse(base64_decode($personalInfo->data)));
    $ctx->markdown->no_markup = false;
} else {
    $model->personalInfo = null;
}

$model->country = $ctx->miscService->countryNameByCode($userdata->country);

$model->fbLike = array(
    'url' => $ctx->util->fullUrl(url('user_profile', 'param', $model->user->url)),
    'image' => $userdata->avatar,
    'title' => "{$model->user->username} - watch my success at CodeAbbey!");

$ctx->elems->robots = $userdata->solved >= 5 ? 'index,follow' : 'noindex,nofollow';
$ctx->elems->title = $user->username . ' - user info';
$ctx->elems->analytics = true;

