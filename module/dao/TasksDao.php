<?php

namespace module\dao;

class TasksDao extends MysqlDao {

    function __construct() {
        parent::__construct('tasks');
    }

    function updateStats() {
        $this->query('update mess_tasks set solved = (select count(userid) from mess_usertasks where solved > 0 and taskid = mess_tasks.id)');
    }

    function updateCosts() {
        $this->query('update mess_tasks full join '
            . '(select max(solved) + 1 as maxsolved from mess_tasks) mx '
            . 'set cost = 1 + log10(maxsolved / (solved + 1)) * 4');
    }

}
