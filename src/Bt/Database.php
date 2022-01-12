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
 * 数据库管理
 * Class Database
 * @package Ehua\Bt
 */
class Database extends BaseBt
{
    /**
     * 获取数据库列表
     * @param int $page
     * @param int $limit
     * @param string $search
     * @return mixed
     * @throws CurlException
     */
    public function getList($page = 1, $limit = 15, $search = '')
    {
        $url = '/data?action=getData';
        $p_data['tojs'] = 'database.get_list';
        $p_data['table'] = 'databases';
        $p_data['limit'] = $limit;
        $p_data['p'] = $page;
        $p_data['search'] = $search;
        $p_data['order'] = 'id desc';
        //请求面板接口
        $result = $this->HttpPostCookie($url, $p_data);
        if (empty($result['data'])) $result['data'] = [];
        if (empty($result['page'])) $result['page'] = 0;
        if (!is_array($result['data'])) $result['data'] = [];
        return [
            'data' => $result['data'],
            'count' => $this->getCountData($result['page'])
        ];
    }
}
