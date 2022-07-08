<?php

namespace KyyIM\Contracts;

class MessageClass {
    //服务通知 默认
    const SERVICE_NOTICE = 1;
    //工作事项
    const WORK_NOTICE = 2;
    //采购通知
    const ORDER_NOTICE = 3;
    //活动通知
    const ACTIVITY_NOTICE = 4;
    //群通知
    const GROUP_NOTICE = 5;

    const CONSTANT_NAME = [
        self::SERVICE_NOTICE  => "服務通知",
        self::WORK_NOTICE     => "工作事項",
        self::ORDER_NOTICE    => "採購交易",
        self::ACTIVITY_NOTICE => "精選活動",
    ];
}
