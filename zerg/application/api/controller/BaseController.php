<?php
namespace app\api\controller;

use app\api\service\Token;
use think\Controller;

class BaseController extends Controller
{

    /**
     * 检测用户权限
     * (用户|管理员都可以访问)
     * @throws
     */
    protected function checkPrimaryScope()
    {
        Token::needPrimaryScope();
    }

    /**
     * 检测用户权限
     * (用户独享的权限,只有用户可以访问)
     * @throws
     */
    public function checkExclusiveScope()
    {
        Token::needExclusiveScope();
    }
}