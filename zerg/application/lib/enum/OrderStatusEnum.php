<?php
namespace app\lib\enum;

/**
 * 订单状态枚举类
 * Class OrderStatusEnum
 * @package app\lib\enum
 */
class OrderStatusEnum
{

    //未支付
    const UNPAID = 1;

    //已支付
    const PAID = 2;

    //已发货
    const DELIVERY = 3;

    //已支付，但库存不足
    const PAID_BUT_OUT_OF = 4;
}