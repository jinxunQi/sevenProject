<?php
namespace app\api\service;
use app\api\model\Product;
use app\lib\exception\OrderException;

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
     * @throws
     */
    protected function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->uid = $uid;
        $this->products = $this->getProductsByOrder($oProducts);

        $status = $this->getOrderStatus();
    }

    /**
     * 通过客户端传递的products
     * 从数据库中匹配相对应的数据
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
            'p_status_array' => [],//记录商品列表的详情商品数据
        ];
        
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'], $oProduct['count'], $this->products);
            if (!$pStatus['have_stock']) {
                $status['pass'] = false;
            }
            $status['order_price'] += $pStatus['total_price'];
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
}