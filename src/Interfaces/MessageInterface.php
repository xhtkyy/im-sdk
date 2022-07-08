<?php

namespace KyyIM\Interfaces;

use App\Im\Template\TemplateInterface;

/**
 * 消息
 * Interface ImMessageInterface
 * @package App\Im\Contracts
 */
interface MessageInterface
{
    // 消息通知
    public function notice(array $user_ids, $template): array;
}
