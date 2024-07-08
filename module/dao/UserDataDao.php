<?php

namespace module\dao;

class UserDataDao extends MysqlDao {

    function __construct() {
        parent::__construct('userdata');
    }

    function registerStats($from) {
        $res = $this->query('select date(created) as thedate, count(userid) as cnt from '
                . $this->getTable() . " where solved > 0 and created > '$from' group by thedate order by thedate desc");
        return $this->objectsArray($res);
    }

    function updatePoints() {
        $this->query('update ' . $this->getTable() . ' join ' . $this->getPrefix() . 'userpoints using(userid) '
            . 'set points = sumcost');
        $this->query('update ' . $this->getTable() . ' set rankpos = '
            . '(select @idx := @idx + 1 from (select @idx := 0) s) '
            . 'order by points desc');
    }

    function languages() {
        $res = $this->query('select * from '
            . '(select language,count(1) as cnt from ' . $this->getTable() . ' group by language) '
            . 'subq order by cnt desc');
        return $this->objectsArray($res);
    }

}
