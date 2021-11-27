<?php


namespace Ehua\Caiji;


use Ehua\Tool\Tool;
use GuzzleHttp\Client;

/**
 * 抓取常规网站数据方法 {列表+内页}
 * Class Word
 * @package Ehua\Caiji
 */
class Word
{
    public $guzz;

    public $stream_opts = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ];

    /**
     * 初始化入口
     */
    public function init()
    {
        $this->guzz = new Client();

        $domain = "http://www.fuermajiaju.com";

        $tt = 145;
        $ii = null;
        $p = '/product-54show.html';


        $res = (string)$this->guzz->get($domain . $p)->getBody();

        $res = Tool::str_To_Utf8($res);
        $res = str_replace('gb2312', 'utf-8', $res);


        $res = \phpQuery::newDocument($res);
        $data = [];
        \phpQuery::selectDocument($res);
        $count = pq('body')->find('.prolist')->find('a');
        $b = pq('body')->find('.prolist')->find('a');
        for ($i = 0; $i < count($count); $i++) {
            $url = $domain . $b->eq($i)->attr('href');
            $img = $domain . $b->eq($i)->find('img')->attr('src');
            if (substr($img, 0, 1) == '\\') {
                $img = $domain . ($img);
            } else {
                $img = $img;
            }
            $img=$this->str_to_url($img);


            $rand = uniqid() . rand(100, 999);
            $this->makdir();
            $path = DS . 'uploads' . DS . 'sort' . DS . $rand . '.jpg';
            file_put_contents(ROOT_PATH . 'public' . $path, file_get_contents($img, false, stream_context_create($this->stream_opts)));

            $data['img'] = $path;
            $data['type'] = $tt;
            $body = $this->getbody($domain, $url);
            $data['body'] = $body['body'];
            $data['name'] = $body['name'];

            //TODO 重置id
//            preg_match("/\d+/", $url, $data[$i]['id']);
//            $data[$i]['id'] = $data[$i]['id'][0];
            \phpQuery::selectDocument($res);
            db('article')->insert($data);
            die;
        }

    }

    private function makdir()
    {
        if (!file_exists(ROOT_PATH . 'public' . DS . 'uploads' . DS)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads' . DS);//这里会返回true
        }
        if (!file_exists(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'sort' . DS)) {
            mkdir(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'sort' . DS);//这里会返回true
        }
    }
    private function str_to_url($str)
    {
        preg_match_all("/[\x{4e00}-\x{9fff}]+/u", $str, $matches);
        if (!empty($matches[0])) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $str = str_replace($matches[0][$i], urlencode($matches[0][$i]), $str);
            }
        }
        return $str;
    }
    private function getbody($domain, $url)
    {
        $this->makdir();

        $res = (string)$this->guzz->get($url)->getBody();

        $res = Tool::str_To_Utf8($res);
        $res = str_replace('gb2312', 'utf-8', $res);

        $res = \phpQuery::newDocument($res);
        \phpQuery::selectDocument($res);
        $imgs = pq('.dprochanpin_2_2')->find('img');
        $body = pq('.dprochanpin_2_2')->html();
        //去除所有img
        for ($i = 0; $i < $imgs->count(); $i++) {
            $temp_img = $imgs->eq($i)->attr('src');
            if (substr($temp_img, 0, 1) == '\\' || substr($temp_img, 0, 1) == '/') {
                $img = $domain . ($temp_img);
            } else {
                $img = $temp_img;
            }

            $img=$this->str_to_url($img);

            $rand = uniqid() . rand(100, 999);
            $path = DS . 'uploads' . DS . 'sort' . DS . $rand . '.jpg';
            file_put_contents(ROOT_PATH . 'public' . $path, file_get_contents(($img), false, stream_context_create($this->stream_opts)));
            $body = str_replace($temp_img, $path, $body);
        }

        $data['body'] = $body;
        $data['name'] = pq('.dprochanpin_2_1')->text();;

        return $data;
    }

}