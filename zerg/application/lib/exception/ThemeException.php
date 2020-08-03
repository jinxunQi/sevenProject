<?php
namespace app\lib\exception;

/**
 * Theme类异常错误
 * Class BaseException
 * @package app\lib\exception
 */
class ThemeException extends BaseException
{
    public $code = 404;//http状态码
    public $msg = '指定主题不存在，请检查主题ID';
    public $errorCode = 30000;//响应状态码
}