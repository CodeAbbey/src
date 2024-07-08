<?php

namespace module\dao;

class UserTasksDao extends MysqlDao {

    function __construct() {
        parent::__construct('usertasks');
    }

    function languagesCount($userid) {
        $res = $this->query("select language, count(language) as cnt from "
                . $this->getTable() . " where userid = $userid group by language");
        return $this->objectsArray($res);
    }
    
    function solvingStats($from) {
        $res = $this->query('select date(ts) as thedate, count(id) as cnt from '
                . $this->getTable() . " where solved > 0 and ts > '$from' and variant = 0 group by thedate order by thedate desc");
        return $this->objectsArray($res);
    }

    function topOfWeek($limit) {
        $res = $this->query("select userid, count(taskid) as cnt from " . $this->getTable()
                . " where solved = 1 and variant = 0 and ts > now() - interval 7 day group by userid order by cnt desc limit $limit");
        return $this->objectsArray($res);
    }

    function searchSolution($taskid, $snippet, $count=20, $offset = 0) {
        $res = $this->query(
            "select url, language, length(s.solution) as len from {$this->getTable()} ut "
            . "join {$this->getPrefix()}solutions s on ut.id = s.usertaskid "
            . "join {$this->getPrefix()}users u on ut.userid = u.id "
            . "where taskid = $taskid and INSTR(FROM_BASE64(solution), 
'$snippet') limit $count offset $offset");
        return $this->objectsArray($res);
    }

    function solvers($criteria, $noblanks=true) {
        $res = $this->query(
            "select ut.id from {$this->getTable()} ut "
            . "join {$this->getPrefix()}solutions s on ut.id = s.usertaskid "
            . "where $criteria" . ($noblanks ? " and length(s.solution) > 13" : ""));
        return $this->singleFieldArray($res);
    }

    function languages() {
        $res = $this->query('select * from '
            . '(select language,count(1) as cnt from ' . $this->getTable() . ' where solved=1 group by language) '
            . 'subq order by cnt desc');
        return $this->objectsArray($res);
    }

    function lastSolved($userid) {
        $res = $this->query("select taskid, ts as ts "
            . "from {$this->getTable()} "
            . "where userid = $userid and solved=1 order by ts desc limit 1");
        if (!$res) {
            return [0, 0];
        }
        $res = $this->objectsArray($res);
        return [$res[0]->taskid, strtotime($res[0]->ts)];
    }

    function neverSolvedIds($userid) {
        $res = $this->query("select taskid from {$this->getTable()} "
            . "where userid=$userid group by taskid having max(solved)=-1");
        return $this->singleFieldArray($res);
    }
}
