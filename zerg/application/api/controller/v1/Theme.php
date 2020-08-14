<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;

class Theme extends BaseController
{
    /**
     * * 通过拼接$ids获取专题栏目列表
     * @url     /theme?ids=:id1,id2,id3...
     * @param string $ids
     * @return mixed
     * @throws
     */
    public function getSimpleList($ids = '')
    {
        //参数验证
        $validate = new IDCollection();
        $validate->goCheck();

        $ids = explode(',', $ids);
//        $result = ThemeModel::with(['topicImg','headImg'])->where('id','in',$ids)->select();
        $result = ThemeModel::with(['topicImg','headImg'])->select($ids);
        if ($result->isEmpty()) {
            throw new ThemeException();
        }
        return $result;
    }

    /**
     * 获取专题栏目下的商品
     * @param $id
     * @return array
     * @throws
     */
    public function getComplexOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $theme = ThemeModel::getThemewithProducts($id);

        if (empty($theme)) {
            throw new ThemeException();
        }
        return $theme->hidden(['products.summary'])->toArray();
    }
}
