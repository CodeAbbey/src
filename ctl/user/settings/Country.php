<?php

$code = $ctx->util->paramPost('code');
$userid = $ctx->auth->loggedUser();

if (empty($userid) || empty($code) || !preg_match('/[a-z]{2,3}/i', $code)) {
    $ctx->util->changePage('error404');
    return;
}

$country = $ctx->countriesDao->findFirst("code = '$code'");

if (!is_object($country)) {
    $ctx->util->changePage('error404');
    return;
}

$userdata = $ctx->userDataDao->findFirst("userid = $userid");
$userdata->country = $code;
$ctx->userDataDao->save($userdata);

$ctx->util->flash('Country info updated!');
$ctx->util->redirect('user_settings');
