<?php

namespace KyyIM\Services\KyyIm;

abstract class AbstractKyyIm {

    const STATUS_OK = 1000; //发送成功
    const STATUS_FAIL = 1; //请求失败

    protected $config;

    public function __construct($config) {
        $this->config = $config;
    }
}
