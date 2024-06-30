<?php

namespace module\service;

class UserService extends \stdClass {

    public $rankingPageSize = 50;

    private $ranks = array('peasant', 'acolyte', 'believer', 'follower', 'priest', 'fanatic',
            'deacon', 'bishop', 'stargazer', 'the doctor', 'frost enchanter', 'cardinal',
            'sensei', 'beholder');

    function register($username, $loginid, $password) {
        $ctx = $this->ctx;
        $user = new \stdClass();
        $user->username = $username;
        $user->loginid = $loginid ? $loginid : '??' . rand(10000000, 99999999);
        $user->password = $password ? $ctx->loginService->hashPassword($password) : '' . rand(10000, 99999);
        $user->url = $ctx->strUtils->nameToUrl($username);
        $i = 2;
        while (true) {
            $existing = $ctx->usersDao->findFirst("url = '{$user->url}'");
            if (!is_object($existing)) {
                break;
            }
            $user->url = $ctx->strUtils->nameToUrl($username);
            $user->url .= "-$i";
            $i++;
        }

        $uid = $ctx->usersDao->save($user);

        $role = new \stdClass();
        $role->userid = $uid;
        $role->role = 'user';
        $ctx->rolesDao->save($role);

        $data = new \stdClass();
        $data->userid = $uid;
        $data->rankpos = 1000000000;
        $ctx->userDataDao->save($data);

        $user->id = $uid;

        return $user;
    }

    function checkNewUsername($username, $extended = false) {
        $limit = $extended ? 31 : 15;
        $minLength = $this->shortNameAllowed() ? 4 : 8;
        if (strlen($username) < $minLength || strlen($username) > $limit) {
            return "bad name length";
        }
        $pattern = $extended ? '/^[a-z]+([\_\s][a-z]+){0,2}$/i' : '/^[a-z]+\_?[a-z]*$/i';
        if (preg_match($pattern, $username) !== 1) {
            return "name should consist of letters";
        }
        $user = $this->ctx->usersDao->findFirst("username like '$username'");
        if (is_object($user)) {
            return "name is already in use";
        }
        return null;
    }

    function checkRegisterData($username, $password, $password2, $email) {
        $nameCheck = $this->checkNewUsername($username);
        if ($nameCheck !== null) {
            return $nameCheck;
        }
        $pwdCheck = $this->checkPassword($password, $password2);
        if (!is_null($pwdCheck)) {
            return $pwdCheck;
        }
        $emailCheck = $this->checkEmail($email);
        if (!is_null($emailCheck)) {
            return $emailCheck;
        }
        return null;
    }

    function checkPassword($password, $password2) {
        if (strlen($password) < 8 || strlen($password) > 31) {
            return "Bad password Length";
        }
        if (preg_match('/^[a-z\d]+$/i', $password) !== 1
                || preg_match('/[a-z]/i', $password) !== 1 || preg_match('/\d/', $password) !== 1) {
            return "password should contain Letters and Digits";
        }
        if ($password !== $password2) {
            return "two passwords do not match";
        }
        return null;
    }

    function checkEmail($email) {
        if (strlen($email) < 5 || strlen($email) > 100) {
            return "Bad E-mail length";
        }
        if (preg_match('/[a-z][^\*\@]*\@.*\..*/i', $email) !== 1 || preg_match('/[\s\;\(\)\,]/', $email)) {
            return "Wrong E-mail format";
        }
        return null;
    }

    function rankAsNumber($solved) {
        return $solved >= 305 ? 13 : min((int) (($solved + 15) / 20), count($this->ranks) - 2);
    }

    function rankAsText($solved) {
        return $this->ranks[$this->rankAsNumber($solved)];
    }

    function rank($solved, $text = null) {
        $r = $this->rankAsNumber($solved);
        if ($text === null) {
            $text = $this->ranks[$r];
        }
        return "<span class=\"rank rank$r\">$text</span>";
    }

