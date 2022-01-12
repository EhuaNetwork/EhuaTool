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
use Ehua\Bt\Curl\BtCn;
/**
 * 文件管理
 * Class Files
 * @package Ehua\Bt
 */
class Files extends BaseBt
{
    /**
     * 获取文件列表
     * @param int $page
     * @param int $limit
     * @param string $search
     * @param int $type
     * @return mixed
     * @throws CurlException
     */
    public function getList($page = 1, $showRow = 100, $path = '/www/wwwroot')
    {
        $url = '/files?action=GetDir';
        $p_data['p'] = $page;
        $p_data['showRow'] = $showRow;
        $p_data['path'] = $path;
        //请求面板接口
        $result = $this->HttpPostCookie($url, $p_data);
        if (empty($result['DIR'])) $result['DIR'] = [];
        if (empty($result['FILES'])) $result['FILES'] = [];
        if (empty($result['PAGE'])) $result['PAGE'] = 0;
        if (!is_array($result['DIR'])) $result['DIR'] = [];
        foreach($result['DIR'] as $k=>$dat){
            $d=explode(';',$dat);
            $res['DIR'][$d[0]]['path']=$d[0];
            $res['DIR'][$d[0]]['all_path']=$path.'/'.$d[0];
            $res['DIR'][$d[0]]['time']=$d[2];
            $res['DIR'][$d[0]]['power']=$d[3];
        }
        foreach($result['FILES'] as $k=>$dat){
            $d=explode(';',$dat);
            $res['FILES'][$d[0]]['path']=$d[0];
            $res['FILES'][$d[0]]['all_path']=$path.'/'.$d[0];
            $res['FILES'][$d[0]]['time']=$d[2];
            $res['FILES'][$d[0]]['power']=$d[3];
        }
        if (empty($res)) $res = [];

        return [
            'data' => $res,
            'count' => $this->getCountData($result['PAGE'])
        ];
    }


    /**
     * @param string $sfile 要压缩的文件名
     * @param string $dfile    生成的压缩文件全路径
     * @param string $z_type   压缩类型
     * @param string $path  要压缩的文件所在路径
     * @return array
     * @throws CurlException
     */
    public function zip($sfile='',$dfile = '', $z_type = 'tar.gz', $path = '')
    {
        $url = '/files?action=Zip';
        $p_data['sfile'] = $sfile;
        $p_data['dfile'] = $dfile;
        $p_data['z_type'] = $z_type;
        $p_data['path'] = $path;
        //请求面板接口
        $result = $this->HttpPostCookie($url, $p_data);
        dd($result);
        if (empty($result['DIR'])) $result['DIR'] = [];
        if (empty($result['PAGE'])) $result['PAGE'] = 0;
        if (!is_array($result['DIR'])) $result['DIR'] = [];

        foreach($result['DIR'] as $k=>$dat){
            $d=explode(';',$dat);
            $res['DIR'][$k]['path']=$d[0];
            $res['DIR'][$k]['all_path']=$path.'/'.$d[0];
            $res['DIR'][$k]['time']=$d[2];
            $res['DIR'][$k]['power']=$d[3];
        }
        return [
            'data' => $res['DIR'],
            'count' => $this->getCountData($result['PAGE'])
        ];
    }

    /**
     * @param string $path  删除文件
     * @return array
     * @throws CurlException
     */
    public function DeleteFile($path = '')
    {
        $url = '/files?action=DeleteFile';
        $p_data['path'] = $path;
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

    /**
     * @param string $user          权限属于用户
     * @param string $access        777
     * @param string $all           子目录继承
     * @param string $filename      目录或文件
     * @return array
     * @throws CurlException
     */
    public function SetFileAccess($user='www',$access='755',$all='True',$filename=''){
        $url = '/files?action=SetFileAccess';
        $p_data['user'] = $user;
        $p_data['access'] = $access;
        $p_data['all'] = $all;
        $p_data['filename'] = $filename;
        //请求面板接口
        $result = $this->HttpPostCookie($url, $p_data);
        if (empty($result['status'])) $result['status'] = [];
        if (empty($result['msg'])) $result['msg'] = 0;
        return [
            'status' => $result['status'],
            'msg' => $result['msg'],
        ];
    }



}
