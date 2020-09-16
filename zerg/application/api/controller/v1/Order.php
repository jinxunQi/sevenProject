<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PagingParameter;
use app\api\validate\PlaceOrder;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

/**
 * 订单控制器
 * Class Order
 * @package app\api\controller\v1
 */
class Order extends BaseController
{
    /**
     * 前置操作数组
     * @var array
     */
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getSummaryByUser']
    ];

    /**
     * 思路：
     * 用户在选择商品后，向API提交包含它所选择商品的相关信息
     * API在接收到信息后，需要检查订单相关商品的库存量
     * 有库存，把订单数据存入数据库中=下单成功了，返回客户端信息，告诉客户端可以支付了
     * 调用我们的支付接口，进行支付
     * 还需要再次进行库存量检测
     * 服务器这边就可以调用微信的支付接口进行支付
     * 微信会返回给我们一个支付的结果（异步）
     * 成功：也需要进行库存量的检查
     * 成功：进行库存量的扣除
     */

    /**
     * 商品下单接口
     * @return mixed
     * @throws
     */
    public function placeOrder()
    {
        (new PlaceOrder())->goCheck();
        $products = input('post.products\a');
        $uid = TokenService::getCurrentTokenVar('uid');
        $order = new OrderService();
        $status = $order->place($uid, $products);
        return $status;
    }


    /**
     * 获取用户的订单列表分页数据
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @throws \think\exception\DbException
     */
    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        //其实可以直接return $pagingData的
        if ($pagingOrders->isEmpty()) {
            return [
                'data' => [],
                'current_page' => $pagingOrders->getCurrentPage()
            ];
        }
        return [
            'data' => $pagingOrders->hidden(['snap_items', 'snap_address'])->toArray(),
            'current_page' => $pagingOrders->getCurrentPage()
        ];
    }

    /**
     * 获取订单详情
     * @param $id
     * @return OrderModel
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\exception\DbException
     */
    public function getDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);

        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }
}