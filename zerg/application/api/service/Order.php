<?php
namespace app\api\service;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;

/**
 * 订单服务接口
 * Class Order
 * @package app\api\service
 */
class Order
{
    /**
     * 客户端传递过来的products
     * @var
     */
    protected $oProducts;

    /**
     * 从数据库中匹配客户端传递过来的products
     * @var
     */
    protected $products;

    /**
     * 用户id
     * @var
     */
    protected $uid;

    /**
     * Product模型
     * @var Product
     */
    private $productModel;

    /**
     * 构造函数赋值模型参数
     * Order constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->productModel = $product;
    }

    /**
     * 下单执行接口
     * @param $uid
     * @param $oProducts
     * @return mixed
     * @throws
     */
    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->uid = $uid;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;//告诉客户端是否下单成功
        return $order;
    }

    /**
     * 通过客户端传递的products
     * 从数据库中匹配相对应的数据
     * (根据订单信息查找真实的商品信息)
     * @param $oProducts
     * @return mixed
     * @throws
     */
    protected function getProductsByOrder($oProducts)
    {
        $oProductIDs = [];
//        $oProductIds = array_column($oProducts, 'product_id');//简化方法
        foreach ($oProducts as $item) {
            array_push($oProductIDs, $item['product_id']);
        }
//        $products = $this->productModel
//            ->where('id', 'in', $oProductIDs)
//            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
//            ->select();//另一种方法
        $products = Product::all($oProductIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;
    }


    /**
     * 检测订单中的商品库存 对外开放(支付接口)
     * @param $orderId
     * @return mixed
     * @throws
     */
    public function checkOrderStock($orderId)
    {
        //查询下单的订单包含的商品
        $oProducts = OrderProduct::where('order_id', 'eq', $orderId)
            ->select();
        $this->oProducts = $oProducts;

        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

    /**
     * 通过客户端提交商品列表，与数据库匹配的数据products
     * 进行库存检测、整合商品列表数据、计算订单总价等
     * @return array
     * @throws OrderException
     */
    private function getOrderStatus()
    {
        $status = [
            'pass' => true, //订单商品是否全部通过库存检测(标识)
            'order_price' => 0, //订单总价
            'total_count' => 0,//订单商品总数量
            'p_status_array' => [],//记录商品列表的详情商品数据
        ];
        
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'], $oProduct['count'], $this->products);
            if (!$pStatus['have_stock']) {
                $status['pass'] = false;
            }
            $status['order_price'] += $pStatus['total_price'];
            $status['total_count'] += $pStatus['count'];
            array_push($status['p_status_array'], $pStatus);
        }
        return $status;
    }

    /**
     * 对详细商品数据进行处理
     * 库存检测、下单商品是否存在、整合商品数据
     * @param $oPID
     * @param $oCount
     * @param $products
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;//用于记录检测从products中找到商品数据的数组下标
        $pStatus = [
            'id' => null,//商品id
            'have_stock' => false,//是否存在库存
            'count' => 0,//商品购买数量
            'name' => '',//商品名称
            'total_price' => 0,//商品总价值
        ];

        //商品列表当前的商品id和数据库匹配的商品循环检测是否存在
        for ($i = 0; $i < count($products); $i++) {
            //数据库中存在该商品
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            // 客户端传递的product_id有可能根本不存在
            throw new OrderException([
                'msg' => 'id为'. $oPID .'的商品不存在，创建订单失败'
            ]);
        }else{
            $product = $products[$pIndex];//商品数据
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['count'] = $oCount;
            $pStatus['total_price'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['have_stock'] = true;
            }
        }
        return $pStatus;
    }

    /**
     * 生成订单快照数据
     * @param $status
     * @return array
     * @throws
     */
    private function snapOrder($status)
    {
        $snap = [
            'order_price' => 0,//订单总价格
            'snap_name' => '',//商品名称快照
            'snap_img' => '',//图片快照
            'snap_address' => '',//地址快照
            'total_count' => 0,//订单总商品数量
            'p_status' => [],//订单关联商品信息
        ];

        $snap['total_count'] = $status['total_count'];
        $snap['order_price'] = $status['order_price'];
        $snap['snap_img'] = $this->products[0]['main_img_url'];//取商品中第一个的图片
        $snap['snap_name'] = $this->products[0]['name'];
        $snap['p_status'] = $status['p_status_array'];
        $snap['snap_address'] = json_encode($this->getUserAddress());

        if (count($this->products) > 1) {
            $snap['snap_name'] .= '等';
        }
        return $snap;
    }

    /**
     * 获取用户的地址
     * @return mixed
     * @throws
     */
    private function getUserAddress()
    {
        $addressInfo = UserAddress::where('user_id', 'eq', $this->uid)
            ->find();
        if (!$addressInfo) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001,
            ]);
        }
        return $addressInfo;
    }

    /**
     * 创建订单(使用对象的形式)
     * @param $snapOrder
     * @return array
     * @throws Exception
     */
    private function createOrder($snapOrder)
    {
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->order_no = $orderNo;
            $order->user_id = $this->uid;
            $order->total_price = $snapOrder['order_price'];
            $order->total_count = $snapOrder['total_count'];
            $order->snap_img = $snapOrder['snap_img'];
            $order->snap_name = $snapOrder['snap_name'];
            $order->snap_address = $snapOrder['snap_address'];
            $order->snap_items = json_encode($snapOrder['p_status']);//序列化对象数据存储
            $order->save();
            $orderId = $order->id;
            $createTime = $order->create_time;

            $orderProduct = new OrderProduct();
            foreach ($this->oProducts as &$product) {
                $product['order_id'] = $orderId;
            }
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderId,
                'create_time' => $createTime
            ];
        } catch (Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }

    /**
     * 生成随机订单号
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
        $orderNo = $yCode[intval((date('Y')) - 2020)] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderNo;
    }
}