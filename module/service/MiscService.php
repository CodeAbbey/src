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

    function getTaggedValue($tag) {
        return null;
    }

    function postToMessHall($userid, $message) {
    }

    function logAction($userid, $message) {
    }

}

