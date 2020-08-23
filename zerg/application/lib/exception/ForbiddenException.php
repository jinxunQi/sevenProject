<?php
namespace app\lib\exception;

/**
 * 权限异常类
 * Class ForbiddenException
 * @package app\lib\exception
 */
class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;//响应状态码
}