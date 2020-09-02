<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;

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


    public function getPreOrder($id = '')
    {
        (new IDMustBePositiveInt())->goCheck();
    }

}