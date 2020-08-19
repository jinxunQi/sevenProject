<?php
namespace app\api\model;

class ProductImage extends BaseModel
{
    protected $hidden = ['delete_time', 'img_id', 'product_id'];

    /**
     * 关联详细的图片（一对一）
     * @return \think\model\relation\BelongsTo
     */
    public function imageUrl()
    {
        return $this->belongsTo(Image::class, 'img_id', 'id');
    }
}
