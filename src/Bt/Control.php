<?php

// +----------------------------------------------------------------------
// | 宝塔PHP扩展包
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 [ https://www.Ehua.net ]
// +----------------------------------------------------------------------
// | 官方网站: https://gitee.com/liguangchun/bt
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 仓库地址 ：https://gitee.com/liguangchun/bt
// | github 仓库地址 ：https://github.com/GC0202/bt
// | Packagist 地址 ：https://packagist.org/packages/liguangchun/bt
// +----------------------------------------------------------------------

namespace Ehua\Bt;

use Ehua\Bt\Curl\CurlException;

/**
 * 系统监控
 * Class Control
 * @package Ehua\Bt
 */
class Control extends BaseBt
{
    /**
     * 获取监控信息
     * @param string $type 类型 GetCpuIo = CPU信息/内存 GetDiskIo = 磁盘IO GetNetWorkIo = 网络IO
     * @param int $start_time 开始时间
     * @param int $end_time 结束时间
     * @return mixed
     * @throws CurlException
     */
    public function getInfo($type = 'GetCpuIo', $start_time = 0, $end_time = 0)
    {
        if (empty($start_time)) $start_time = strtotime(date('Y-m-d'));
        if (empty($end_time)) $end_time = time();
        $url = "/ajax?action={$type}&start={$start_time}&end={$end_time}";
        //请求面板接口
        return $this->HttpPostCookie($url, []);
    }
}
