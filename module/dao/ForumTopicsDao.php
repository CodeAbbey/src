<?php

namespace module\dao;

class ForumTopicsDao extends MysqlDao {

    function __construct() {
        parent::__construct('forumtopics');
    }

    function recent($from = '1970-01-01') {
        $res = $this->query('select * from '
                . $this->getTable() . " where lastpost > '$from' order by lastpost desc limit 20");
        return $this->objectsArray($res);
    }

}

