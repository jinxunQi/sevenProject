<?php
namespace app\api\validate;

/**
 * TokenGetValidate
 * Class TokenGetValidate
 * @package app\api\validate
 */
class TokenGetValidate extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '缺失code参数，无法获取token令牌'
    ];

}