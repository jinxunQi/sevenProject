<?php
namespace app\lib\exception;

/**
 * 通用参数类异常错误
 * Class BaseException
 * @package app\lib\exception
 */
class ParameterException extends BaseException
{
    public $code = 400;//http状态码
    public $msg = 'invalid parameters';
    public $errorCode = 10000;//响应状态码
}