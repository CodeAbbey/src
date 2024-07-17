<?php

namespace module\sys;

class Util extends \stdClass {

    private $sessionStarted;

    function sessionPut($key, $value) {
        $this->sessionPrepare();
        $_SESSION[$key] = $value;
    }

    function sessionDel($key) {
        $this->sessionPrepare();
        unset($_SESSION[$key]);
    }

    function sessionGet($key) {
        $this->sessionPrepare();
        if (!isset($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }

    private function sessionPrepare() {
        if (!$this->sessionStarted) {
            ini_set('session.gc_maxlifetime', 6 * 3600);
            session_start();
            $this->sessionStarted = true;
        }
    }

    function flash($msg = null) {
        if ($msg !== null) {
            $this->sessionPut('__flash_msg', $msg);
        } else {
            $res = $this->sessionGet('__flash_msg');
            $this->sessionDel('__flash_msg');
            return $res;
        }
    }

    function fullUrl($url, $prefix = 'http://') {
        if (strpos($url, '/') === false) {
            $url = url($url);
        }
        return $prefix . $_SERVER['HTTP_HOST'] . $url;
    }

    function sendGetRequest($url, $timeout = 3) {
        $context = stream_context_create(array('http' => array('timeout' => $timeout)));
        return file_get_contents($url, false, $context);
    }

    function sendPostRequest($url, $data, $timeout = 3, &$responseHeaders = null) {
        $context = stream_context_create(array('http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => !is_string($data) ? http_build_query($data) : $data,
            'timeout' => $timeout
        )));
        $res = file_get_contents($url, false, $context);
        if ($responseHeaders !== null) {
            $responseHeaders = $http_response_header;
        }
        return $res;
    }

    function redirect($url) {
        if (strpos($url, '/') === false) {
            $url = url($url);
        }
        header("Location: $url");
        $this->changePage(null);
    }

    function paramGet($name) {
        if (!isset($_GET[$name])) {
            return null;
        }
        return $_GET[$name];
    }

    function paramPost($name) {
        if (!isset($_POST[$name])) {
            return null;
        }
        return $_POST[$name];
    }

    function fragment($name) {
        $name = $this->ctx->elems->conf->custFrag[$name] ?? $name;
        if ($name === '') {
            return;
        }
        $name = str_replace('_', '/', $name);
        if (!addfile('fragments/' . $name . '.html')) {
            echo '??? FRAGMENT: ' . $name . ' ???';
        }
    }

    function changePage($name) {
        $this->ctx->elems->page = str_replace('_', '/', $name ?? '');
    }

    function plainOutput($data, $type = 'text/plain') {
        $this->changePage(null);
        header("Content-Type: $type");
        echo $data;
    }

    function sillyDecode($s) {
        $hiCode = ord('p');
        $loCode = ord('k');
        $n = strlen($s);
        $res = array();
        for ($i = 0; $i < $n; $i += 2) {
            $res[] = chr(($hiCode - ord($s[$i])) * 16 + (ord($s[$i + 1]) - $loCode));
        }
        return implode('', $res);
    }

    function runScript($cmd, $input, &$output, &$stderr = null) {
        $dsc = array(
            array('pipe', 'r'),
            array('pipe', 'w'),
            array('pipe', 'w'));
        $pipes = array();
        $ps = proc_open($cmd, $dsc, $pipes);
        if (!is_resource($ps)) {
            return -1;
        }
        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        if ($stderr !== null) {
            $stderr = $err;
        }
        return proc_close($ps);
    }

    function runSqlite($input) {
        return $this->sendPostRequest(
            'http://rodiongork.freeshell.org/sqlrun.cgi', $input, 5);
    }

}
