<?php
namespace app\api\validate;
use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

/**
 * Class BaseValidate
 * 验证类的基类
 */
class BaseValidate extends Validate
{
    /**
     * 检测所有客户端发来的参数是否符合验证类规则
     * 基类定义了许多自定义验证方法
     * 这些自定义方法可以直接进行调用
     * @return bool
     * @throws ParameterException
     */
    public function goCheck()
    {
        $request = Request::instance();
        $params = $request->param();
        $params['token'] = $request->header('token');

        if (true != $this->check($params)) {
            //抛出参数异常 (验证器验证错误信息)
            $exception = new ParameterException([
                'msg' => is_array($this->error) ? implode(';',$this->error):$this->error
            ]);
            throw $exception;
        }
        return true;
    }

    /**
     * 自定义验证规则 验证参数必须是正整数
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    public function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value)
            &&is_int($value + 0)
            &&($value + 0) > 0) {
            return true;
        }
        return false;
//        return $field . '必须是正整数';
    }

    /**
     * 验证参数是否为空
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool
     */
    public function isNotEmpty($value, $rule = '', $data = [], $field = '')
    {
        if (empty($value)) {
            return false;
        }
        return true;
    }

    /**
     * 验证手机号码是否符合
     * @param $value
     * @return bool
     */
    public function isMobile($value)
    {
        if (preg_match('/^1[34578]\d{9}$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * 通过验证规则中获取参数
     * @param array $arrays
     * @return array
     * @throws
     */
    public function getDataByRule($arrays = [])
    {
        // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
        if (array_key_exists('uid', $arrays)
            || array_key_exists('user_id', $arrays)) {
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }
        $arrayData = [];
        foreach ($this->rule as $key => $value) {
            $arrayData[$key] = $arrays[$key];
        }
        return $arrayData;
    }
}