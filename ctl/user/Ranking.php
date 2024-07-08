<?php

$friends = $ctx->util->paramGet('friends');

$pageFromForm = $ctx->util->paramPost('pf');
if (is_numeric($pageFromForm)) {
    $ctx->util->redirect(url('user_ranking', 'p', $pageFromForm - 1));
    return;
}

if ($ctx->util->paramGet('clr')) {
    $ctx->util->sessionDel('ranking-country');
    $ctx->util->sessionDel('ranking-language');
    $ctx->util->redirect(url('user_ranking'));
    return;
}

$model->country = $ctx->util->paramPost('country');
if (empty($model->country)) {
    $model->country = $ctx->util->sessionGet('ranking-country');
}
$model->language = $ctx->util->paramPost('lang');
if (empty($model->language)) {
    $model->language = $ctx->util->sessionGet('ranking-language');
}

$model->userid = $ctx->auth->loggedUser();
if (!is_numeric($model->userid)) {
    $model->friends = null;
} else {
    $model->friends = $friends ? $model->userid : null;
}

$countries = $ctx->countriesDao->makeLookup('code');
if (!isset($countries[$model->country])) {
    $model->country = '';
}
$model->languages = $ctx->langService->languagesArray();
if (!isset($model->languages[$model->language])) {
    $model->language = '';
}

if ($model->friends === null) {
    $page = $ctx->util->paramGet('p');
    $count = $ctx->userService->rankingPageSize;
    if (!is_numeric($page) || $page < 0 || $page > 100000000) {
        $page = 0;
    } else {
        $page = floor($page);
    }

    $query = 'solved > 0';
    if (!empty($model->country)) {
        $query .= " and country = '{$model->country}'";
        $ctx->util->sessionPut('ranking-country', $model->country);
    }
    if (!empty($model->language)) {
        $query .= " and language = '{$model->language}'";
        $ctx->util->sessionPut('ranking-language', $model->language);
    }
} else {
    $friendIds = $ctx->friendService->followingAllIds($model->userid);
    if (!in_array($model->userid, $friendIds)) {
        $friendIds[] = $model->userid;
    }
    $page = 0;
    $count = count($friendIds);
    $friendIdsStr = implode(',', $friendIds);
    $query = "id in ($friendIdsStr)";
}
$model->total = $ctx->userRankDao->getCount($query); 
$model->rank = $ctx->userRankDao->find($query, $count, $page * $count);
$model->page = $page;
$model->count = $count;


$userPresent = false;
$loggedUser = $ctx->auth->user() ? $ctx->auth->loggedUser() : false;

foreach ($model->rank as $entry) {
    if ($loggedUser !== false && $entry->id == $loggedUser) {
        $userPresent = true;
        $entry->current = true;
    } else {
        $entry->current = false;
    }
    $ctx->userService->additionalRankingData($entry, $countries);
}

if ($loggedUser !== false && !$userPresent) {
    $model->myRank = $ctx->userRankDao->findFirst("id = $loggedUser");
    $ctx->userService->additionalRankingData($model->myRank, $countries);
    $model->myRank->current = true;
    $model->myRank->before = $model->myRank->rankpos < $model->rank[0]->rankpos;
} else {
    $model->myRank = null;
}

$model->countries = $countries;

$ctx->elems->robots = $page < 5 ? 'index,follow' : 'noindex,nofollow';
$ctx->elems->title = 'User ranking';
$ctx->elems->analytics = true;

