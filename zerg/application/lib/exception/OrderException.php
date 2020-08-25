<?php
namespace app\lib\exception;

/**
 * 订单异常类
 * Class OrderException
 * @package app\lib\exception
 */
class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查ID';
    public $errorCode = 80000;
}