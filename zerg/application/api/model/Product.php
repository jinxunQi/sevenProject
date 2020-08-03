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
}