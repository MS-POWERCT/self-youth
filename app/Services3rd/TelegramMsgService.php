<?php

namespace App\Services3rd;

use Illuminate\Support\Facades\Log;

/**
 * Telegram 消息模板服务
 *
 * 用于统一管理各种 Telegram 消息通知的格式化模板
 */
class TelegramMsgService
{

    /**
     * 单例模式获取 TelegramService 实例
     *
     * @return TelegramService
     */
    private static function getTelegramService()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new TelegramService();
        }
        return $instance;
    }

    /**
     * 最基本的发送文本
     *
     * @param string $text 要发送的文本
     * @return array|false 发送结果
     */
    public static function sendText($text)
    {
        return self::getTelegramService()->sendMessage($text);
    }
}
