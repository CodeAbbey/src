<?php

namespace module\service;

class LoginService extends \stdClass {

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
        $ip = trim($ip);
        $lines = file_get_contents('./data/db-ip.txt');
        if (!preg_match('/\d+\.\d+\.\d+\.\d+/', $ip) || $lines === false) {
            return 'ZZ';
        }
        $ip = explode('.', $ip);
        $res = 0;
        foreach ($ip as $d) {
            $res = $res * 256 + $d;
        }
        $lines = explode("\n", $lines);
        $min = 0;
        $max = count($lines) - 1;
        while ($min < $max) {
            $mid = floor(($min + $max + 1) / 2);
            $line = explode(' ', $lines[$mid]);
            $start = floatval(base_convert($line[0], 36, 10));
            if ($start > $res) {
                $max = $mid - 1;
            } else {
                $min = $mid;
            }
        }
        $line = explode(' ', $lines[$min]);
        $from = floatval(base_convert($line[0], 36, 10));
        $till = $from + floatval(base_convert($line[1], 36, 10));
        return ($res >= $from && $res <= $till) ? $line[2] : 'ZZ';
    }

    function githubLoginUrl() {
        return '#';
    }

    function googleLoginUrl() {
        return '#';
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
}
