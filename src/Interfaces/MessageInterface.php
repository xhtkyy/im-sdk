<?php

namespace KyyIM\Interfaces;

use KyyIM\Template\TemplateInterface;

/**
 * 消息
 * Interface ImMessageInterface
 */
interface MessageInterface
{
    // 消息通知
    public function notice(array $user_ids, array $notice = [], int $typ = 1): array;
}
