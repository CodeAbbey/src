<?php

namespace module\service;

class MiscService extends \stdClass {

    function formatTitle() {
        if (empty($this->ctx->elems->title)) {
            return 'CodeAbbey - programming problems to practice and learn for beginners';
        }

        return "{$this->ctx->elems->title} - CodeAbbey";
    }

    function formatDescription() {
        if (!empty($this->ctx->elems->description)) {
            return $this->ctx->elems->description;
        }
        if (empty($this->ctx->elems->title)) {
            return 'Collection of programming problems to practice solving, learn to program and code, and win certificates';
        }
        $title = preg_replace('/[\"\&]/', '', $this->ctx->elems->title);
        return "$title - Programming problems for beginners";
    }

    function formatKeywords() {
        if (!empty($this->ctx->elems->keywords)) {
            $keywords = 'programming,problems,practice,beginner,learn';
            foreach ($this->ctx->elems->keywords as $kw) {
                $keywords .= ',' . str_replace('-', ' ', $kw);
            }
            return $keywords;
        } else {
            return 'programming,education,problems,exercises,projects,solving,learning,studying,beginner,practice';
        }
    }

    function validUrlParam($url) {
        return $url === null || preg_match('/^[a-z0-9\-\_]+$/', $url);
    }

    function setTaggedValue($tag, $val) {
        $record = $this->ctx->tagValsDao->findFirst("tag = '$tag'");
        if (!is_object($record)) {
            $record = new \stdClass();
            $record->tag = $tag;
        } else if ($val === null) {
            $this->ctx->tagValsDao->delete($record->id);
            return;
        }
        $record->val = base64_encode(serialize($val));
        $this->ctx->tagValsDao->save($record);
    }

    function getTaggedValue($tag) {
        $record = $this->ctx->tagValsDao->findFirst("tag = '$tag'");
        if (!is_object($record)) {
            return null;
        }
        return unserialize(base64_decode($record->val));
    }

    function getTaggedValues($prefix) {
        $records = $this->ctx->tagValsDao->find("tag like '$prefix%'");
        $res = array();
        foreach ($records as $rec) {
            $res[$rec->tag] = unserialize(base64_decode($rec->val));
        }
        return $res;
    }

    function postToMessHall($userid, $message) {
    }

    function logAction($userid, $message) {
    }

}

