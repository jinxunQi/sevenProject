<?php
namespace app\api\service;
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay', EXTEND_PATH, 'Api.php');

/**
 * 微信支付回调
 * Class WxNotify
 * @package app\api\service
 */
class WxNotify extends \WxPayNotify
{
    //    protected $data = <<<EOD
    //<xml><appid><![CDATA[wxaaf1c852597e365b]]></appid>
    //<bank_type><![CDATA[CFT]]></bank_type>
    //<cash_fee><![CDATA[1]]></cash_fee>
    //<fee_type><![CDATA[CNY]]></fee_type>
    //<is_subscribe><![CDATA[N]]></is_subscribe>
    //<mch_id><![CDATA[1392378802]]></mch_id>
    //<nonce_str><![CDATA[k66j676kzd3tqq2sr3023ogeqrg4np9z]]></nonce_str>
    //<openid><![CDATA[ojID50G-cjUsFMJ0PjgDXt9iqoOo]]></openid>
    //<out_trade_no><![CDATA[A301089188132321]]></out_trade_no>
    //<result_code><![CDATA[SUCCESS]]></result_code>
    //<return_code><![CDATA[SUCCESS]]></return_code>
    //<sign><![CDATA[944E2F9AF80204201177B91CEADD5AEC]]></sign>
    //<time_end><![CDATA[20170301030852]]></time_end>
    //<total_fee>1</total_fee>
    //<trade_type><![CDATA[JSAPI]]></trade_type>
    //<transaction_id><![CDATA[4004312001201703011727741547]]></transaction_id>
    //</xml>
    //EOD;

    /**
     * 编写微信支付回调方法，重写NotifyProcess方法
     * @param \WxPayNotifyResults $objData
     * @param \WxPayConfigInterface $config
     * @param string $msg
     * @return bool|\true 回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
        if ($objData['result_code'] == 'SUCCESS') {
            $orderNo = $objData['out_trade_no'];//自定义下单订单号
            Db::startTrans();
            try {
                $order = OrderModel::where('order_no', 'eq', $orderNo)
                    ->lock(true)
                    ->find();
                if ($order->status == 1) {//待支付的订单
                    $service = new OrderService();
                    //检测库存量
                    $stockStatus = $service->checkOrderStock($order->id);
                    if ($stockStatus['pass']) {
                        //更新订单状态
                        $this->updateOrderStatus($order->id, true);
                        //减商品库存
                        $this->reduceStock($stockStatus);
                    }else{
                        //更新订单状态
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            } catch (Exception $ex) {
                Db::rollback();
                Log::error($ex);//记录日志
                // 如果出现异常，向微信返回false，请求重新发送通知
                return false;
            }
        }else{
            return true;
        }
    }


    /**
     * 更新订单状态
     * @param $orderId
     * @param $success boolean true的时候更新订单状态为'已支付'
     * false的时候更新订单状态为'已支付但库存不足'
     */
    private function updateOrderStatus($orderId, $success)
    {
        $status = $success ? OrderStatusEnum::PAID
            : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', 'eq', $orderId)
            ->update([
                'status' => $status
            ]);
    }

    /**
     * 减商品库存
     * @param $stockStatus
     * @throws Exception
     */
    private function reduceStock($stockStatus)
    {
        if (!empty($stockStatus['p_status_array'])) {
            foreach ($stockStatus['p_status_array'] as $singlePStatus) {
                Product::where('id', 'eq', $stockStatus['id'])
                    ->setDec('stock', $singlePStatus['count']);

                //5.1的另一种写法
//                Product::where('id', 'eq', $stockStatus['id'])
//                    ->update([
//                        'stock' => Db::raw('stock -' . $singlePStatus['count'])
//                    ]);
            }
        }
    }
}