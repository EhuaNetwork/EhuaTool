<?php
namespace  Ehua\Rest\Elasticsearch;
use Elasticsearch\ClientBuilder;
//   "elasticsearch/elasticsearch": "~6.0"
class Elasticsearch
{
    //配置
    private $config = [
        'hosts' => ['http://66.112.217.82:9200']
    ];
    private $api;

    public function __construct()
    {
        #include(APP_PATH .'/vendor/autoload.php');
        #require_once EXTEND_PATH . 'org/elasticsearch/autoload.php';
        import('org.elasticsearch.autoload', EXTEND_PATH);
        $this->api = ClientBuilder::create()->setHosts($this->config['hosts'])->build();
    }

    /*************************************************************
    /**
     * 索引一个文档
     * 说明：索引没有被创建时会自动创建索引
     */
    public function addOne()
    {
        $params = [];
        $params['index'] = 'alldate';
        $params['type']  = 'cat';
//        $params['id']  = '20180407001';  # 不指定就是es自动分配
        $params['body']  = [
            'First_Name'=>'Shaquanda',
            'Last_Name'=>'Gooden',
            'Address'=>'354 Papere Ridge',
            'City'=>'Orangeburg',
            'State'=>'SC',
            'Zip'=>'29115',
            'Email'=>'lasia.lasia66@icloud.com',
            'Phone'=>'8039287078',
        ];
        $ret = $this->api->index($params);
        var_dump($ret);
    }

    /**
     * 索引多个文档
     * 说明：索引没有被创建时会自动创建索引
     */
    public function addAll()
    {
        $params = [];
        for($i = 1; $i < 21; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'test_index'.$i,
                    '_type'  => 'cat_test',
                    '_id'    => $i,
                ]
            ];
            $params['body'][] = [
                'name' => '小川编程'.$i,
                'content' => '内容'.$i
            ];
        }
        $ret = $this->api->bulk($params);
        var_dump($ret);
    }

    /**
     * 获取一个文档
     */
    public function getOne()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat';
        $params['id']    = '20180407001';
        $ret = $this->api->get($params);
        var_dump($ret);
    }

    /**
     * 搜索文档
     */
    public function search($data,$page=1,$size=20)
    {
        $params = [];
        $params['index'] = 'alldate';
        $params['size'] = $size;
        $params['from'] = ($page-1)*$size;
//        $params['type']  = 'cat';

        foreach ($data as $key=>$dat){
            $params['body']['query']['bool']['must'][]['match_phrase'][$key] = "*$dat*";
        }
        $ret = $this->api->search($params);
       return $ret;
    }

    /**
     * 删除文档
     * 说明：文档删除后，不会删除对应索引。
     */
    public function delete()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['type'] = 'cat';
        $params['id'] = '20180407001';
        $ret = $this->api->delete($params);
        var_dump($ret);
    }

    /*************************************************************
    /**
     * 创建索引
     */
    public function createIndex()
    {
        $params = [];
        $params['index']  = 'alldate';
        $ret = $this->api->indices()->create($params);
        var_dump($ret);
    }

    /**
     * 删除索引：匹配单个 | 匹配多个
     * 说明： 索引删除后，索引下的所有文档也会被删除
     */
    public function deleteIndex()
    {
        $params = [];
        $params['index'] = 'xiaochuan1';  # 删除test_index单个索引
        #$params['index'] = 'test_index*'; # 删除以test_index开始的所有索引
        $ret = $this->api->indices()->delete($params);
        var_dump($ret);
    }

    /*************************************************************
    /**
     * 设置索引配置
     */
    public function setIndexConfig()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['body']['index']['number_of_replicas'] = 0;
        $params['body']['index']['refresh_interval'] = -1;
        $ret = $this->api->indices()->putSettings($params);
        var_dump($ret);
    }

    /**
     * 获取索引配置
     */
    public function getIndexConfig()
    {
        # 单个获取条件写法
        $params['index'] = 'xiaochuan';
        # 多个获取条件写法
        //$params['index'] = ['xiaochuan', 'test_index'];
        $ret = $this->api->indices()->getSettings($params);
        var_dump($ret);
    }

    /**
     * 设置索引映射？
     */
    public function setIndexMapping()
    {
        #  设置索引和类型
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat';

        #  向现有索引添加新类型
        $myTypeMapping = array(
            '_source' => array(
                'enabled' => true
            ),
            'properties' => array(
                'first_name' => array(
                    'type' => 'string',
                    'analyzer' => 'standard'
                ),
                'age' => array(
                    'type' => 'integer'
                )
            )
        );
        $params['body']['cat'] = $myTypeMapping;

        #  更新索引映射
        $ret = $this->api->indices()->putMapping($params);
        var_dump($ret);
    }

    /**
     * 获取索引映射
     */
    public function getIndexMapping()
    {
        #  获取所有索引和类型的映射
        $ret = $this->api->indices()->getMapping();

        /*
        #  获取索引为：xiaochuan的映射
        $params['index'] = 'xiaochuan';
        $ret = $this->api->indices()->getMapping($params);

        #  获取类型为：cat的映射
        $params['type'] = 'cat';
        $ret = $this->api->indices()->getMapping($params);

        #  获取（索引为：xiaochuan和 类型为：cat）的映射
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat'
        $ret = $this->api->indices()->getMapping($params);

        #  获取索引为：xiaochuan和test_index的映射
        $params['index'] = ['xiaochuan', 'test_index'];
        $ret = $this->api->indices()->getMapping($params);
        */
        var_dump($ret);
    }
}