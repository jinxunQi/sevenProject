<?php
namespace app\lib\exception;

/**
 * Category类异常错误
 * Class BaseException
 * @package app\lib\exception
 */
class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '指定栏目不存在，请检查参数';
    public $errorCode = 50000;//响应状态码
}