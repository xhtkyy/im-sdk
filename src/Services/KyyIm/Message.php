<?php

namespace KyyIM\Services\KyyIm;

use KyyIM\Interfaces\MessageInterface;
use KyyTools\Facades\Lang;

class Message extends AbstractKyyIm implements MessageInterface {

    use ImRequest;

    /**
     * 发送通知
     * @param array $user_ids
     * @param $template
     * @return array
     */
    public function notice(array $user_ids, $template): array {
        try {
            return $this->requestHttp("POST", '/im/messages/notices', [
                "user_ids" => array_filter($user_ids),
                "notice"   => Lang::trans2tw($template)
            ]);
        } catch (\Exception $exception) {
            return [
                "code"    => self::STATUS_FAIL,
                "message" => '消息请求发送失败'
            ];
        }
    }
}
