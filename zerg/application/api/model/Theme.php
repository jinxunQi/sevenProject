<?php
namespace app\api\model;

class Theme extends BaseModel
{
    protected $hidden = ['delete_time','topic_img_id','head_img_id'];

    /**
     * 获取栏目的封面图数据
     * @return \think\model\relation\BelongsTo
     */
    public function topicImg()
    {
        return $this->belongsTo(Image::class, 'topic_img_id', 'id');
    }

    /**
     * 获取栏目中的大头图数据
     * @return \think\model\relation\BelongsTo
     */
    public function headImg()
    {
        return $this->belongsTo(Image::class, 'head_img_id', 'id');
    }

    /**
     *  关联product，多对多关系
     * @return \think\model\relation\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'theme_product', 'product_id', 'theme_id');
    }

    /**
     * 通过id获取对应的主题数据
     * @param $id
     * @return mixed
     */
    public static function getThemeWithProducts($id)
    {
        $themes = self::with(['products','topicImg','headImg'])->find($id);
        return $themes;
    }
}
