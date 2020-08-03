<?php
namespace app\api\validate;

/**
 * IDcollection
 * Class IDCollection
 * @package app\api\validate
 */
class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    protected $message = [
        'ids' => 'ids参数必须为以逗号分隔的多个正整数,仔细看文档啊'
    ];

    /**
     * 检测ids字符串，是否每个id都是正整数
     * @param $value string
     * @return bool
     */
    public function checkIDs($value)
    {
        $values = explode(',',$value);
        if (empty($values)) {
            return false;
        }

        foreach ($values as $id) {
            if (!$this->isPositiveInteger($id)) {
                //必须是正整数
                return false;
            }
        }
        return true;
    }
}