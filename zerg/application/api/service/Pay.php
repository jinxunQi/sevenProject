<?php
namespace app\api\service;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;

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