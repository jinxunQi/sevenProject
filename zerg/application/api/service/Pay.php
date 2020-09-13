<?php
namespace app\api\service;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;

// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay', EXTEND_PATH, 'Api.php');

/**
 * 支付服务接口
 * Class Pay
 * @package app\api\service
 */
class Pay
{
    private $orderNo;
    private $orderId;

    public function __construct($orderId)
    {
        if (empty($orderId)) {
            throw new Exception('订单号不能为空');
        }
        $this->orderId = $orderId;
    }

    /**
     * 预下单对外接口
     * @return mixed
     * @throws
     */
    public function pay()
    {
        //订单号可能根本就不存在
        //订单号确实是存在的，但是，订单号和当前用户是不匹配的
        //订单号有可能已经被支付过
        //进行库存量检测
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderId);
        if (!$status['pass']) {
            return $status;
        }

        return $this->makeWxPreOrder($status['order_price']);
    }

    /**
     * 构建微信支付订单信息
     * @param $totalFee int 订单总价(微信的是分)
     * @return mixed
     * @throws
     */
    private function makeWxPreOrder($totalFee)
    {
        $openId = Token::getCurrentTokenVar('openid');
        if (!$openId) {
            throw new TokenException();
        }

        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI ');
        $wxOrderData->SetOpenid($openId);
        $wxOrderData->SetTotal_fee($totalFee * 100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetNotify_url('');
        return $this->getPaySignature($wxOrderData);
    }

    /**
     * 向微信请求订单号并生成签名
     * @param $wxOrderData
     * @return mixed
     * @throws
     */
    private function getPaySignature($wxOrderData)
    {
        $config = new \WxPayConfig();
        $wxOrder = \WxPayApi::unifiedOrder($config, $wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS'
            || $wxOrder['result_code'] != 'SUCCESS') {
            //记录日志
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }

        //保存prepay_id
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }


    /**
     * 构造拉取微信支付所需的参数
     * @param $wxOrder
     * @return array
     * @throws
     */
    private function sign($wxOrder)
    {
        $wxPayJsApiData = new \WxPayJsApiPay();
        $wxPayJsApiData->SetAppid(config('wx.app_id'));
        $wxPayJsApiData->SetSignType('MD5');
        $wxPayJsApiData->SetTimeStamp((string)time());

        $rand = md5(time() . mt_rand(0, 1000));
        $wxPayJsApiData->SetNonceStr($rand);

        $wxPayJsApiData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $sign = $wxPayJsApiData->MakeSign();//签名参数paySign

        $rawValues = $wxPayJsApiData->GetValues();
        $rawValues['paySign'] = $sign;

        unset($rawValues['app_id']);
        return $rawValues;
    }

    /**
     * 保存微信预下单的一些数据
     * prepay_id用于给用户发送消息通知
     * @param $wxOrder
     */
    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id', 'eq', $this->orderId)
            ->update([
                'prepay_id' => $wxOrder['prepay_id']
            ]);
    }

    /**
     * 检测订单号是否合法
     * 1.订单号可能根本就不存在
     * 2.订单号确实是存在的，但是，订单号和当前用户是不匹配的
     * 3.订单号有可能已经被支付过
     * @return bool
     * @throws
     */
    private function checkOrderValid()
    {
        $orderInfo = OrderModel::where('id', 'eq', $this->orderId)->find();

        if (!$orderInfo) {
            throw new OrderException();
        }
        if (!Token::isValidOperate($orderInfo->user_id)) {
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }
        if ($orderInfo->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }

        $this->orderNo = $orderInfo->order_no;
        return true;
    }


}