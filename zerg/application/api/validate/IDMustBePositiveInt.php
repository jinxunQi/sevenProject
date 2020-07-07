<?php
namespace app\api\validate;

/**
 * Id验证器,验证id是否为空|正整数
 * Class IDMustBePositiveInt
 * @package app\api\validate
 */
class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger'
    ];
}