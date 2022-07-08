<?php

namespace KyyIM\Exception;

use KyyIM\Contracts\ErrorCode;

class ImException extends \Exception {
    protected $code = ErrorCode::ERROR;
    protected $message = "IM請求異常,稍後重試";


}
