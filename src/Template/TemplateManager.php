<?php
/**
 * @author ThompsonCr
 * @date 2022/3/24 0024
 */

namespace KyyIM\Template;

use KyyIM\Exception\ImException;
use Monolog\Logger;

/**
 * @method static TemplateInterface normal(int|null $type = null)
 * @method static TemplateInterface review(int|null $type = null)
 * @method static TemplateInterface project(int|null $type = null)
 */
class TemplateManager {
    /**s
     * @throws ImException
     */
    public static function __callStatic($name, $arguments) {
        $name  = strtolower($name);
        $class = "App\\Im\\Template\\src\\" . ucfirst($name);
        if (!class_exists($class)) throw new ImException("模板未找到", Logger::ERROR);
        /**
         * @var TemplateInterface $template
         */
        $template = new $class();
        $template->setType($arguments[0] ?? 0)
            ->setTemplate($name);
        return $template;
    }
}
