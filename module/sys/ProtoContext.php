<?php

namespace module\sys;

class ProtoContext {

    protected $names = [];

    function __get($name) {
        $v = $this->names[$name] ?? null;
        if ($v !== null) return $v;
        $methodName = 'get' . ucfirst($name);
        if (!method_exists($this, $methodName)) {
            throw new \Exception("No property '$name' in Context!");
        }
        $res = $this->$methodName();
        if (is_object($res)) {
            $res->ctx = $this;
        }
        $this->names[$name] = $res;
        return $res;
    }

    protected function getElems() {
        return \module\sys\Elems::$elems;
    }

    protected function getUtil() {
        return new \module\sys\Util();
    }

}