    function haveSolved($userid, $taskid) {
        return $this->ctx->userTasksDao->getCount("userid = $userid and taskid = $taskid and solved = 1");
    }

    function solvedTasks($userid) {
        $recs = $this->ctx->userTasksDao->makeLookup('taskid', "userid = $userid and solved = 1 and variant = 0");
        if (empty($recs)) {
            return array();
        }
        $ids = array_keys($recs);
        $tasks = $this->ctx->tasksDao->makeLookup('id', "id in (" . implode(',', $ids) . ")");
        $res = array();
        $userTasks = $this->ctx->userTasksDao->find("userid = $userid and solved = 1");
        foreach ($userTasks as $ut) {
            $elem = new \stdClass();
            $elem->url = $tasks[$ut->taskid]->url;
            $elem->title = $tasks[$ut->taskid]->title;
            $elem->taskid = $ut->taskid;
            $elem->language = $ut->language;
            $elem->timestamp = strtotime($ut->ts);
            $elem->ts = $this->ctx->miscService->formatDate($ut->ts);
            $elem->ts2 = $this->ctx->miscService->formatDate($ut->ts, true);
            $res[] = $elem;
        }
        usort($res, function($a, $b) {
            $ta = $a->timestamp - 1300000000;
            $tb = $b->timestamp - 1300000000;
            return $ta - $tb;
        });
        return $res;
    }

    function solvedTaggedTasks($userid, $tag = 'c-1') {
        $res = $this->ctx->userTasksDao->makeLookup('taskid',
            "userid = $userid and solved = 1 and variant = 0 "
            . "and taskid in (select taskid from mess_tasktags where "
            . "tagid = (select id from mess_tags where title = '$tag'))");
        return array_keys($res);
    }

    function additionalRankingData($entry, &$countries) {
        $entry->url = url('user_profile', 'param', $entry->url);
        $entry->rank = $this->rank($entry->solved);
        $entry->points = number_format($entry->points, 2);
        if (empty($entry->country)) {
            $entry->country = 'ZZ';
            $entry->countryTitle = 'Unknown';
        } else {
            $entry->countryTitle = $countries[$entry->country]->title;
        }
    }

    function topOfWeek() {
        $data = $this->ctx->miscService->getTaggedValue('top-of-week');
        if ($data === null || time() - $data->ts > 3600) {
            $data = $this->topOfWeekRecreate();
            $this->ctx->miscService->setTaggedValue('top-of-week', $data);
        }
        return $data->top;
    }

    private function topOfWeekRecreate() {
        $ctx = $this->ctx;
        $top = $ctx->userTasksDao->topOfWeek(13);
        foreach ($top as $rec) {
            $user = $ctx->usersDao->read($rec->userid);
            $userdata = $ctx->userDataDao->findFirst("userid = {$rec->userid}");
            $rec->username = $user->username;
            $rec->url = $user->url;
            $rec->rank = $this->rank($userdata->solved);
        }
        $data = new \stdClass();
        $data->top = $top;
        $data->ts = time();
        return $data;
    }

    function byUrl($url) {
         $ctx = $this->ctx;
         if (!$this->ctx->miscService->validUrlParam($url)) {
            return null;
         }
         return $ctx->usersDao->findFirst("url = '$url'");
    }

    function commentingAllowed() {
        return $this->levelAllowed(5);
    }

    function personalInfoAllowed() {
        return $this->levelAllowed(25);
    }

    function messAllowed() {
        return $this->levelAllowed(5);
    }

    function forumAllowed() {
        return $this->levelAllowed(5);
    }

    function shortNameAllowed() {
        return $this->levelAllowed(125);
    }

    function levelAllowed($level) {
        $ctx = $this->ctx;
        if ($ctx->auth->admin()) {
            return true;
        } else if ($ctx->auth->user()) {
            $userdata = $ctx->userDataDao->findFirst('userid = ' . $ctx->auth->loggedUser());
            return ($userdata->solved >= $level);
        } else {
            return false;
        }
    }
}
