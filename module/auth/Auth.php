<?php

namespace module\auth;

class Auth extends \stdClass {

    function login($username, $data) {
        if (!$username) {
            return false;
        }
        $this->ctx->util->sessionPut('userid', $username);
        return true;
    }

    function logout() {
        $this->ctx->util->sessionDel('userid');
    }

    function check($role) {
        return !is_null($this->loggedUser());
    }

    protected function checkWithHeader($role, $header, $page) {
        if ($this->check($role)) {
            return true;
        }
        $this->ctx->util->changePage($page);
        header($header);
        return false;
    }

    public function admin() {
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

}
