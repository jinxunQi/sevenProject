<?php
namespace app\lib\exception;

/**
 * Product类异常错误
 * Class ProductException
 * @package app\lib\exception
 */
class ProductException extends BaseException
{
    public $code = 404;//http状态码
    public $msg = '指定的商品不存在，请检查参数';
    public $errorCode = 20000;//响应状态码
}