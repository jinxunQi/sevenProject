<?php
namespace app\lib\exception;

/**
 * 用户异常类
 * Class UserException
 * @package app\lib\exception
 */
class UserException extends BaseException
{
    public $code = 404;//http状态码
    public $msg = '用户不存在';
    public $errorCode = 60000;//响应状态码
}