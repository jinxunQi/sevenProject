<?php
namespace app\api\service;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ParameterException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

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

    /**
     * 根据token向缓存中拿取token中所要的变量
     * @param $key string uid|wxResult|scope
     * @return mixed
     * @throws
     */
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        }

        if (!is_array($vars)) {
            $vars = json_decode($vars, true);
        }
        if (!array_key_exists($key, $vars)) {
            return $vars[$key];
        }else{
            //不返回到客户端去，调Exception记录到日志中
            throw new Exception('尝试获取的Token变量并不存在');
        }
    }


    /**
     * 获取登录用户uid
     * @return mixed
     * @throws
     */
    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        $scope = self::getCurrentTokenVar('scope');//用户权限

        if ($scope == ScopeEnum::Admin) {
            //如果时管理员身份、权限才可以自己传入uid
            //且必须在get参数中，post不接受任何uid字段
            $userID = input('get.uid');
            if (!$userID) {
                throw new ParameterException([
                    'msg' => '没有指定要操作的用户对象'
                ]);
            }
            return $userID;
        }else{
            return $uid;
        }
        
    }
}