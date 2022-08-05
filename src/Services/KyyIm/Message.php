<?php

namespace KyyIM\Services\KyyIm;

use KyyIM\Interfaces\MessageInterface;
use KyyIM\Template\TemplateInterface;
use KyyTools\Facades\Lang;

class Message extends AbstractKyyIm implements MessageInterface {

    use ImRequest;

    /**
     * 发送通知
     * @param array $user_ids
     * @param TemplateInterface $template
     * @return array
     */
    public function template(array $user_ids, TemplateInterface $template): array {
        return $this->notice(array_unique($user_ids), Lang::trans2tw($template->toArray()));
    }

    /**
     * 通知接口
     * @param array $user_ids
     * @param array $notice
     * @param int $typ
     * @return array
     */
    public function notice(array $user_ids, array $notice = [], int $typ = 1): array {
        try {
            return $this->requestHttp("POST", '/im/messages/notices', [
                "typ"      => $typ,
                "user_ids" => array_filter($user_ids),
                "notice"   => $notice
            ]);
        } catch (\Exception $exception) {
            return [
                "code"    => self::STATUS_FAIL,
                "message" => '消息请求发送失败'
            ];
        }
    }
}
