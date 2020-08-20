<?php
namespace app\lib\exception;

/**
 * 成功提醒消息
 * Class SuccessMessage
 * @package app\lib\exception
 */
class SuccessMessage extends BaseException
{
    public $code = 201;//http状态码
    public $msg = 'success';
    public $errorCode = 0;//响应状态码
}