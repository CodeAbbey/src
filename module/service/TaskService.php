<?php

namespace module\service;

class TaskService extends \stdClass {

    function tagNamesForTask($taskid) {
        $tags = $this->ctx->tagsDao->makeLookup('id');
        $taskTags = $this->ctx->taskTagsDao->find("taskid = $taskid");
        $res = array();
        foreach ($taskTags as $tt) {
            $res[] = $tags[$tt->tagid]->title;
        }
        return $res;
    }

    function loadTasks($tagid) {
        if ($tagid == null) {
            return $this->ctx->taskListDao->find();
        }
        $taskTags = $this->ctx->taskTagsDao->makeLookup("taskid", "tagid = $tagid");
        $taskids = implode(',', array_keys($taskTags));
        return $this->ctx->taskListDao->find("id in ($taskids)");
    }

    public function unsolvedTasks($userId) {
        $userTasks = $this->ctx->userTasksDao->makeLookup('taskid', "userid = $userId and solved > 0");
        if (count($userTasks) > 0) {
            $ids = implode(',', array_keys($userTasks));
        } else {
            $ids = '-1';
        }
        $tasks = $this->ctx->tasksDao->find("id not in ($ids) and shown = 1");
        return $tasks;
    }

    private function executeChecker($code, $expected = null, $answer = null, $solution = null, $lang = null) {
        if (substr($code, 0, 7) === '#python') {
            $ver = substr($code, 1, 7);
            return $this->executePyChecker($code, $ver, $expected, $answer);
        }
        if (substr($code, 0, 5) === '#perl') {
            return $this->executePerlChecker($code);
        }
        if (!function_exists('checker')) {
            $code = str_replace(array('<?php', '?>'), '', $code);
            eval($code);
        }
        if ($expected == null) {
            $result = checker();
        } else {
            $checkerReflection = new \ReflectionFunction('checker');
            switch ($checkerReflection->getNumberOfParameters()) {
                case 3: $result = checker($expected, $answer, $solution); break;
                case 4: $result = checker($expected, $answer, $solution, $lang); break;
                default: $result = checker($expected, $answer); break;
            }
        }
        return $result;
    }

    function executePyChecker($code, $pyver, $expected = null, $answer = null) {
      $tmp = tmpfile();
      $fname = stream_get_meta_data($tmp)['uri'];
      if ($expected !== null) {
          fwrite($tmp, "expected=\"\"\"$expected\"\"\"\n");
      }
      if ($answer !== null) {
          fwrite($tmp, "answer=\"\"\"$answer\"\"\"\n");
      }
      fwrite($tmp, $code);
      fflush($tmp);
      $res = array();
      $retcode = -1;
      if (isset($this->ctx->elems->conf->interpreters[$pyver])) {
          $pyver = $this->ctx->elems->conf->interpreters[$pyver];
      }
      exec("$pyver $fname 2>&1", $res, $retcode);
      fclose($tmp);
      if ($retcode !== 0) {
          return array(
              'Input generation error - tell admin please:'
                  . implode("\n", $res), 'tubz');
      }
      $ans = '';
      while (count($res) > 0) {
          $ans = array_pop($res);
          if ($ans !== '') {
              break;
          }
      }
      if ($expected !== null) {
          return $ans;
      }
      return array(implode("\n", $res), $ans);
    }

    function executePerlChecker($code) {
      $tmp = tmpfile();
      $fname = stream_get_meta_data($tmp)['uri'];
      fwrite($tmp, $code);
      fflush($tmp);
      $res = array();
      $code = -1;
      exec('perl ' . $fname, $res, $code);
      fclose($tmp);
      if ($code !== 0) {
          return array(
              'Input generation error - tell admin please', 'tubz');
      }
      $ans = '';
      while (count($res) > 0) {
          $ans = array_pop($res);
          if ($ans !== '') {
              break;
          }
      }
      return array(implode("\n", $res), $ans);
    }

