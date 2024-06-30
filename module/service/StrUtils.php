<?php

namespace module\service;

class StrUtils extends \stdClass {

    function nameToUrl($name) {
        $name = strtolower($name);
        $name = preg_replace('/\s+/', '-', $name);
        $name = preg_replace('/[^a-z0-9\-]/', '', $name);
        return $name;
    }

    function zeroDash($value) {
        return $value == 0 ? '-' : (string) $value;
    }

}
