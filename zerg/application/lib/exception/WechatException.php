<?php
namespace app\lib\exception;

/**
 * 微信服务器异常
 * Class WechatException
 * @package app\lib\exception
 */
class WechatException extends BaseException
{
    public $code = 400;
    public $msg = 'wechat unknown error';
    public $errorCode = 999;
}