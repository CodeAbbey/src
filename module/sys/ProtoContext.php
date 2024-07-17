<?php

namespace module\sys;

class ProtoContext extends \stdClass {

    function __get($name) {
        $methodName = 'get' . ucfirst($name);
        if (!method_exists($this, $methodName)) {
            throw new \Exception("No property '$name' in Context!");
        }
        $res = $this->$methodName();
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
