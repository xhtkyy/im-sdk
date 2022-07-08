<?php

namespace KyyIM;

use Illuminate\Support\Str;
use KyyIM\Exception\ImException;
use KyyIM\Interfaces\ImInterface;

/**
 * im 服务管理器
 * @method static ImInterface kyyIm($config = null)
 */
class ImServiceManager {
    /**
     * @throws ImException
     */
    public static function __callStatic($name, $arguments) {
        $class = "KyyIM\\Services\\$name\\Manager";
        if (!class_exists($class)) {
            throw new ImException("Driver Manager Not Found");
        }
        if (empty($arguments)) {
            $arguments = [config("kyy_im.drivers." . Str::snake($name))];
        }
        return new $class(...$arguments);
    }
}
