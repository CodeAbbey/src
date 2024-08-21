<?php

namespace module\service;

class LangService extends \stdClass {

    function detectLanguage($s) {
        $cnt = count_chars($s);
        $len = strlen($s);
        $lenBf = strlen(preg_replace('/[^\+\-\.\,\[\]\<\>\:\;\s]/', '', $s));
        if ($lenBf / ($len + 1) > 0.6 && strpos($s, '#include') === false) {
            return 'Brainfuck';
        }
        if (stripos($s, '<-') !== false) {
            return "R";
        }
        if (strpos($s, 'var ') !== false) {
            return strpos($s, 'using ') !== false ? "C#" : "JavaScript";
        }
        if (preg_match('/\bputs|gets\b/', $s) == 1
            && preg_match('/\b\.times|\.each\b/', $s) == 1) {
            return "Ruby";
        }
        if (preg_match('/with\s+ada/i', $s) == 1) {
            return "Ada";
        }
        if (preg_match('/fn\s+main/i', $s) == 1) {
            return "Rust";
        }
        $endMatches = array();
        $endlines = preg_match_all('/[\,\;\{][\040\t]*[\n\r]/', $s, $endMatches);
        if ($endlines / ($cnt[10] + 1) > 0.4) {
            if (strpos($s, '#include') !== false) {
                return "C/C++";
            }
            if (preg_match('/main\s*\(\s*String/', $s) == 1) {
                return "Java";
            }
            if ($cnt[ord('$')] / $len > 0.005) {
                return "PHP";
            }
            return "C#";
        } else {
            if (preg_match('/\([\+]/', $s) == 1) {
                return "LISP";
            }
            if (stripos($s, 'dim ') !== false) {
                return "VB";
            }
            if (stripos($s, 'local ') !== false) {
                return "Lua";
            }
            return "Python";
        }
    }

    function languagesArray() {
        return $this->ctx->elems->conf->languages;
    }

    function preferredLanguage($userid) {
        $table = $this->ctx->userTasksDao->languagesCount($userid);
        $res = '';
        $max = 0;
        foreach ($table as $t) {
            if ($t->cnt > $max) {
                $max = $t->cnt;
                $res = $t->language;
            }
        }
        return $res;
    }

    function preferredLanguageUpdate($userid) {
        $lang = $this->preferredLanguage($userid);
        $data = $this->ctx->userDataDao->findFirst("userid = $userid");
        $data->language = $lang;
        $this->ctx->userDataDao->save($data);
        return $lang;
    }

    function checkAndFixLanguage($language) {
        $languages = $this->languagesArray();
        if (isset($languages[$language])) {
            return $languages[$language];
        } else if (in_array($language, $languages)) {
            return $language;
        }
        return null;
    }

    function changeLanguage($task, $user, &$oldLang, &$language, $confirm, $model) {
        $language = $this->checkAndFixLanguage($language);
        $oldLang = $this->checkAndFixLanguage($oldLang);
        if ($oldLang === null || $language === null) {
            return array(0, "Unknown error");
        }

        $userTaskOld = $this->ctx->userTasksDao->findFirst("taskid = {$task->id} and userid = {$user->id} and language = '$oldLang'");
        $userTaskNew = $this->ctx->userTasksDao->findFirst("taskid = {$task->id} and userid = {$user->id} and language = '$language'");
        if (!is_object($userTaskOld)) {
            return array(0, 'Solution missed');
        }

        if ($confirm) {
            $this->doChangeLanguage($userTaskOld, $userTaskNew, $task->url, $user->url, $language);
            return array(2, 'Language successfully changed');
        } else {
            $this->prepareConfirmation($task, $user, $oldLang, $language, is_object($userTaskNew), $model);
            return array(1);
        }
    }

    private function doChangeLanguage($userTaskOld, $userTaskNew, $taskurl, $userurl, $language) {
        $ctx = $this->ctx;
        if (is_object($userTaskNew)) {
            $userTaskOld->solved = max($userTaskNew->solved, $userTaskOld->solved);
            $userTaskOld->variant = min($userTaskNew->variant, $userTaskOld->variant);
        }
        $userTaskOld->language = $language;
        $ctx->userTasksDao->save($userTaskOld);
        if (is_object($userTaskNew)) {
            $this->deleteDuplicateSolution($userTaskOld, $userTaskNew);
        }
    }

    private function deleteDuplicateSolution($userTaskOld, $userTaskNew) {
        $ctx = $this->ctx;
        $votes = $ctx->codelikesDao->find("codeid = {$userTaskNew->id}");
        foreach ($votes as $vote) {
            $vote->codeid = $userTaskOld->id;
            $ctx->codelikesDao->save($vote);
        }
        $ctx->userTasksDao->delete($userTaskNew->id);
    }

    private function prepareConfirmation($task, $user, $oldlang, $language, $overwrite, $model) {
        $model->task = $task;
        $model->username = $user->username;
        $model->userurl = $user->url;
        $model->oldlang = $oldlang;
        $model->newlang = $language;
        $model->overwrite = $overwrite;
        $model->backurl = url('task_solution', 'task', $task->url, 'user', $user->url, 'lang', urlencode($oldlang));
    }
}
