<?php
namespace app\api\model;

class Product extends BaseModel
{
    protected $hidden = ['delete_time','update_time'];

    /**
     * 获取最近$count个商品数据
     * @param int $count
     * @return mixed
     * @throws mixed
     */
    public static function getMostRecent($count = 15)
    {
        return self::limit($count)->order([
            'create_time' => 'desc'
        ])->select();
    }


    /**
     * 通过categoryId获取栏目下的商品
     * @param $categoryId
     * @return mixed
     * @throws
     */
    public static function getProductsByCategoryID($categoryId)
    {
        $products = self::where('category_id', 'eq', $categoryId)
            ->select();
        return $products;
    }

    /**
     * 商品关联的商品图（一对多）
     * @return \think\model\relation\HasMany
     */
    public function imgs()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    /**
     * 商品关联属性（一对多）
     * @return \think\model\relation\HasMany
     */
    public function properties()
    {
        return $this->hasMany(ProductProperty::class, 'product_id', 'id');
    }

    /**
     * 通过id获取商品详情
     * @param $id
     * @return mixed
     * @throws
     */
    public static function getProductDetail($id)
    {
//        $product = self::with(['imgs.imageUrl' ,'properties'])->find($id);
        //query
        $product = self::with([
            'imgs' => function ($query) {
                $query->with(['imageUrl'])
                    ->order('order', 'asc');
            },
            'properties'
        ])->find($id);

        return $product;
    }
}
