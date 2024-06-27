<?php

namespace module;

use module\dao\MysqlDao;

class Context extends \module\sys\ProtoContext {

    protected function getAuth() {
        return new \module\auth\Auth();
    }

    protected function getUsersDao() {
        return new MysqlDao('users');
    }

    protected function getRolesDao() {
        return new MysqlDao('roles');
    }

    protected function getVolumesDao() {
        return new MysqlDao('volumes');
    }

    protected function getTasksDao() {
        return new \module\dao\TasksDao();
    }

    protected function getTaskListDao() {
        return new MysqlDao('tasklist');
    }

    protected function getTaskVolumesDao() {
        return new MysqlDao('taskvolumes');
    }

    protected function getTaskDataDao() {
        return new MysqlDao('taskdata');
    }

    protected function getUserTasksDao() {
        return new \module\dao\UserTasksDao();
    }

    protected function getTagsDao() {
        return new MysqlDao('tags');
    }

    protected function getTaskTagsDao() {
        return new MysqlDao('tasktags');
    }

    protected function getSolutionsDao() {
        return new MysqlDao('solutions');
    }

    protected function getUserDataDao() {
        return new \module\dao\UserDataDao();
    }

    protected function getFriendsDao() {
        return new MysqlDao('friends');
    }

    protected function getWikiDao() {
        return new MysqlDao('wiki');
    }

    protected function getTestDao() {
        return new MysqlDao('test');
    }

    protected function getCodelikesDao() {
        return new \module\dao\CodelikesDao();
    }

    protected function getUserRankDao() {
        return new MysqlDao('userrank');
    }

    protected function getUserPointsDao() {
        return new MysqlDao('userpoints');
    }

    protected function getChatDao() {
        return new MysqlDao('chat');
    }

    protected function getChatViewDao() {
        return new MysqlDao('chatview');
    }

    protected function getCountriesDao() {
        return new MysqlDao('countries');
    }

    protected function getPostsDao() {
        return new MysqlDao('posts');
    }

    protected function getCommentsDao() {
        return new MysqlDao('comments');
    }

    protected function getChallengesDao() {
        return new \module\dao\ChallengesDao();
    }

    protected function getArenaDao() {
        return new MysqlDao('arena');
    }

    protected function getForumsDao() {
        return new MysqlDao('forums');
    }

    protected function getForumTopicsDao() {
        return new \module\dao\ForumTopicsDao();
    }

    protected function getForumPostsDao() {
        return new MysqlDao('forumposts');
    }

    protected function getDiplomasDao() {
        return new MysqlDao('diplomas');
    }

    protected function getUserDiplomasDao() {
        return new MysqlDao('userdiplomas');
    }

    protected function getTagValsDao() {
        return new MysqlDao('tagval');
    }

    protected function getSubscrDao() {
        return new MysqlDao('subscr');
    }

    protected function getSchoolDao() {
        return new MysqlDao('school');
    }

    // end of daos

    protected function getMarkdown() {
        require_once 'module/lib/Markdown_Parser.php';
        return new \Markdown_Parser();
    }

    protected function getLinearAlgebra() {
        return new \module\lib\LinearAlgebra();
    }

    protected function getBrainfuck() {
        return new \module\lib\Brainfuck(true);
    }

    protected function getTuring() {
        return new \module\lib\Turing();
    }

    protected function getBasic() {
        return new \module\lib\Basic();
    }

    protected function getRegexService() {
        return new \module\service\RegexService();
    }

    protected function getStrUtils() {
        return new \module\service\StrUtils();
    }

    protected function getTaskService() {
        return new \module\service\TaskService();
    }

    protected function getInteractService() {
        return new \module\service\InteractService();
    }

    protected function getTagService() {
        return new \module\service\TagService();
    }

    protected function getVolumeService() {
        return new \module\service\VolumeService();
    }

    protected function getUserService() {
        return new \module\service\UserService();
    }

    protected function getFriendService() {
        return new \module\service\FriendService();
    }

    protected function getLoginService() {
        return new \module\service\LoginService();
    }

    protected function getForumService() {
        return new \module\service\ForumService();
    }

    protected function getMiscService() {
        return new \module\service\MiscService();
    }

    protected function getMsgService() {
        return new \module\service\MsgService();
    }

    protected function getCertService() {
        return new \module\service\CertService();
    }

    protected function getCommentService() {
        return new \module\service\CommentService();
    }

    protected function getChallengeService() {
        return new \module\service\ChallengeService();
    }

    protected function getLangService() {
        return new \module\service\LangService();
    }

    protected function getLocaleService() {
        return new \module\service\LocaleService();
    }

    protected function getMailService() {
        return new \module\service\MailService();
    }

    protected function getRssService() {
        return new \module\service\RssService();
    }

    protected function getAzileService() {
        return new \module\service\AzileService();
    }

}
