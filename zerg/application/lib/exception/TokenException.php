<?php
namespace app\lib\exception;

/**
 * Token验证失败异常
 * Class TokenException
 * @package app\lib\exception
 */
class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token已过期或无效Token';
    public $errorCode = 10001;
}