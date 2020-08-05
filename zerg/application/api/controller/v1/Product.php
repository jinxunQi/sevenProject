<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\CountValidate;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product extends BaseController
{
    /**
     * 获取最近的商品详情
     * @param int $count 默认15条
     * @return mixed
     * @throws
     */
    public function getRecent($count = 15)
    {
        (new CountValidate())->goCheck();

        $result = ProductModel::getMostRecent($count);
        if ($result->isEmpty()) {
            throw new ProductException();
        }
        $result = $result->hidden(['summary']);
        return $result;
    }


    /**
     * 获取指定栏目下的商品
     * @param $id
     * @return mixed|\think\Paginator
     * @throws
     */
    public function getAllInCategory($id)
    {
        (new IDMustBePositiveInt())->goCheck();

        $products = ProductModel::getProductsByCategoryID($id);
        if (0 == count($products)) {
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }
}