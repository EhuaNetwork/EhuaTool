<?php


namespace app\api\controller;


use Ehua\Caiji\Selenum;
use Ehua\Dama\Tujian;
use Ehua\Tool\Tool;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use GuzzleHttp\Client;

use think\Controller;
use think\Db;
use think\Exception;
use think\Url;

class Login extends Controller
{

    public $guzz;
    public $driver;

    public function init()
    {
        header("Content-Type: text/html; charset=UTF-8");
// start Firefox with 5 second timeout
        $waitSeconds = 5;  //需等待加载的时间，一般加载时间在0-15秒，如果超过15秒，报错。
        $host = 'http://localhost:9515'; // this is the default
//这里使用的是chrome浏览器进行测试，需到http://www.seleniumhq.org/download/上下载对应的浏览器测试插件
//我这里下载的是win32 Google Chrome Driver 2.25版：https://chromedriver.storage.googleapis.com/index.html?path=2.25/

        $capabilities = DesiredCapabilities::chrome();
//header头
        $useragent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.80 Safari/537.36';
        $options = new ChromeOptions();
        //设置ua
        $options->addArguments(["user-agent={$useragent}"]);
        //无痕模式
        $options->addArguments(["incognito"]);
        $options->addArguments(["incognito"]);
        $options->addArguments(["--disable-blink-features=AutomationControlled"]);
        //linux 兼容
        $options->addArguments(["--no-sandbox"]);
        $options->addArguments(["--disable-gpu"]);
//        $options->addArguments(["blink-settings=imagesEnabled=false"]);//图片加载
//        $options->addArguments(["--headless"]);
        //设置窗口大小
        $options->addArguments(['window-size=1200,768']);
        // 禁用SSL证书
        $capabilities->setCapability('acceptSslCerts', false);
        //无头
        $capabilities->setCapability('ChromeOptions', ['args' => ['-headless']]);


        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        //浏览器设置不加载图片
//        $value = ['profile.managed_default_content_settings.images' => 2];
//        $options->setExperimentalOption('prefs', $value);
//        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        //防检测
        $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
        $options->setExperimentalOption('useAutomationExtension', false);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);;

