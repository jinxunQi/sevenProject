<?php
namespace app\api\validate;
use app\lib\exception\ParameterException;
use think\Validate;

/**
 * 下单验证类
 * Class PlaceOrder1
 */
class PlaceOrder extends BaseValidate
{
    /**
     * 验证规则rule
     * @var array
     */
    protected $rule = [
        'products' => 'require|checkProducts'
    ];

    /**
     * 自定义验证规则singleRule
     * product_id 和 count 验证规则
     * @var array
     */
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    /**
     * 验证商品列表规则 (first)
     * @param $values
     * @throws ParameterException
     * @return mixed
     */
    public function checkProducts($values)
    {
        if (!is_array($values)) {
            throw new ParameterException([
                'msg' => '商品列表必须是数组'
            ]);
        }
        if (empty($values)) {
            throw new ParameterException([
                'msg' => '商品列表不能为空'
            ]);
        }

        foreach ($values as $value) {
            $this->checkProduct($value);
        }
        return true;
    }

    /**
     * 验证商品列表下的商品参数 (second)
     * product_id 和 count
     * @param $value
     * @throws ParameterException
     */
    public function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if (!$result) {
            throw new ParameterException([
                'msg' => '商品列表参数错误'
            ]);
        }
    }
}