<?php
namespace app\api\model;

class Order extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'user_id'];

    /**
     * 通过user_id获取订单分页数据
     * @param $uid
     * @param $page
     * @param $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getSummaryByUser($uid, $page, $size)
    {
        //返回的是一个分页对象Paginator
        $pagingData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }
}
