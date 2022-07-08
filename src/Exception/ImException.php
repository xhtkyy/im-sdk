<?php

namespace KyyIM\Exception;

use KyyIM\Constants\ErrorCode;
use KyyTools\Exceptions\Exception;

class ImException extends Exception {
    protected $code = ErrorCode::ERROR;
    protected $message = "IM請求異常,稍後重試";

}
