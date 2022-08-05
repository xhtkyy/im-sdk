<?php
/**
 * @author ThompsonCr
 * @date 2022/4/24 0024
 */

namespace KyyIM\Template;

use KyyIM\Constants\MessageConstant;
use Exception;
use KyyIM\Constants\MessageClass;
use KyyIM\Exception\ImException;
use KyyIM\Template\Models\TemplateMessage;
use KyyIM\Template\Services\TemplateMessageService;
use KyyIM\Template\src\Normal;

/**
 * //通用
 * @method static TemplateInterface byScene($scene, ...$arguments)
 */
abstract class MessageType {

    //消息类
    protected static $class = MessageClass::WORK_NOTICE;

    protected static $institution_type = MessageConstant::BUYER; //默认采购商

    protected static $config = [];

    /**
     * 更新消息状态
     * @param int $status
     * @param int $operator
     * @param int $relation_id
     * @param string $relation_field
     * @return void
     */
    public static function updateStatusByScene(int $status, int $operator, int $relation_id, string $relation_field = "id"){
        (new TemplateMessageService())->updateStatus(...func_get_args());
    }

    /**
     * @throws Exception
     */
    public static function __callStatic($name, $arguments) {
        //判断是否是通用
        if ($name == "byScene") {
            $message_type = $arguments[0];
            unset($arguments[0]);
        } else {
            //获取枚举
            $message_type = MessageConstant::get(strtoupper($name));
            if (!$message_type) throw new ImException("消息枚举值获取不到");
        }

        //获取配置
        $config = static::$config[$message_type] ?? [];
        $class  = $config['template'] ?? Normal::class;
        /**
         * @var TemplateInterface $template
         */
        $template = new $class;
        $template->setConfig($config)
            ->setMessageClass(static::$class)
            ->setType($message_type)
            ->setScene($config['scene'] ?? 0)
            ->setTemplate(strtolower(class_basename($template)))
            ->setHeaders(['institution_type' => static::$institution_type])
            ->setContent([
                'title' => sprintf($config['title'] ?? "%s", ...$arguments)
            ]);
        return $template;
    }
}
