<?php
namespace app\api\validate;

/**
 * CountValidate
 * Class CountValidate
 * @package app\api\validate
 */
class CountValidate extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'
    ];

    protected $message = [
        'count' => 'count参数必须为正整数,且在1到15之间'
    ];

}