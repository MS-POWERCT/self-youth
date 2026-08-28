<?php

namespace App\Services3rd;

use App\Services\ApiService;
use Exception;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;
    protected $baseUrl;

    public function __construct()
    {
        // 请进行配置 TELEGRAM_BOT_TOKEN 和 TELEGRAM_CHAT_ID
        $this->botToken = config('app.telegram_bot_token');
        $this->chatId = config('app.telegram_chat_id');
        $this->baseUrl = 'https://api.telegram.org/bot' . $this->botToken;

        // 验证必要的配置
        if (empty($this->botToken)) {
            throw new Exception('TELEGRAM_BOT_TOKEN 未配置');
        }
        if (empty($this->chatId)) {
            throw new Exception('TELEGRAM_CHAT_ID 未配置');
        }
    }

    /**
     * 发送文本消息
     *
     * @param string $message 要发送的消息
     * @param string|null $chatId 可选的聊天ID，如果不提供则使用默认配置
     * @param array $options 额外的选项（如 parse_mode, disable_web_page_preview 等）
     * @return array|false 返回API响应或false（失败时）
     */
    public function sendMessage($message, $chatId = null, $options = [])
    {

        // 本地不发送
        if (config('app.env') == 'local') {
            return true;
        }

        try {
            $targetChatId = $chatId ?: $this->chatId;

            if (empty($targetChatId)) {
                throw new Exception('聊天ID不能为空');
            }

            if (empty($message)) {
                throw new Exception('消息内容不能为空');
            }

            // 构建URL参数
            $params = [
                'chat_id' => $targetChatId,
                'text' => $message
            ];

            // 合并额外选项
            $params = array_merge($params, $options);

            // 构建完整的API URL
            $url = $this->baseUrl . '/sendMessage?' . http_build_query($params);


            $response = ApiService::json_get($url);

            if (!$response || !isset($response['ok']) || !$response['ok']) {
                Log::error('Telegram消息发送失败', [
                    'response' => $response,
                    'message' => $message
                ]);
                return false;
            }

            // Log::info('Telegram消息发送成功', [
            //     'message_id' => $response['result']['message_id'] ?? null
            // ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Telegram消息发送异常', [
                'error' => $e->getMessage(),
                'message' => $message
            ]);
            return false;
        }
    }

    /**
     * 发送图片消息
     *
     * @param string $photoUrl 图片URL
     * @param string|null $caption 图片说明文字
     * @param string|null $chatId 可选的聊天ID
     * @return array|false
     */
    public function sendPhoto($photoUrl, $caption = null, $chatId = null)
    {
        try {
            $targetChatId = $chatId ?: $this->chatId;

            $params = [
                'chat_id' => $targetChatId,
                'photo' => $photoUrl
            ];

            if ($caption) {
                $params['caption'] = $caption;
            }

            $url = $this->baseUrl . '/sendPhoto?' . http_build_query($params);

            return ApiService::json_get($url);
        } catch (Exception $e) {
            Log::error('Telegram图片发送异常', [
                'error' => $e->getMessage(),
                'photo_url' => $photoUrl
            ]);
            return false;
        }
    }

    /**
     * 发送文档
     *
     * @param string $documentUrl 文档URL
     * @param string|null $caption 文档说明
     * @param string|null $chatId 可选的聊天ID
     * @return array|false
     */
    public function sendDocument($documentUrl, $caption = null, $chatId = null)
    {
        try {
            $targetChatId = $chatId ?: $this->chatId;

            $params = [
                'chat_id' => $targetChatId,
                'document' => $documentUrl
            ];

            if ($caption) {
                $params['caption'] = $caption;
            }

            $url = $this->baseUrl . '/sendDocument?' . http_build_query($params);

            return ApiService::json_get($url);
        } catch (Exception $e) {
            Log::error('Telegram文档发送异常', [
                'error' => $e->getMessage(),
                'document_url' => $documentUrl
            ]);
            return false;
        }
    }

    /**
     * 获取机器人信息
     *
     * @return array|false
     */
    public function getMe()
    {
        try {
            $url = $this->baseUrl . '/getMe';
            return ApiService::json_get($url);
        } catch (Exception $e) {
            Log::error('获取Telegram机器人信息异常', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 获取更新
     *
     * @param int $offset 偏移量
     * @param int $limit 限制数量
     * @return array|false
     */
    public function getUpdates($offset = 0, $limit = 100)
    {
        try {
            $params = [
                'offset' => $offset,
                'limit' => $limit
            ];

            $url = $this->baseUrl . '/getUpdates?' . http_build_query($params);
            return ApiService::json_get($url);
        } catch (Exception $e) {
            Log::error('获取Telegram更新异常', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 设置Webhook
     *
     * @param string $webhookUrl Webhook URL
     * @return array|false
     */
    public function setWebhook($webhookUrl)
    {
        try {
            $params = [
                'url' => $webhookUrl
            ];

            $url = $this->baseUrl . '/setWebhook?' . http_build_query($params);
            return ApiService::json_get($url);
        } catch (Exception $e) {
            Log::error('设置Telegram Webhook异常', [
                'error' => $e->getMessage(),
                'webhook_url' => $webhookUrl
            ]);
            return false;
        }
    }

    /**
     * 删除Webhook
     *
     * @return array|false
     */
    public function deleteWebhook()
    {
        try {
            $url = $this->baseUrl . '/deleteWebhook';
            return ApiService::json_get($url);
        } catch (Exception $e) {
            Log::error('删除Telegram Webhook异常', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
