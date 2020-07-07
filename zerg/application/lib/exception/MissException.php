<?php
namespace app\lib\exception;

/**
 * 404时抛出此异常
 * Class MissException
 * @package app\lib\exception
 */
class MissException extends BaseException
{
    public $code = 404;
    public $msg = 'global:your required resource are not found';
    public $errorCode = 10001;
}