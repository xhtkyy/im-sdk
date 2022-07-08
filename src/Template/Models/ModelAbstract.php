<?php

namespace KyyIM\Template\Models;

use KyyIM\Exception\ImException;

/**
 * @method static \Illuminate\Database\Eloquent\Builder query()
 */
abstract class ModelAbstract {
    public static function __callStatic($name, $arguments) {
        $class = config("kyy_im.models")[self::class] ?? "";
        if (empty($class) || !class_exists($class)) {
            throw new ImException("class " . self::class . " 配置不能为空");
        }
        return call_user_func([$class, $name], ...$arguments);
    }
}
