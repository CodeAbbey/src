<?php

namespace module\service;

class FriendService extends \stdClass {

    function getFriendship($userid, $targetid) {
        $res = $this->loadFriendship($userid, $targetid);
        if (!is_object($res)) {
            return 0;
        }
        return $res->visible ? 1 : -1;
    }

    private function loadFriendship($userid, $targetid) {
        return $this->ctx->friendsDao->findFirst("userid = $userid and targetid = $targetid");
    }

    function followedByCount($userid) {
        return $this->ctx->friendsDao->getCount("targetid = $userid");
    }

    function followingCount($userid) {
        return $this->ctx->friendsDao->getCount("userid = $userid");
    }

    function followedBy($userid) {
        $list = $this->ctx->friendsDao->find("targetid = $userid and visible <> 0");
        return $this->loadUsers($list, 'userid');
    }

    function following($userid) {
        $list = $this->ctx->friendsDao->find("userid = $userid and visible <> 0");
        return $this->loadUsers($list, 'targetid');
    }

    function followingAllIds($userid) {
        $list = $this->ctx->friendsDao->makeLookup('targetid', "userid = $userid");
        return array_keys($list);
    }

    private function loadUsers(&$records, $field) {
        $users = array();
        foreach ($records as $rec) {
            $users[] = $this->ctx->usersDao->read($rec->$field);
        }
        return $users;
    }

    function changeState($userid, $targetid, $secret) {
        $cur = $this->loadFriendship($userid, $targetid);
        if (is_object($cur)) {
            $this->ctx->friendsDao->delete($cur->id);
        } else {
            $f = new \stdClass();
            $f->userid = $userid;
            $f->targetid = $targetid;
            $f->visible = $secret ? 0 : 1;
            $this->ctx->friendsDao->save($f);
        }
    }

}