        //隐性设置15秒
        return $driver = RemoteWebDriver::create($host, $capabilities, 2000);
    }

    /**
     * 获取登录验证码
     * @return \think\response\Json
     */
    public function login_init()
    {
        $driver = $this->init();
//        $driver->manage()->timeouts()->implicitlyWait(2);


        $driver->manage()->deleteAllCookies();

        $SessionID = $driver->getSessionID();


//        $data= $driver->get('https://bot.sannysoft.com');
        $data = $driver->get('https://www.douyin.com/search/111?publish_time=182&sort_type=1&source=normal_search&type=video');

        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="_285c63f4da53bd5cedc023b4fdd71412-scss"]/button'))->click(' ');

        $file_dir = "test.jpg";
        //截屏
        sleep(1);
        $driver->takeScreenshot($file_dir);
        sleep(1);//扫码 5秒后执行获取cookie
        $r = Tool::image_thumb($file_dir, 130, 130, 0, 1, 686, 251);


        \db('chrome_time')->insert(['session_id' => $SessionID, 'time' => time()]);

        return json(['code' => 1, 'msg' => 'ok', 'data' => '/' . $file_dir . '?i=' . rand(1, 9999999), 'sessionid' => $SessionID]);

    }

    /**
     * 执行登录 录入cookie
     * @param $sessionid
     * @return \think\response\Json
     */
    public function login_run($sessionid)
    {

        $host = 'http://localhost:9515'; // this is the default
        $driver = RemoteWebDriver::createBySessionID($sessionid, $host, 2000);

        $headimg = $driver->findElement(WebDriverBy::xpath('//*[@id="root"]/div/div[1]/div/header/div[2]/div[2]/div/div/ul[1]/li[6]/a/div/img'))->getAttribute('src');


        $this->isyzm($driver);


        $cookie = $driver->manage()->getCookies();
        $cook = '';
        foreach ($cookie as $c) {
            $cook .= $c['name'] . '=' . $c['value'];
            $cook .= ';';
        }
        $data = [
            'cookie' => $cook,
            'headimg' => $headimg,
        ];
        db('account')->insert($data);

        $driver->quit();

        return json(['code' => 1, 'msg' => 'ok', 'data' => '']);

    }

    /**
     * 打开指定视频  进行留言
     * @param null $url
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function send($driver, $url = null, $msg = null, $cookies)
    {

        //刷新当前页面
        Selenum::reload($driver);

        //验证码风控处理
        $this->isyzm($driver);


        //按下鼠标下滑
        Selenum::key_down($driver);
        $this->isyzm($driver);

        sleep(1);
        //点击输入框
//        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div[2]/div[2]/div/div/div[1]/div/div/div[2]/div/div/div/div');
        //点击第一个热评
        if (!Selenum::isset($driver, WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[2]/div/div[2]'))
            || Selenum::isset($driver, WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[2]')) && $driver->findElement(WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div'))->getText() == '你要观看的视频不存在') {
            return 'error';
        }


        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[2]/div/div[2]');
        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[2]/div[2]/div/div/div[1]/div/div/div[2]/div/div/div/div');


        //输入内容
        $driver->getKeyboard()->sendKeys($msg);
        sleep(1);
//        //发布
//        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div[2]/div[2]/div/div/div[2]/div/span[3]');

        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[2]/div[2]/div/div/div[2]/div/span[3]');


        //*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div[4]/div[2]/div[1]/div[2]/div/div


    }


    /**
     * 抖音过验证码
     * 原理：
     * 截屏获得验证码界面
     * 接口识别滑块坐标偏移量
     * 由于直接滑动导致验证失败，所有分五次滑动到偏移量的位置
     * @param $driver
     */
    public function isyzm($driver)
    {
        if ($driver->getTitle() == '验证码中间页' || Selenum::isset($driver, WebDriverBy::id('captcha_container'))) {
            $file_dir = "test2.jpg";
            //截屏
            sleep(1);
            $driver->takeScreenshot($file_dir);
            sleep(1);//扫码 5秒后执行获取cookie
            Tool::image_thumb($file_dir, 335, 200, 0, 1, 426, 224);

            $cfg = \db('dm')->where('id', 1)->find();
            $r = Tujian::init($file_dir, $cfg);
            if ($r <= 0) {
                dd('验证码出错');
            }
            $r = (int)$r;

            //获得坐标偏移量  分5词移动至目标点

            $temp = $r / 5;
            $temp = (int)$temp;
            for ($i = 0; $i < 5; $i++) {
                $driver->action()->clickAndHold($driver->findElement(WebDriverBy::xpath('//*[@id="secsdk-captcha-drag-wrapper"]/div[2]')))
                    ->moveByOffset($temp, 0)->perform();
                usleep(50);
            }
            //松开鼠标
            $driver->action()->release()->perform();
            //一次性滑动值偏移量位置
//            $r= $driver->action()->dragAndDropBy($driver->findElement(WebDriverBy::xpath('//*[@id="secsdk-captcha-drag-wrapper"]/div[2]')),$r,0)->perform();
            sleep(5);
        }
    }


    public function setcookie($driver, $cookies)
    {
        $driver->manage()->deleteAllCookies();//清空cookie
        //设置cookie
        $cookies = array_filter($cookies);
        foreach ($cookies as $co) {
            $temp = explode('=', $co);
            $driver->manage()->addCookie([
                'name' => $temp[0],
                'value' => $temp[1],
            ]);
        }
    }

    /**
     * 脚本——采集新闻
     */
    public function caiji_article()
    {
        $driver = $this->init();
        $dat = db('system')->where('key', 'kill')->value('value');
        $driver->get('https://www.douyin.com/search/'.$dat);


        $info = db('account')->where('static', 1)->find();
        $cookies = $info['cookie'];
        $cookies = explode(';', $cookies);

        $this->setcookie($driver, $cookies);

        //刷新当前页面
        Selenum::reload($driver);

        sleep(3);
        \Ehua\Douyin\Tool::isyzm($driver);


        $js = file_get_contents('https://code.jquery.com/jquery-2.1.4.min.js');
        $driver->executeScript($js);


        $i = 1;
        $bool = true;
        while (Selenum::isset($driver, WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div[1]/div[2]/ul/li[1]')) || $bool) {
            try {

                $i++;
                if ($i % 10 == 1) {
                    Selenum::key_down($driver);
                    \Ehua\Douyin\Tool::isyzm($driver);
                }
                if ($i > 100) {
                    $bool = false;
                }

                sleep(2);
                $data['url'] = $driver->findElement(WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div[1]/div[2]/ul/li[1]/div/a[1]'))->getAttribute('href');
                $data['create_time'] = date('Y-m-d H:i:s', time());
                $data['title'] = $driver->findElement(WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div[1]/div[2]/ul/li[1]/div/a[2]/p/span/span/span/span/span'))->getText();
                \db('article')->insert($data);

                $driver->executeScript("
                       var a=document.evaluate('//*[@id=\"root\"]/div/div[2]/div[1]/div[2]/ul/li[1]',document).iterateNext();
                       a.remove();
                ");

            } catch (Exception $e) {

            }

            unset($data);
        }

        $driver->close();


    }


    /**
     *脚本——留言任务
     */
    public function caiji_send()
    {
        $data = \db('task_log')->field('nga_task_log.url,nga_task_log.id,nga_task_log.msg,account.cookie')->where('status', 0)->order('nga_task_log.uid')->join('account', 'account.id=nga_task_log.uid')->select();
        $driver = $this->init();

        $hao = \db('account')->select();

        foreach ($data as $dat) {
            //        $driver = $this->init();


            $r = $driver->getSessionID();//当前浏览器标识
            $driver->get($dat['url']);
            //设置原来cookie
            $cookies = explode(';', $dat['cookie']);
            $this->setcookie($driver, $cookies);

            $r = $this->send($driver, $dat['url'], $dat['msg'], $dat['cookie']);
            if ($r == 'error') {
                \db('task_log')->where('id', $dat['id'])->update(['status' => 2]);
                continue;
            }
            $driver->findElements(WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div'));
            sleep(1);
            if (Selenum::isset($driver, WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div[2]/div[2]/div[1]/div[2]/div/div'))) {
                $WebDriverBy = WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div[2]/div[2]/div[1]/div[2]/div/div');
            } else {
                if (Selenum::isset($driver,WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div/div[2]/div[1]/div[2]/div/div'))) {
                    $WebDriverBy = WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/div/div[2]/div[1]/div[2]/div/div');
                } else {//死号了
                    \db('task_log')->where('id', $dat['id'])->update(['status' => 3]);
                    continue;
                }
            }
            $cid = $driver->findElement($WebDriverBy)->getAttribute('id');

            $cid = str_replace('tooltip_', '', $cid);
            preg_match("/\d+/", $driver->getCurrentURL(), $aid);
            $aid = $aid[0];
            \db('task_log')->where('id', $dat['id'])->update(['status' => 1, 'cid' => $cid, 'aid' => $aid]);
        }

        $driver->close();
    }

    public function caiji_touch()
    {
        $data = \db('task_log')->select();
        $driver = $this->init();

        $hao = \db('account')->select();
        foreach ($hao as $dat) {
            //        $driver = $this->init();

            foreach ($data as $da) {

                $r = $driver->getSessionID();//当前浏览器标识
                $driver->get($da['url']);
                //设置原来cookie
                $cookies = explode(';', $dat['cookie']);
                $this->setcookie($driver, $cookies);

                $this->touch($driver, $da['cid']);
                \db('task_log')->where('id', $dat['id'])->update(['status2' => 1]);

            }
        }


    }

    private function touch($driver, $cid)
    {

//        $guzz = new Client(['verify' => false]);
//
//        $fields = [];
//        $url = "https://www.douyin.com/aweme/v1/web/comment/digg?cid=$cid&aweme_id=$aid&digg_type=1&channel_id=0&app_name=aweme&device_platform=webapp&aid=6383&channel=channel_pc_web&version_code=170400&version_name=17.4.0&cookie_enabled=true&screen_width=1920&screen_height=1080&browser_language=zh-CN&browser_platform=Win32&browser_name=Mozilla&browser_version=5.0+(Windows+NT+10.0%3B+WOW64)+AppleWebKit%2F537.36+(KHTML,+like+Gecko)+Chrome%2F75.0.3770.80+Safari%2F537.36&browser_online=true&msToken=7MjgUG65md0D-M2gh8WQ2NI_SMgHt2zJyN14MZDclKzICwy65YcwX30JyxGTuYNMZyWUI8BP-xVw1HMD7UM4aTgs3iBGWKHDsybrXzVoXs-9BxLz3Mzaug==&X-Bogus=DFSzsdVLzjDdU6VGSNDNrwCr9FFx&_signature=_02B4Z6wo00001--QUaAAAIDAJyyYvKxxqXPvlFUAAJpqMyPs8snAv3ReA0tkmQBWVk5zbEDg0J.BW56JBmF2zZ4zHAr.mui7K3zcJUzdcukVe4GvodP2S2zBIH8x8g.mntSSTxhBonJaCW-i03";
//        $r = $guzz->post($url, [
//            'form_params' => $fields,
//            'headers' => [
//                'Cookie' => $cookie,
//                'x-secsdk-csrf-token' => '0001000000012c1bf9c72428fa5d023f6c2e6e544099d5cf7bd6e9424c2e8ef176c446c148df16b49e679634bcda',
//
//            ]
//        ]);
//        dd($r->getBody()->getContents());

        //刷新当前页面
        Selenum::reload($driver);

        //验证码风控处理
        $this->isyzm($driver);


        //按下鼠标下滑
        Selenum::key_down($driver);
        $this->isyzm($driver);

        sleep(3);
        //点击输入框
//        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div[2]/div[2]/div/div/div[1]/div/div/div[2]/div/div/div/div');
        //点击第一个热评


        if (Selenum::isset($driver, WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[2]/button'))) {
            Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[2]/button');
        }
        $i = 6;
        while ($i > 0) {
            $i--;
            sleep(1);
            $this->isyzm($driver);

            if (Selenum::isset($driver, WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/button')) && $driver->findElement(WebDriverBy::xpath('//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/button'))->gettext() == '展开更多') {
                Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div/div[4]/div[1]/div/div[2]/div[3]/button');
            }

        }
//        var a=$('#tooltip_7027042407757710111').parent().parent().parent().parent().parent().find('svg').parent().eq(0).attr('class');

        if (Selenum::isset($driver, WebDriverBy::id('tooltip_' . $cid))) {
            Selenum::js($driver, file_get_contents('http://libs.baidu.com/jquery/2.1.4/jquery.min.js'));
            Selenum::js($driver, "
                  var a=$('#tooltip_$cid').parent().parent().parent().parent().parent().find('svg').parent().eq(0).attr('class');
                        if(a.split(' ').length==1){
                           $('#tooltip_$cid').parent().parent().parent().parent().parent().find('svg').parent().eq(0).click();
                        }else{
                            console.log(1111111111)
                         }
            ");
        } else {
            echo 11111;
            //第一条留言没有我方留言的情况
        }
    }


    /**
     * 关闭超时的chrome进程
     */
    public function out_chrome()
    {
        $data = \db('chrome_time')->where('time', '<', time() - 600)->select();
        foreach ($data as $dat) {
            try {
                $driver = Selenum::init2($dat['session_id']);
                $driver->close();
            } catch (Exception $exception) {

            }

            \db('chrome_time')->where('id', $dat['id'])->delete();
        }
    }

}