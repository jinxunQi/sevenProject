<?php
namespace app\api\service;

/**
 * token基类
 * Class Token
 * @package app\api\service
 */
class Token
{

    /**
     * 生成令牌
     * @return mixed
     */
    public static function generateToken()
    {
        $randChar = getRandChar(32);
        //区别于time() time() 获取当前的系统时间戳 $_SERVER["REQUEST_TIME"] 得到请求开始时的时间戳
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $tokenSalt = config('secure.token_salt');
        return md5($randChar . $timestamp . $tokenSalt);
    }
}