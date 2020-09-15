<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\service\WxNotify;
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

    /**
     * 通过xdebug的支付回调
     */
    public function redirectNotify()
    {
        $notify = new WxNotify();
        $notify->Handle();
    }

    /**
     * 微信异步回调
     * 备注：通知频率为15/15/30/180/1800/1800/1800/1800/3600 单位:秒
     * 1.检测库存量, 防止超卖
     * 2.更新这个订单的status状态
     * 3.减库存
     * 如果成功处理, 我们返回微信成功处理的信息, 否则, 我们需要返回没有成功处理
     * 特点: post, xml格式, 不会携带参数 (?x=xxx)
     */
    public function receiveNotify()
    {
        //---------官方方法---------
        $notify = new WxNotify();
        $notify->Handle();
        //-------------------------

        //通过curl_post的形式转发到redirectNotify方法中去
        $xmlData = file_get_contents('php://input');
        $result = curl_post_row('http:/zerg.aaa/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133', $xmlData);
        return $result;
        //Log::error($xmlData);
    }
}