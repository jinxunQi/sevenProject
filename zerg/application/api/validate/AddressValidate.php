<?php
namespace app\api\validate;

/**
 * 地址验证类
 * Class AddressValidate
 * @package app\api\validate
 */
class AddressValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|isNotEmpty',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty',
    ];

    protected $message = [
        'name' => '收货人必填',
        'mobile' => '手机号必填',
        'province' => '省必填',
        'city' => '省必填',
        'country' => '省必填',
        'detail' => '省必填',
    ];

}