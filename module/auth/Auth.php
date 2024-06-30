<?php

namespace module\auth;

class Auth extends \stdClass {

    function login($username, $password) {
        $username = preg_replace("/[\\'\\;]/", '', $username);
        $record = $this->ctx->usersDao->findFirst("username = '$username'");
        if ($record === false) {
            return false;
        }
        if ($record->password != $password) {
            $tempPwd = $this->ctx->miscService->getTaggedValue("pwd-{$record->id}");
            if (!$tempPwd) {
                return false;
            }
            list($ts, $hash) = explode(' ', $tempPwd);
            if ($ts + 1800 < time() || $hash != $password) {
                return false;
            }
        }
        $this->ctx->util->sessionPut('userid', $record->id);
        return true;
    }

    function logout() {
        $this->ctx->util->sessionDel('userid');
    }

    function check($role) {
        $userid = $this->loggedUser();
        if ($userid === null) {
            return false;
        }
        return $this->ctx->rolesDao->findFirst("userid = $userid and role = '$role'") !== false;
    }

    protected function checkWithHeader($role, $header, $page) {
        if ($this->check($role)) {
            return true;
        }
        $this->ctx->util->changePage($page);
        header($header);
        return false;
    }

    public function loggedUser() {
        return $this->ctx->util->sessionGet('userid');
    }

    function checkWith403($role) {
        return $this->checkWithHeader($role, 'HTTP/1.0 403 Forbidden', 'error403');
    }

    function checkWith404($role) {
        return $this->checkWithHeader($role, 'HTTP/1.0 404 Not Found', 'error404');
    }

    function checkWithRedirect($role, $url) {
        return $this->checkWithHeader($role, "Location: $url");
    }

    function admin() {
        return $this->check('admin');
    }

    function user() {
        return $this->check('user');
    }

    function username() {
        $id = $this->loggedUser();
        if (!$id) {
            return '';
        }
        $user = $this->ctx->usersDao->read($id);
        return $user->username;
    }

}
