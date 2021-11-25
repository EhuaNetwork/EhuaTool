<?php


namespace Ehua\Douyin;


use Ehua\Caiji\Selenum;
use Ehua\Dama\Tujian;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Tool
{

    /**
     * 抖音过验证码
     * 原理：
     * 截屏获得验证码界面
     * 接口识别滑块坐标偏移量
     * 由于直接滑动导致验证失败，所有分五次滑动到偏移量的位置
     * @param $driver
     */
    static function isyzm($driver)
    {
        if ($driver->getTitle() == '验证码中间页' || Selenum::isset($driver, WebDriverBy::id('captcha_container'))) {
            $file_dir = "test2.jpg";
            //截屏
            sleep(1);
            $driver->takeScreenshot($file_dir);
            sleep(1);//扫码 5秒后执行获取cookie
            \Ehua\Tool\Tool::image_thumb($file_dir, 335, 200, 0, 1, 426, 224);

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
                usleep(30);
            }
            //松开鼠标
            $driver->action()->release()->perform();
            //一次性滑动值偏移量位置
//            $r= $driver->action()->dragAndDropBy($driver->findElement(WebDriverBy::xpath('//*[@id="secsdk-captcha-drag-wrapper"]/div[2]')),$r,0)->perform();
                        $driver->executeScript("
            document.getElementById('captcha_container').remove()
            ");
            sleep(3);
        }
    }

    static function init()
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
     * 打开指定视频  进行留言
     * @param null $url
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    static function send($url = null, $msg = null)
    {
        $url = 'https://www.douyin.com/video/7005893214792125732?previous_page=main_page&tab_name=home';
        $msg = '666';
        $driver = self::init();
        $r = $driver->getSessionID();//当前浏览器标识


        $driver->get($url);

        //设置cookie
        $info = db('account')->where('id', 1)->find();
        $cookies = $info['cookie'];
        $cookies = explode(';', $cookies);
        self::setcookie($driver, $cookies);

        //刷新当前页面
        Selenum::reload($driver);

        //验证码风控处理
        self::isyzm($driver);


        //按下鼠标下滑
        Selenum::key_down($driver);
        self::isyzm($driver);

        sleep(1);
        //点击输入框
        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div[2]/div[2]/div/div/div[1]/div/div/div[2]/div/div/div/div');
        //输入内容
        $driver->getKeyboard()->sendKeys($msg);
        sleep(1);
//        //发布
        Selenum::click($driver, '//*[@id="root"]/div/div[2]/div/div[1]/div[3]/div/div[2]/div[2]/div/div/div[2]/div/span[3]');


    }

    static function setcookie($driver, $cookies)
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


}