<?php
namespace app\api\service;

use app\api\model\User;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WechatException;
use think\Exception;

/**
 * 用户令牌接口
 * Class UserToken
 * @package app\api\service
 */
class UserToken extends Token
{

    protected $code;
    protected $wxLoginUrl;
    protected $wxAppID;
    protected $wxAppSecret;

    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(
            config('wx.login_url'),
            $this->wxAppID,
            $this->wxAppSecret,
            $this->code
        );
    }

    /**
     * 通过code换取接口访问令牌token
     * @return mixed
     * @throws
     */
    public function get()
    {
        //登陆
        //思路1：每次调用登录接口都去微信刷新一次session_key，生成新的Token，不删除久的Token
        //思路2：检查Token有没有过期，没有过期则直接返回当前Token
        //思路3：重新去微信刷新session_key并删除当前Token，返回新的Token
        $result = curl_get($this->wxLoginUrl);

        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        }else{
            $loginFail = array_key_exists('errcode',$wxResult);
            if ($loginFail) {
                //组装错误
                $this->processLoginError($wxResult);
            }else{
                //生成token令牌
                return $this->grantToken($wxResult);
            }
        }

    }


    /**
     * 处理微信登陆异常
     * 哪些异常应该返回客户端，哪些异常不应该返回客户端
     * @param $wxResult
     * @return mixed
     * @throws
     */
    private function processLoginError($wxResult)
    {
        if (is_array($wxResult)) {
            throw new WechatException([
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
        }
    }


    /**
     * 颁发令牌
     * @param $wxResult
     * @return mixed
     * @throws
     */
    private function grantToken($wxResult)
    {
        $openid = $wxResult['openid'];
        $user = User::getByOpenID($openid);
        if (empty($user)) {
            //创建新用户
            $uid = $this->newUser($openid);
        }else{
            $uid = $user->id;
        }

        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    /**
     * 通过openid创建登录新用户
     * 返回用户id
     * @param $openid
     * @return mixed
     */
    private function newUser($openid)
    {
        $user = User::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    /**
     * 组装用户缓存数据
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        //配置用户token权限
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    /**
     * 写入用户数据到缓存中
     * 并返回生成的令牌
     * @param $cachedValue
     * @return mixed
     * @throws
     */
    private function saveToCache($cachedValue)
    {
        $key = self::grantToken();
        $value = json_encode($cachedValue);
        $expireIn = config('setting.token_expire_in');
        $result = cache($key, $value, $expireIn);

        if (!$result) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }

        return $key;
    }


    // 判断是否重复获取
    private function duplicateFetch(){
        //TODO:目前无法简单的判断是否重复获取，还是需要去微信服务器去openid
        //TODO: 这有可能导致失效行为
    }
}