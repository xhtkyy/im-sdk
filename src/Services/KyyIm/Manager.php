<?php

namespace KyyIM\Services\KyyIm;

use KyyIM\Exception\ImException;
use KyyIM\Interfaces\GroupInterface;
use KyyIM\Interfaces\ImInterface;
use KyyIM\Interfaces\InstitutionInterface;
use KyyIM\Interfaces\MessageInterface;
use KyyIM\Interfaces\UserInterface;

class Manager implements ImInterface {

    protected $config;
    private static $container = [];

    /**
     * @throws ImException
     */
    public function __construct($config) {
        //检查参数
        if (empty($config['base_uri']) || empty($config['jwt_secret'])) {
            throw new ImException("请检查配置项");
        }
        $this->config = $config;
    }

    public function user(): UserInterface {
        return $this->make(User::class);
    }

    public function group(): GroupInterface {
        return $this->make(Group::class);
    }

    public function message(): MessageInterface {
        return $this->make(Message::class);
    }

    public function institution(): InstitutionInterface {
        return $this->make(Institution::class);
    }

    private function make($class) {
        if (!isset(static::$container[$class])) {
            static::$container[$class] = new $class($this->config);
        }
        return static::$container[$class];
    }
}
