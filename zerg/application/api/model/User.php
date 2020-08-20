<?php
namespace app\api\model;

class User extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $hidden = ['delete_time','update_time'];

    /**
     * 获取用户地址信息（一对一）
     * @return \think\model\relation\HasOne
     */
    public function address()
    {
        return $this->hasOne(UserAddress::class, 'user_id', 'id');
    }

    /**
     * 用户是否存在
     * 存在返回uid，不存在返回0
     * @param $openid
     * @return mixed
     * @throws
     */
    public static function getByOpenID($openid)
    {
        $user = self::where('openid', 'eq', $openid)
            ->find();
        return $user;
    }
}
