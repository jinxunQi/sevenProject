<?php
namespace app\api\model;

class Banner extends BaseModel
{
    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
   }

    /**
     * 通过id获取banner信息
     * @param $id
     * @return Banner
     */
    public static function getBannerById($id)
    {
        $banner = self::with(['items','items.img'])->find($id);
        return $banner;
   }
}