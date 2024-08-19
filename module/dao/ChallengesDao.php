<?php

namespace module\dao;

class ChallengesDao extends MysqlDao {

    function __construct() {
        parent::__construct('challenges');
    }
    
    function sumResults() {
        $res = $this->query("select userid, sum(total) as points from "
            . $this->getTable() . " where userid <> 0 group by userid");
        $res = $this->objectsArray($res);
        $lookup = array();
        foreach ($res as $rec) {
            $lookup[$rec->userid] = $rec->points;
        }
        return $lookup;
    }

}

