<?php

namespace module\sys;

class Elems extends \stdClass {

    public static $elems;

    public $layout;
    public $styles;
    public $scripts;
    public $page;
    public $path;
    public $conf;
    public $modules;
    public $moduleOrder;
    public $errors;
    public $contentResult;

    function __construct() {
        $this->layout = 'default_bs';
        $this->styles = [];
        $this->scripts = [];
        $this->conf = new \stdClass();
        $this->conf->custFrag = [];
        $this->conf->custSvc = [];
        $this->conf->logging = [];
        $this->modules = array();
        $this->moduleOrder = array();
        $this->errors = array();
        self::$elems = $this;
    }

    function get($field, $default = '') {
        if (isset($this->$field)) {
            return $this->$field;
        }
        return $default;
    }

    function addError($e) {
        array_push($this->errors, $e);
    }
}

