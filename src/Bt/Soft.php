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
 * 软件管理
 * Class Soft
 * @package Ehua\Bt
 */
class Soft extends BaseBt
{
    /**
     * 获取软件列表
     * @param int $page
     * @param int $type
     * @param int $force
     * @param string $query
     * @return mixed
     * @throws CurlException
     */
    public function getList($page = 1, $type = 0, $force = 0, $query = '')
    {
        $url = '/plugin?action=get_soft_list';
        $p_data['p'] = $page;
        $p_data['type'] = $type;
        $p_data['tojs'] = 'soft.get_list';
        $p_data['force'] = $force;// 是否更新列表 1=是 0=否
        $p_data['query'] = $query; // 搜索
        //请求面板接口
        return $this->HttpPostCookie($url, $p_data);
    }
}