    function testChecker($code) {
        try {
            $res = $this->executeChecker($code);
            if (is_string($res)) {
                $res = ['n/a', $res];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return "{$res[0]}\n\n{$res[1]}";
    }

    public function arenaChecker($taskid, $sol1text, $sol2text) {
        $code = $this->loadChecker($taskid);
        return $this->executeChecker($code, '#php ', null, array($sol1text, $sol2text));
    }

    public function loadChecker($taskid) {
        $data = $this->ctx->taskDataDao->findFirst("taskid = $taskid and type = 'checker'");
        return base64_decode($data->data);
    }

    function testData($taskid) {
        $code = $this->loadChecker($taskid);
        $result = $this->executeChecker($code);
        return $result;
    }

    function prepareData($taskid) {
        $code = $this->loadChecker($taskid);
        $result = $this->executeChecker($code);
        $this->ctx->util->sessionPut("expected-answer-$taskid", (string) $result[1]);
        $this->ctx->util->sessionPut("input-data-$taskid", (string) $result[0]);
        return $result[0];
    }

    function loadNotes($taskid, $full = false) {
        $source = $this->ctx->taskDataDao->findFirst("taskid = $taskid and type = 'notes'");
        if ($source) {
            $source = base64_decode($source->data);
            $notes = $this->ctx->markdown->parse($source);
        } else {
            $notes = '';
        }
        if ($full) {
            $res = new \stdClass();
            $res->notes = $notes;
            $res->source = $source ? $source : '';
            return $res;
        }
        return $notes;
    }

    function verifyAnswer($taskid, $answer, $solution, $userid, $lang) {
        $expected = $this->ctx->util->sessionGet("expected-answer-$taskid");
        if ($expected === null) {
            return false;
        }

        if (in_array(substr($expected, 0, 5), array('#php ', '#pyx '))) {
            return $this->verifyWithChecker($taskid, $expected, $answer, $solution, $userid, $lang);
        }

        if (substr($expected, 0, 2) != "' ") {
            $answer = strtolower(preg_replace('/\s+/', ' ', trim($answer)));
            if (substr($expected, 0, 2) == '. ') {
                return $this->verifyFloatAnswer(substr($expected, 2), $answer);
            } else {
                $expected = trim(strtolower($expected));
            }
        } else {
            $expected = substr($expected, 2);
        }
        return $expected == $answer;
    }

    private function verifyWithChecker($taskid, $expected, $answer, $solution, $userid, $lang) {
        $code = $this->loadChecker($taskid);
        $result = $this->executeChecker($code, $expected, $answer, $solution, $lang);
        $this->ctx->util->sessionPut("expected-answer-$taskid", $result);
        $ok = preg_match('/^ok(\s.*)?$/', $result);
        if ($ok && $this->ctx->challengeService->challengeExists($taskid)) {
            $this->ctx->challengeService->processResult($taskid, $userid, substr($result, 3));
        }
        return $ok;
    }

    function deleteAnswer($taskid) {
        $a = $this->ctx->util->sessionGet("expected-answer-$taskid");
        $this->ctx->util->sessionDel("expected-answer-$taskid");
        return $a;
    }

    function deleteInputData($taskid) {
        $a = $this->ctx->util->sessionGet("input-data-$taskid");
        $this->ctx->util->sessionDel("input-data-$taskid");
        return $a;
    }

    private function verifyFloatAnswer($expected, $answer) {
        $expected = explode(' ', $expected);
        $answer = explode(' ', $answer);
        $n = sizeof($expected);
        if (sizeof($answer) != $n) {
            return false;
        }
        for ($i = 0; $i < $n; $i++) {
            $v = (float) $expected[$i];
            $eps = max(abs($v), 1) * 1e-7;
            if (abs($v - floatval($answer[$i])) > $eps) {
                return false;
            }
        }

        return true;
    }

    function processSolution($task, $userid, $answer, $solution, $language) {
        list($solved, $prevSolved, $language) =
                $this->processSolutionDetermineState($userid, $task->id, $answer, $solution, $language);

        $userTask = $this->processSolutionPrepareUserTask($userid, $task->id, $solved, $prevSolved, $language);
        $userTaskId = $this->processSolutionSaveUserTask($userTask, $prevSolved);
        $this->processSolutionSaveSolution($userTaskId, $solution);
        $this->processSolutionUpdateTask($solved, $prevSolved, $task);
        $userdata = $this->processSolutionUpdateUserData($solved, $prevSolved, $userid, $task);

        return array($solved, !$prevSolved && $solved ? $task->cost : 0, $userdata->points);
    }

    private function processSolutionDetermineState($userid, $taskid, $answer, $solution, $language) {
        if (empty($language)) {
            $language = $this->ctx->langService->detectLanguage($solution);
        }
        $solved = $this->ctx->taskService->verifyAnswer($taskid, $answer, $solution, $userid, $language);
        $solCount = $this->ctx->userTasksDao->getCount("userid = $userid and taskid = $taskid and solved > 0");
        $prevSolved = $solCount > 0;
        return array($solved, $prevSolved, $language);
    }

    private function processSolutionPrepareUserTask($userid, $taskid, $solved, $prevSolved, $language) {
        $record = $this->ctx->userTasksDao->findFirst("userid = $userid and taskid = $taskid and language = '$language'");
        if (!is_object($record)) {
            $record = new \stdClass();
            $record->taskid = $taskid;
            $record->userid = $this->ctx->auth->loggedUser();
            $record->solved = 0;
            $record->language = $language;
            $record->ts = null;
        }
        if ($record->solved != 1) {
            $record->solved = $solved ? 1 : -1;
            $record->variant = $prevSolved || !$solved ? 1 : 0;
        }
        return $record;
    }

    private function processSolutionSaveUserTask($userTask, $prevSolved) {
        if (!$prevSolved || empty($userTask->ts)) {
            $userTask->ts = date('Y-m-d H:i:s');
        }
        return $this->ctx->userTasksDao->save($userTask);
    }

    private function processSolutionSaveSolution($userTaskId, $solution) {
        $sol = $this->ctx->solutionsDao->findFirst("usertaskid = $userTaskId");
        if (!is_object($sol)) {
            $sol = new \stdClass();
            $sol->usertaskid = $userTaskId;
            $sol->viewkey = mt_rand();
        }
        $sol->solution = base64_encode($solution);
        return $this->ctx->solutionsDao->save($sol);
    }

    private function processSolutionUpdateTask($solved, $prevSolved, $task) {
        if (!$prevSolved && $solved) {
            $task->solved += 1;
            $this->ctx->tasksDao->save($task);
        }
    }

    private function processSolutionUpdateUserData($solved, $prevSolved, $userid, $task) {
        $userdata = $this->ctx->userDataDao->findFirst("userid = $userid");
        if ($prevSolved) {
            return $userdata;
        }
        if ($solved) {
            $userdata->solved += 1;
            $userdata->points += $task->cost;
        } else {
            $userdata->failed += 1;
        }
        $userdata->language = $this->ctx->langService->preferredLanguage($userid);
        $this->ctx->userDataDao->save($userdata);
        return $userdata;
    }

    function isAdminOrAuthor($task) {
        if ($this->ctx->auth->admin()) return true;
        $userid = $this->ctx->auth->loggedUser();
        if (!$userid) return false;
        if (is_numeric($task)) $task = $this->ctx->tasksDao->read($task);
        if (!is_object($task)) return false;
        $user = $this->ctx->usersDao->read($userid);
        return $task->author == $user->url;
    }

    function viewSolution($task, $user, $language, $nocheck) {

        $data = $this->viewSolutionLoadAndCheck($task, $user, $language, $nocheck);

        $res = new \stdClass();
        $res->taskTitle = $task->title;
        $res->taskUrl = $task->url;
        $res->taskId = $task->id;
        $res->userName = $user->username;
        $res->userUrl = $user->url;
        $res->error = $data['error'];
        $res->ts = '';
        if (!$res->error) {
            $res->userTaskId = $data['utid'];
            $res->code = htmlentities($data['soltext'], $flags=ENT_IGNORE, $encoding='UTF-8');
            $res->code = str_replace("\t", "    ", $res->code);
            $res->language = $data['utlang'];
            $res->ts = $this->ctx->miscService->formatDate($data['ts'], true);
        }
        return $res;
    }

    private function viewSolutionLoadAndCheck($task, $user, $language, $nocheck) {
        $ctx = $this->ctx;
        $taskid = $task->id;
        $userid = $user->id;
        $data = array('utid' => null, 'utlang' => null, 'soltext' => null, 'ts' => null, 'error' => null);

        $currentUser = $ctx->auth->loggedUser();
        $nosolview = $ctx->miscService->getTaggedValue("nosol-$taskid");
        $ownerOrAdmin = ($currentUser == $userid || $ctx->auth->admin());
        if ($nosolview && !$ownerOrAdmin && !$nocheck) {
            $data['error'] = "Sorry, for this task solutions are hidden "
                . "due to conspiracy!";
            return $data;
        }

        if (!$nocheck && !$this->viewSolutionCheckAllowed($task, $user, $currentUser)) {
            $data['error'] = "Please, solve this task yourself first\n    "
                . "then you will be able to see other's solutions!";
            return $data;
        }

        if ($ctx->challengeService->challengeExists($taskid)) {
            if (!$ownerOrAdmin) {
                $data['error'] = "Viewing solutions for challenge tasks is not allowed to avoid plagiarism!";
                return $data;
            }
        }

        try {
            list($usertask, $sol) = $this->loadSolution($taskid, $userid, $language);
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
            return $data;
        }

        $data['utid'] = $usertask->id;
        $data['utlang'] = $usertask->language;
        $data['ts'] = $usertask->ts;
        $data['soltext'] = $sol->solution;
        return $data;
    }

    public function loadSolution($taskid, $userid, $language) {
        $usertask = $this->ctx->userTasksDao->findFirst("taskid = {$taskid} and userid = {$userid} and language = '$language'");
        if (!is_object($usertask)) {
            throw new \Exception("Looks like this user did not solve this task!");
        }
        $sol = $this->ctx->solutionsDao->findFirst("usertaskid = {$usertask->id}");
        if (!is_object($sol)) {
            throw new \Exception("Strange, but there is no such solution!");
        }
        $sol->solution = base64_decode($sol->solution);
        return array($usertask, $sol);
    }

    private function viewSolutionCheckAllowed($task, $userid, $currentUser) {
        if ($this->ctx->auth->admin() || $userid == $currentUser) {
            return true;
        }
        $viewer = $this->ctx->usersDao->read($currentUser);
        if ($task->author == $viewer->url) {
            return true;
        }
        $solved = $this->ctx->userTasksDao->getCount(
                "taskid = {$task->id} and userid = $currentUser and solved > 0");
        return $solved;
    }

    public function loadStatement($id, $locale) {
        if (empty($locale)) {
            $st = $this->ctx->taskDataDao->findFirst("taskid = $id and type = 'text'");
            $md = base64_decode($st->data);
        } else {
            $md = @file_get_contents("https://github.com/CodeAbbey/Translations/raw/master/$locale/task-$id.md",
                    false, stream_context_create(array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false))));
            if ($md === false) {
                return null;
            }
        }
        return $this->ctx->markdown->parse($md);
    }

    function byUrl($url) {
         $ctx = $this->ctx;
         if (!$this->ctx->miscService->validUrlParam($url)) {
            return null;
         }
         return $ctx->tasksDao->findFirst("url = '$url'");
    }

}
