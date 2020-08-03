<?php
namespace app\api\model;

class Banner extends BaseModel
{
    /**
     * 获取banner所属的items 一对多
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
    }

    /**
     * 通过id获取banner信息
     * @param $id
     * @return Banner
     * @throws
     */
    public static function getBannerById($id)
    {
        $banner = self::with(['items','items.img'])->find($id);
        return $banner;
   }
}