<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;

class Banner extends BaseController
{
    /**
     * 获取banner信息
     * @url /banner/:id
     * @http get
     * @param $id int
     * @return mixed
     * @throws
     */
    public function getBanner($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $banner = BannerModel::getBannerById($id);
        if (!$banner) {
            throw new MissException([
                'msg' => '请求banner不存在',
                'errorCode' => 40000
            ]);
        }
        return $banner;
    }
}
