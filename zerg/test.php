<?php
/**
 * 生成随机订单号
 * @return string
 */
function makeOrderNo()
{
    $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
    $orderNo = $yCode[intval((date('Y')) - 2020)] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderNo;
}
print_r(makeOrderNo());