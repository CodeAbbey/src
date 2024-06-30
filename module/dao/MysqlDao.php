<?php

namespace module\dao;

class MysqlDao extends \stdClass {

    private static $shared = null;

    private $table;
    private $entityClass;
    private $conn;

    function __construct($table, $entityClass = 'stdClass') {
        self::init();
        $this->table = self::$shared['prefix'] . $table;
        $this->entityClass = $entityClass;
        $this->conn = self::$shared['conn'];
    }

    function read($id) {
        $query = "select * from {$this->table} where id = $id";
        $res = $this->conn->query($query);

        if ($res === false || $res->num_rows < 1) {
            return false;
        }

        $res = $this->objectsArray($res);
        return $res[0];
    }

    function save($entity) {
        $a = (array) $entity;
        foreach ($a as $k => $v) {
            $a[$k] = $this->conn->escape_string($v);
        }
        $names = implode(',', array_keys($a));
        $values = implode('","', array_values($a));
        $query = sprintf('replace into %s (%s) values ("%s")', $this->table, $names, $values);
        $res = $this->conn->query($query);
        if ($res === false) {
            return false;
        }
        if (isset($entity->id) && $entity->id !== null) {
            return $entity->id;
        }
        return $this->conn->insert_id;
    }

    function delete($id) {
        $query = "delete from {$this->table} where id = $id";
        return $this->conn->query($query);
    }

    function findIds($cond = null, $limit = null, $offset = null) {
        $query = "select id from {$this->table}";
        $query .= $this->clauses($cond, $limit, $offset);
        $res = $this->conn->query($query);

        return $this->singleFieldArray($res);
    }

    function find($cond = null, $limit = null, $offset = null) {
        $query = "select * from {$this->table}";
        $query .= $this->clauses($cond, $limit, $offset);
        $res = $this->conn->query($query);

        return $this->objectsArray($res);
    }

    function makeLookup($key = 'id', $cond = null) {
        $res = $this->find($cond);
        $lookup = array();
        foreach ($res as $val) {
            $lookup[$val->$key] = $val;
        }
        return $lookup;
    }

    function findFirst($cond = null) {
        $res = $this->find($cond, 1);
        if ($res === false || sizeof($res) < 1) {
            return false;
        }
        return $res[0];
    }

    function getCount($cond = null) {
        $query = "select count(*) from {$this->table}";
        $query .= $this->clauses($cond, null, null);
        $res = $this->conn->query($query);
        $res = $this->singleFieldArray($res);
        return $res[0];
    }

    protected function query($query) {
        return $this->conn->query($query);
    }

    private function clauses($cond, $limit, $offset) {
        if ($cond !== null) {
            $query = " where $cond";
        } else {
            $query = "";
        }
        if ($offset !== null) {
            $query .= " limit $offset, $limit";
        } else if ($limit !== null) {
            $query .= " limit $limit";
        }
        return $query;
    }

    protected function objectsArray($res) {
        if ($res === false) {
            return false;
        }

        $a = array();
        while (true) {
            $o = $res->fetch_object($this->entityClass);
            if ($o === null) {
                break;
            }
            array_push($a, $o);
        }

        return $a;
    }

    protected function singleFieldArray($res) {
        if ($res === null) {
            return false;
        }

        $f = array();
        while (true) {
            $row = $res->fetch_row();
            if ($row === null) {
                break;
            }
            array_push($f, $row[0]);
        }

        return $f;
    }

    public function lastError() {
        return self::$shared['error'];
    }

    public function getTable() {
        return $this->table;
    }

    protected static function init() {
        if (self::$shared !== null) {
            return;
        }

        global $ctx;
        $elems = $ctx->elems;
        $port = $elems->conf->mysql['port'];
        $conn = new \mysqli(
            "{$elems->conf->mysql['host']}" . (!empty($port) ? ":$port" : ""),
            $elems->conf->mysql['username'],
            $elems->conf->mysql['password']);

        self::$shared = array('conn' => $conn, 'prefix' => $elems->conf->mysql['prefix'], 'error' => null);

        if ($conn->connect_error) {
            self::$shared['error'] = "Mysql connection error {$conn->connect_errno}";
            return;
        }

        $conn->set_charset('utf8');

        $res = $conn->select_db($elems->conf->mysql['db']);

        if ($res === false) {
            self::$shared['error'] = 'Mysql database error';
            return;
        }
    }

    function __destruct() {
        if (self::$shared === null) {
            return;
        }
        self::$shared['conn']->close();
        self::$shared = null;
    }

}
