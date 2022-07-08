<?php

namespace KyyIM\Constants;


use KyyIM\Exception\ImException;

class MessageConstant {
    //采购商
    const BUYER = 1;
    //供应商
    const SUPPLIER = 2;

    protected static $constants = [];

    /**
     * 获取枚举
     * @param $name
     * @return mixed|string|int
     * @throws ImException
     */
    public static function get($name) {
        if (!isset(self::$constants[$name])) {
            $class = config("kyy_im.class")[MessageConstant::class] ?? '';
            if (!class_exists($class)) throw new ImException("MessageConstant 映射类不能为空");
            $reflectionClass = new \ReflectionClass($class);
            self::$constants = $reflectionClass->getConstants();
        }
        return self::$constants[$name] ?? "";
    }
}
