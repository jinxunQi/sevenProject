<?php
namespace app\lib\exception;
use think\Exception;
use Throwable;

/**
 * 自定义异常类的基类
 * Class BaseException
 * @package app\lib\exception
 */
class BaseException extends Exception
{
    public $code = 400;
    public $msg = 'invalid parameters';
    public $errorCode = 999;
    public $shouldToClient = true;

    /**
     * 构造函数，接收一个关联数组
     * @param array $params 关联数组只应包含code、msg和errorCode，且不是空值
     */
    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return ;
        }
        if (array_key_exists('code',$params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg',$params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode',$params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}