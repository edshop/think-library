<?php
// +----------------------------------------------------------------------
// | Think-Library
// +----------------------------------------------------------------------
// | 版权所有 2015~2020 泉州市牧滨电子商务有限公司 [ http://www.mubin.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://www.mubin.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://github.com/edshop/think-library
// +----------------------------------------------------------------------

namespace think\admin\extend;

/**
 * 随机数码管理扩展
 * Class CodeExtend
 * @package think\admin\extend
 */
class CodeExtend
{
    /**
     * 获取随机字符串编码
     * @param integer $length 字符串长度
     * @param integer $type 字符串类型(1纯数字,2纯字母,3数字字母)
     * @return string
     */
    public static function random($length = 10, $type = 1)
    {
        $numbs = '0123456789';
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        if (intval($type) === 1) $chars = $numbs;
        if (intval($type) === 2) $chars = "a{$chars}";
        if (intval($type) === 3) $chars = "{$numbs}{$chars}";
        $string = $chars[rand(1, strlen($chars) - 1)];
        if (isset($chars)) while (strlen($string) < $length) {
            $string .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $string;
    }

    /**
     * 唯一日期编码
     * @param integer $length
     * @return string
     */
    public static function uniqidDate($length = 16)
    {
        if ($length < 14) $length = 14;
        $string = date('Ymd') . (date('H') + date('i')) . date('s');
        while (strlen($string) < $length) $string .= rand(0, 9);
        return $string;
    }

    /**
     * 唯一数字编码
     * @param integer $length
     * @return string
     */
    public static function uniqidNumber($length = 12)
    {
        $time = time() . '';
        if ($length < 10) $length = 10;
        $string = ($time[0] + $time[1]) . substr($time, 2) . rand(0, 9);
        while (strlen($string) < $length) $string .= rand(0, 9);
        return $string;
    }
}