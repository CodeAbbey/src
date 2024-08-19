<?php

namespace module\service;

class MiscService extends \stdClass {

    function countryNameByCode($code) {
        if (!$code) {
            return null;
        }
        $country = $this->ctx->countriesDao->findFirst("code = '$code'");
        if (is_object($country)) {
            return $country->title;
        }
        return null;
    }

    function curTimeStr() {
        return date('Y-m-d H:i:s');
    }

    function formatDate($ts, $withTime = false) {
        if (gettype($ts) == 'string') {
            $ts = strtotime($ts);
        }
        return date($withTime ? 'M j Y H:i' : 'M j Y', $ts);
    }

    function formatTitle() {
        if (empty($this->ctx->elems->title)) {
            return $this->ctx->elems->conf->title;
        }

        return "{$this->ctx->elems->title} - {$this->ctx->elems->conf->projectName}";
    }

    function formatDescription() {
        if (!empty($this->ctx->elems->description)) {
            return $this->ctx->elems->description;
        }
        if (empty($this->ctx->elems->title)) {
            return $this->ctx->elems->conf->descr;
        }
        $title = preg_replace('/[\"\&]/', '', $this->ctx->elems->title);
        return "$title - {$this->ctx->elems->conf->descrSuffix}";
    }

    function headerLastModified($timestamp) {
        $time = strtotime($timestamp);
        $twoWeeksAgo = time() - 86400 * 14;
        $time = max($time, $twoWeeksAgo);
        header('Last-Modified: ' . date("D, d M Y H:i:s", $time) . ' GMT');
    }

    function postToMessHall($userid, $message) {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->created = date('Y-m-d H:i:s');
        $record->message = base64_encode($message);
        $this->ctx->chatDao->save($record);
    }

    function logAction($userid, $message) {
        $date = date('Ymd-His');
        if (!empty($userid)) {
            $user = $this->ctx->usersDao->read($userid);
            $url = $user->url;
        } else {
            $url = '-null-';
        }
        $logName = $this->ctx->elems->conf->logging['activity'] ?? false;
        if ($logName)
            error_log("$date $url $message\n", 3, $logName);
    }

    function summarize(&$arr, $field) {
        $res = array();
        foreach ($arr as $elem) {
            $idx = $elem->$field;
            if (!isset($res[$idx])) {
                $res[$idx] = 1;
            } else {
                $res[$idx]++;
            }
        }
        return $res;
    }

    function listIds(&$arr, $field, $imploded = true) {
        $res = $this->summarize($arr, $field);
        $res = array_keys($res);
        if ($imploded) {
            $res = implode(',', $res);
        }
        return $res;
    }

    function validUrlParam($url) {
        return $url === null || preg_match('/^[a-z0-9\-\_]+$/', $url);
    }

    function validUrlParams() {
        $args = func_get_args();
        foreach ($args as $a) {
            if (!$a || !$this->validUrlParam($a)) {
                return false;
            }
        }
        return true;
    }

    function encryptInt($value) {
        return sprintf("%12d", $value);
    }

    function decryptInt($cipher) {
        return intval($cipher);
    }

    function setTaggedValue($tag, $val) {
        $record = $this->ctx->tagValDao->findFirst("tag = '$tag'");
        if (!is_object($record)) {
            $record = new \stdClass();
            $record->tag = $tag;
        } else if ($val === null) {
            $this->ctx->tagValDao->delete($record->id);
            return;
        }
        $record->val = base64_encode(serialize($val));
        $this->ctx->tagValDao->save($record);
    }

    function getTaggedValue($tag) {
        $record = $this->ctx->tagValDao->findFirst("tag = '$tag'");
        if (!is_object($record)) {
            return null;
        }
        return unserialize(base64_decode($record->val));
    }

    function getTaggedValues($prefix) {
        $records = $this->ctx->tagValDao->find("tag like '$prefix%'");
        $res = array();
        foreach ($records as $rec) {
            $res[$rec->tag] = unserialize(base64_decode($rec->val));
        }
        return $res;
    }

    function isMobile() {
        $aMobileUA = array('/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad',
            '/android/i' => 'Android', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile');
        foreach($aMobileUA as $sMobileKey => $sMobileOS){
            if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
                return true;
            }
        }
        return false;
    }

    function calcPoints() {
        $this->ctx->tasksDao->updateCosts();
        $tasks = $this->ctx->tasksDao->makeLookup();

        $res = [];
        foreach ($tasks as $t) {
            if ($this->ctx->challengeService->challengeExists($t->id)) {
                $this->ctx->challengeService->recalculate($t->id);
            }
            $res[] = "{$t->title}: {$t->cost}\n";
        }

        $this->ctx->userDataDao->updatePoints();

        return $res;
    }

}

