<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category extends BaseController
{
    /**
     * 获取最近的商品详情
     * @param int $count 默认15条
     * @return mixed
     * @throws
     */
    public function getAllCategories()
    {
        $categories = CategoryModel::all([], 'img');

        if (0 == count($categories)) {
            new CategoryException();
        }
        return $categories;
    }
}
