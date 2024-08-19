<?php

namespace module\service;

class ForumService extends \stdClass{

    function loadTopics($forumId) {
        $ctx = $this->ctx;
        $topics = $ctx->forumTopicsDao->find(
            $forumId !== null ? "forumid = $forumId" : null);

        foreach ($topics as $topic) {
            $topic->created = $ctx->miscService->formatDate($topic->created);
            $topic->ts = strtotime($topic->lastpost);
            $topic->lastpost = $ctx->miscService->formatDate($topic->lastpost, true);
            $lastPost = $ctx->forumPostsDao->read($topic->lastpostid);
            $topic->lastUserId = $lastPost->userid;
        }

        usort($topics, function($a, $b) {
            return $b->ts - $a->ts;
        });

        $this->addUserInfo($topics);
        $this->addUserInfo($topics, 'lastUserId', 'lastUser');

        return $topics;
    }

    function loadTopicByUrl($url) {
        $ctx = $this->ctx;
        if (empty($url) || !$ctx->miscService->validUrlParam($url)) {
            return null;
        }
        $topic = $ctx->forumTopicsDao->findFirst("url = '$url'");
        if (!is_object($topic)) {
            return null;
        }
        return $topic;
    }

    function loadForumByUrl($url) {
        $ctx = $this->ctx;
        if (empty($url) || !$ctx->miscService->validUrlParam($url)) {
            return null;
        }
        $forum = $ctx->forumsDao->findFirst("url = '$url'");
        if (!is_object($forum)) {
            return null;
        }
        return $forum;
    }

    function privateTopicRestriction($taskId) {
        $ctx = $this->ctx;
        $task = $ctx->tasksDao->read($taskId);
        $userId = $ctx->auth->loggedUser();
        if (!$userId) {
            return $task->url;
        }
        if ($ctx->auth->admin() || $ctx->userService->haveSolved($userId, $taskId)) {
            return null;
        }
        if ($task->author) {
            $user = $ctx->usersDao->read($userId);
            if ($user->url == $task->author) {
            	return null;
            }
        }
        return $task->url;
    }

    function loadPosts($topic) {
        $ctx = $this->ctx;
        $posts = $ctx->forumPostsDao->find("topicid = {$topic->id}");

        $this->ctx->markdown->no_markup = true;
        foreach ($posts as $post) {
            $post->isauthor = ($topic->userid == $post->userid);
            $post->post = $this->ctx->markdown->parse(base64_decode($post->post));
            $post->ts = strtotime($post->created);
            $post->createdStr = $ctx->miscService->formatDate($post->created, true);
        }

        usort($posts, function($a, $b) {
            return $a->ts - $b->ts;
        });

        $this->addUserInfo($posts);
        $this->addEditingLinks($posts);

        return $posts;
    }

    private function addUserInfo(&$entities, $userIdProperty = 'userid', $userProperty = 'user') {
        $users = array();
        foreach ($entities as $entity) {
            $userid = $entity->$userIdProperty;
            if (!isset($users[$userid])) {
                $user = $this->ctx->usersDao->read($userid);
                $userdata = $this->ctx->userDataDao->findFirst("userid = $userid");
                $u = new \stdClass();
                if (!empty($userdata)) {
                    $u->username = $this->ctx->userService->rank($userdata->solved, $user->username);
                    $u->userurl = url('user_profile', 'param', $user->url);
                    $u->avatar = $userdata->avatar;
                } else {
                    $u->username = '<span class="strong hint">Rodion (admin)</span>';
                    $u->userurl = url('wiki', 'param', 'copyright');
                    $u->avatar = 'https://i.imgur.com/tAobHN7.jpg';
                }
                $users[$userid] = $u;
            }
            $entity->$userProperty = $users[$userid];
        }
    }

    public function recentList($forumsLookup=null) {
        $ctx = $this->ctx;
        if ($forumsLookup === null) $forumsLookup = $ctx->forumsDao->makeLookup();
        $topics = $ctx->forumTopicsDao->recent();
        foreach ($topics as $topic) {
            $topic->lastpost = $ctx->miscService->formatDate($topic->lastpost, true);
            $topic->forum = $forumsLookup[$topic->forumid];
            $lastPost = $ctx->forumPostsDao->read($topic->lastpostid);
            $topic->lastUserId = $lastPost->userid;
        }
        $this->addUserInfo($topics);
        $this->addUserInfo($topics, 'lastUserId', 'lastUser');
        return $topics;
    }

    private function addEditingLinks($posts) {
        $ctx = $this->ctx;
        $userId = $ctx->auth->loggedUser();
        if (!$userId) {
            return;
        }
        $isAdmin = $ctx->auth->admin();
        $curTime = time();
        foreach ($posts as $post) {
            if (!$isAdmin) {
                if ($post->userid != $userId) {
                    continue;
                }
                $editingAllowed = $this->editingAllowed(strtotime($post->created), $curTime);
                if (!$editingAllowed) {
                    continue;
                }
                $post->editingHint = "Editing allowed for $editingAllowed min";
            } else {
                $post->editingHint = 'Always allowed';
            }
            $post->editing = url('forum_edit', 'param', $ctx->miscService->encryptInt($post->id));
        }
    }

    public function editingAllowed($postTime, $curTime = null) {
        if ($curTime === null) {
            $curTime = time();
        }
        $curTime -= $postTime;
        $curTime = 60 - floor($curTime / 60);
        return $curTime > 0 ? $curTime : 0;
    }

    public function stripTopicTitle($title) {
        return preg_replace('/[^A-Za-z0-9\-\_\s\(\)\[\]\?]/', '', $title);
    }

    public function resourcesForCodeMirror() {
        $ctx = $this->ctx;
        $ctx->elems->styles[] = 'codemirror';
        $ctx->elems->scripts[] = '_cm/codemirror';
        $ctx->elems->scripts[] = '_cm/markdown';
        $ctx->elems->scripts[] = '_cm/xml';
    }

}
