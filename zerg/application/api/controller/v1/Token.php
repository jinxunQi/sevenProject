<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\service\UserToken;
use app\api\validate\TokenGetValidate;

class Token extends BaseController
{
    /**
     * 通过小程序code获取令牌token
     * @param string $code
     * @return mixed
     * @throws
     */
    public function getToken($code = '')
    {
        (new TokenGetValidate())->goCheck();

        //...伪代码
        $userTokenService = new UserToken();
        $token = $userTokenService->get($code);
        return $token;
    }
}