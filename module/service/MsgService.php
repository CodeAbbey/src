<?php

namespace module\service;

class MsgService extends \stdClass {

    function loadNotifications($userid) {
        $ctx = $this->ctx;
        $msg = $this->loadPersonal($userid);
        if (!empty($msg)) {
            $ctx->util->sessionPut('message-personal', $msg);
        }
    }

    function loadPersonal($userid) {
        return $this->ctx->miscService->getTaggedValue("msg-$userid");
    }

    function setPersonal($userUrl, $msg) {
        $ctx = $this->ctx;
        $user = $ctx->usersDao->findFirst("url = '$userUrl'");
        if (is_object($user)) {
            $ctx->util->flash('Message set for ' . $user->url);
            $ctx->miscService->setTaggedValue("msg-{$user->id}", empty($msg) ? null : $msg);
        } else {
            $ctx->util->flash('User not found: ' . $userUrl);
        }
    }

    function clearPersonal() {
        $ctx = $this->ctx;
        $userId = $ctx->auth->loggedUser();
        $ctx->miscService->setTaggedValue("msg-$userId", null);
        $this->ctx->util->sessionDel('message-personal');
    }

    function getMessagePersonal() {
        $delta = time() - $this->ctx->util->sessionGet('message-personal-ts') ?? 0;
        if ($delta > 180) {
            $msg = $this->ctx->miscService->getTaggedValue('msg-' . $this->ctx->auth->loggedUser());
            if (!empty($msg)) {
                $this->ctx->util->sessionPut('message-personal-ts', time());
                $this->ctx->util->sessionPut('message-personal', $msg);
            }
        }
        return $this->ctx->util->sessionGet('message-personal');
    }

    function getAllPersonal() {
        $ctx = $this->ctx;
        $records = $ctx->tagValsDao->find("tag like 'msg-%'");
        $users = array();
        foreach ($records as $rec) {
            $userid = substr($rec->tag, 4);
            $user = $ctx->usersDao->read($userid);
            $users[$user->id] = $user->username;
        }
        return $users;
    }

}

