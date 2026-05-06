<?php

namespace App\Services;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Services3rd\AlibabaTranslateService;
use Illuminate\Support\Facades\Redis;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of My
 *
 * @author Administrato
 */
class I18nService
{

    public static $models = [
        'App\Models\Announce' => '公告列表',
    ];
    public static $select = [
        'App\Models\Announce' => ['title', 'content'],
    ];
    public static $htmls = [
        'App\Models\Announce' => ['content'],
    ];

    public static function setI18n($id, $cache_key, $fields, $langs = [])
    {

        if (!$langs) {
            $langs = GlobalTool::$LANGS;
        }

        foreach ($langs as $lang => $lang_name) {
            $cache_key_lang = 'i18n_' . $cache_key . '_' . $lang;
            $translates = json_decode(Redis::connection('cache')->hget($cache_key, $id), true);
            foreach ($fields as $k => $v) {
                if ($lang !== 'zh_CN' && $v['value']) {
                    $translation = AlibabaTranslateService::translate($v['value'], 'zh', $lang, $v['format_type'] ?? 'text');
                    $translatedValue = $translation['Data']['Translated'];
                } else {
                    $translatedValue = $v['value'];
                }
                $translates[$k] = $translatedValue;
            }
            Redis::connection('cache')->hset($cache_key_lang, $id, json_encode($translates));
        }
    }


    public static function getI18n($model, $id)
    {
        $cache_key_lang = 'i18n_' . $model . '_' . $GLOBALS['user_lang'];
        return json_decode(Redis::connection('cache')->hget($cache_key_lang, $id), true) ?? [];
    }

    // 获取多个产品翻译
    public static function getBatchI18n($model, $ids)
    {
        $cache_key_lang = 'i18n_' . $model . '_' . $GLOBALS['user_lang'];
        $data = Redis::connection('cache')->hmget($cache_key_lang, $ids);
        $data = array_combine($ids, $data);
        foreach ($data as $key => $value) {
            $data[$key] = json_decode($value, true);
        }
        return $data;
    }

    public static function getTranslateDetail($detail, $model)
    {
        if ($GLOBALS['user_lang'] != 'zh_CN') {
            if ($detail) {
                $i18n = self::getI18n($model, $detail->id);
                $fields = self::$select[$model];
                if ($i18n) {
                    foreach ($fields as $field) {
                        if ($detail->$field) {
                            $detail->$field = $i18n[$field];
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public static function getTranslateList($list, $model)
    {
        if ($GLOBALS['user_lang'] != 'zh_CN') {
            if (count($list) > 0) {
                $i18ns = self::getBatchI18n($model, $list->pluck('id')->toArray());
                $fields = self::$select[$model];
                foreach ($list as $key => &$value) {
                    if ($i18ns[$value->id]) {
                        foreach ($fields as $field) {
                            if ($value->$field) {
                                $value->$field = $i18ns[$value->id][$field];
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }
}
