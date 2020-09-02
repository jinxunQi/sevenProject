<?php
namespace app\api\service;
use app\lib\enum\OrderStatusEnum;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
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

    /**
     * 从token中检测基础权限 scope
     * (管理员和用户都能访问)
     * @return bool
     * @throws
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    /**
     * 客户端用户独享权限
     * 只有客户端用户才有权限访问
     * @return bool
     * @throws
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }


    /**
     * 检查操作UID是否合法
     * @param $checkUID
     * @return mixed
     * @throws
     */
    public static function isValidOperate($checkUID)
    {
        if (!$checkUID) {
            throw new Exception(['检查UID时必须传入一个被检查的UID']);
        }
        $currentOperateUID = self::getCurrentUid();
        if ($checkUID == $currentOperateUID) {
            return true;
        }
        return false;
    }
}