<?php

namespace App\Services3rd;

use AlibabaCloud\Client\AlibabaCloud;


class AlibabaTranslateService
{

    const DEFAULT_SCENE = 'title';
    const DEFAULT_FORMAT_TYPE = 'text';
    const METHOD = 'POST';

    public static function translate(string $content, string $from, string $to, $format_type = self::DEFAULT_FORMAT_TYPE)
    {
        AlibabaCloud::accessKeyClient(env('alibaba_cloud_access_key'), env('alibaba_cloud_access_secret'))
            ->regionId('cn-hangzhou')
            ->asGlobalClient();

        $result = AlibabaCloud::alimt()
            ->v20181012() // E-commerce version
            ->translateECommerce()
            ->method(self::METHOD) // Set the request POST
            ->withSourceLanguage($from) // source language
            ->withScene(self::DEFAULT_SCENE) // Set the scenario. Product title: title, product description: description, and product communication: communication
            ->withSourceText($content) // The original text.
            ->withFormatType($format_type) // Format of the translated text
            ->withTargetLanguage($to) // target language
            ->request();
        return $result->toArray();
    }
}
