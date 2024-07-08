<?php

namespace module\dao;

class TasksDao extends MysqlDao {

    function __construct() {
        parent::__construct('tasks');
    }

    function updateStats() {
        $this->query('update ' . $this->getTable()
            . ' set solved = (select count(userid) from '
            . $this->getPrefix() . 'usertasks where solved > 0 and taskid = ' . $this->getTable() . '.id)');
    }

    function updateCosts() {
        $this->query('update ' . $this->getTable() . ' full join '
            . '(select max(solved) + 1 as maxsolved from ' . $this->getTable() . ') mx '
            . 'set cost = 1 + log10(maxsolved / (solved + 1)) * 4');
    }

}
