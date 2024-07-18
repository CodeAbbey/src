<?php

namespace module\service;

class CheatService extends \stdClass {

    public function isSuspended($userid) {
        return $this->status($userid)->suspended;
    }

    public function status($userid) {
        $res = new \stdClass();
        $res->score = 7;
        $res->suspended = false;
        $res->msg = '';
        $res->note = '';
        return $res;
    }
}
