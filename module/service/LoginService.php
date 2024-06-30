<?php

namespace module\service;

class LoginService extends \stdClass {

    function __construct() {
        global $ctx;
    }

    function login($username, $password, $hashed = true) {
        $ctx = $this->ctx;
        if ($hashed) {
            $password = $this->hashPassword($password);
        }
        $ctx->auth->login($username, $password);
        $userid = $ctx->auth->loggedUser();
        if (!$userid) {
            return null;
        }
        if ($ctx->auth->user()) {
            $this->afterLogin($userid, $username);
        }
        return $userid;
    }

    function afterLogin($userid, $username) {
        $ctx = $this->ctx;
        $userdata = $ctx->userDataDao->findFirst("userid = $userid");
        $userdata->lastlogin = date('Y-m-d H:i:s');
        $remoteAddress = $this->remoteAddress();
        $country = $this->detectCountry($remoteAddress);
        $ctx->util->sessionPut('country', $country);
        if (empty($userdata->country)) {
            $userdata->country = $country;
        }
        $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE']; 
        $ctx->userDataDao->save($userdata);
        $ctx->miscService->logAction($userid, "login $remoteAddress $country $lang");
        $ctx->miscService->postToMessHall($userid, "I've just logged in...");
        $ctx->msgService->loadNotifications($userid);
    }

    function remoteAddress() {
        if(!empty($_SERVER['REMOTE_ADDR']) ){
            return $_SERVER['REMOTE_ADDR'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return '';
    }

    function detectCountry($ip) {
        // skip logic for now
        return 'ZZ';
    }

    function hashPassword($pwd) {
        return base64_encode(hash('sha512', $this->ctx->elems->conf->passwordSalt . $pwd, true));
    }

    function hashEmail($email) {
        $email = strtolower($email);
        $h = base64_encode(hash('sha512', $this->ctx->elems->conf->emailSalt . $email, true));
        $tail = preg_replace('/^[^\@]+/', '', $email);
        return $email[0] . '*' . substr($h, 0, 8) . $tail;
    }

    function githubLoginUrl() {
        return '#';
    }

    function googleLoginUrl() {
        return '#';
    }

}
