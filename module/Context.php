<?php

namespace module;

use module\dao\MysqlDao;

class Context extends \module\sys\ProtoContext {

    protected function getAuth() {
        return new \module\auth\Auth();
    }

    protected function getTasksDao() {
        return new \module\dao\TasksDao();
    }

    protected function getUserTasksDao() {
        return new \module\dao\UserTasksDao();
    }

    protected function getUserDataDao() {
        return new \module\dao\UserDataDao();
    }

    protected function getChallengesDao() {
        return new \module\dao\ChallengesDao();
    }

    protected function getForumTopicsDao() {
        return new \module\dao\ForumTopicsDao();
    }

    // end of daos

    protected function getMarkdown() {
        require_once 'module/lib/Markdown_Parser.php';
        return new \Markdown_Parser();
    }

    protected function getBrainfuck() {
        return new \module\lib\Brainfuck(true);
    }

    protected function getStrUtils() {
        return new \module\service\StrUtils();
    }

}
