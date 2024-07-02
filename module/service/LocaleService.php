<?php

namespace module\service;

class LocaleService extends \stdClass {

    public $LOCALES_ARRAY;

    function __construct() {
        $this->LOCALES_ARRAY = array('ar' => 'Arabic', 'es' => 'Spanish',
            'fr' => 'French', 'de' => 'German', 'ro' => 'Romanian',
            'ru' => 'Russian', 'sk' => 'Slovak', 'zh' => 'Chinese',
            'ua' => 'Ukrainian');
    }

    function parseLocale($url) {
        if (strpos($url, '--') == false) {
            return array($url, '');
        }
        $res = explode('--', $url, 2);
        if (!in_array($res[1], array_keys($this->LOCALES_ARRAY))) {
            $res[0] = '';
        }
        return $res;
    }

    public function availableTaskLocales($id) {
        $loc = $this->ctx->miscService->getTaggedValue("tran-$id");
        if (empty($loc)) {
            return array();
        }
        $ava = $this->LOCALES_ARRAY;
        $loc = explode(' ', $loc);
        $res = array();
        foreach ($loc as $k) {
            if (isset($ava[$k])) {
                $res[$k] = $ava[$k];
            }
        }
        return $res;
    }

    public function direction($locale) {
        return $locale != 'ar' ? 'ltr' : 'rtl';
    }
}

