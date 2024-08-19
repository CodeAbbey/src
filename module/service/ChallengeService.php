<?php

namespace module\service;

class ChallengeService extends \stdClass {

    private $maxScore = 7;

    function challengeExists($taskid) {
        return $this->ctx->challengesDao->getCount("taskid = $taskid and userid = 0") != 0;
    }

    function arenaExists($taskid) {
        $ch = $this->ctx->challengesDao->findFirst("taskid = {$taskid} and userid = 0");
        return is_object($ch) && preg_match('/\+arena$/', $ch->notes) > 0;
    }

    function processResult($taskid, $userid, $result) {
        $result = explode(' ', $result, 2);
        $score = (float) $result[0];
        $notes = $result[1];
        $this->updateResult($taskid, $userid, $score, $notes);
        $this->recalculate($taskid);
    }

    function updateResult($taskid, $userid, $score, $notes) {
        $ctx = $this->ctx;
        $chlng = $ctx->challengesDao->findFirst("taskid = $taskid and userid = $userid");
        if (!is_object($chlng)) {
            $chlng = new \stdClass();
            $chlng->taskid = $taskid;
            $chlng->userid = $userid;
        }
        $chlng->score = $score;
        $chlng->notes = $notes;
        $ctx->challengesDao->save($chlng);
    }

    function recalculate($taskid) {
        $ctx = $this->ctx;
        $stats = $this->loadAndSort($taskid);
        if (empty($stats)) {
            return;
        }
        $prev = INF;
        $count = -1;
        foreach ($stats as $entry) {
            $count++;
            if ($prev - $entry->score > $entry->score * 1e-6) {
                $prev = $entry->score;
                $curCount = $count;
            }
            $entry->total = $this->maxScore * exp(- $curCount / 16);
            $ctx->challengesDao->save($entry);
        }
    }

    function loadStats($taskid) {
        $ctx = $this->ctx;
        $stats = $this->loadAndSort($taskid);
        if (empty($stats)) {
            return $stats;
        }
        $userIds = $ctx->miscService->listIds($stats, 'userid');
        $users = $ctx->usersDao->makeLookup('id', "id in ($userIds)");
        foreach ($stats as $entry) {
            $user = $users[$entry->userid];
            $entry->username = $user->username;
            $entry->userurl = $user->url;
        }
        return $stats;
    }

    function loadAndSort($taskid) {
        $stats = $this->ctx->challengesDao->find("taskid = $taskid and userid <> 0");
        if (empty($stats)) {
            return $stats;
        }
        usort($stats, array('\module\service\ChallengeService', 'statsSort'));
        return $stats;
    }

    static function statsSort($a, $b) {
        if ($a->score > $b->score) {
            return -1;
        } else if ($a->score < $b->score) {
            return 1;
        }
        return 0;
    }

}

