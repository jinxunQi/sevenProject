<?php
namespace app\api\model;

class Category extends BaseModel
{
    protected $hidden = ['update_time','delete_time','create_time'];

    /**
     * 分类栏目对应的图片 一对一
     * @return \think\model\relation\BelongsTo
     */
    public function img()
    {
        return $this->belongsTo(Image::class, 'topic_img_id', 'id');
    }
}
