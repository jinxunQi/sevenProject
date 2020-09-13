<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

/**
 * 支付控制器
 * Class Pay
 * @package app\api\controller\v1
 */
class Pay extends BaseController
{
    /**
     * 前置操作数组
     * @var array
     */
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];


    /**
     * 获取微信预下单数据
     * @param string $id
     * @return mixed
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function getPreOrder($id = '')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

}