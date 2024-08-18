<?php

namespace module\sys;

class ProtoContext extends \stdClass {

    function __get($name) {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            $res = $this->$methodName();
        } else {
            if (substr($name, -7) == 'Service') {
                $name = ucfirst($name);
                $name = $this->elems->conf->custSvc[$name] ?? $name;
                $fullname = "\\module\\service\\$name";
                $res = new $fullname();
            } elseif (substr($name, -3) == 'Dao') {
                $res = new \module\dao\MysqlDao(strtolower(substr($name, 0, -3)));
            } else {
                $fullname = "\\module\\lib\\" . ucfirst($name);
                $res = new $fullname();
            }
        }
        if (is_object($res)) {
            $res->ctx = $this;
        }
        $this->$name = $res;
        return $res;
    }

    protected function getElems() {
        return \module\sys\Elems::$elems;
    }

    protected function getUtil() {
        return new \module\sys\Util();
    }

}
