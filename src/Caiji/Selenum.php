<?php

namespace Ehua\Caiji;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverSelect;
/**
 * php-Selenum 操作类
 * 相关文档  http://ask.sov5.cn/q/7598k2fKfn
 * 环境下载地址：https://www.aliyundrive.com/s/sKFF23PN8Kq
 * Class Selenum
 * @package Ehua\Caiji
 */
class Selenum
{

    static function setSelect($driver,$xpath){
        $elm = $driver->findElement(
            $xpath
        );
        $selectAcao = new WebDriverSelect($elm);
        $selectAcao->selectByValue("CN");
    }
    /**
     * 强制点击某个元素
     * @param $driver
     * @param $xpath
     */
    static function click($driver, $xpath)
    {
        $driver->action()->moveToElement($driver->findElement($xpath))->click()->perform();
    }

    static function getcookie($driver){
        $cookies = $driver->manage()->getCookies();
        $cook = '';
        foreach ($cookies as $c) {
            $cook .= $c['name'] . '=' . $c['value'];
            $cook .= ';';
        }
        return $cook;
    }
    static function setcookie($driver, $cookies)
    {
        $cookies = explode(';', $cookies);
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
     * 初始化方法
     * @return RemoteWebDriver
     */
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
       $options->addArguments(["blink-settings=imagesEnabled=false"]);//图片加载
        $options->addArguments(["--headless"]);
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
     * 启动已知的线程
     * @param $sessionid
     * @return mixed
     */
    static function Session_init($sessionid)
    {
        $host = 'http://localhost:9515'; // this is the default
        $driver = RemoteWebDriver::createBySessionID($sessionid, $host, 2000);
        return $driver;
    }

    /**
     * 生成随机UA
     * @return string
     */
    private function getua()
    {
        $data = [
            'Opera/8.73.(Windows NT 5.1; gez-ER) Presto/2.9.187 Version/11.00',
            'Mozilla/5.0 (Windows NT 6.0) AppleWebKit/533.2 (KHTML, like Gecko) Chrome/51.0.889.0 Safari/533.2',
            'Opera/9.30.(Windows NT 6.0; the-NP) Presto/2.9.170 Version/11.00',
            'Opera/9.79.(Windows 98; fi-FI) Presto/2.9.173 Version/11.00',
            'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1; Trident/5.1)',
            'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1; Trident/5.1)',
            'Mozilla/5.0 (compatible; MSIE 5.0; Windows NT 4.0; Trident/4.0)',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.0; Trident/3.0)',
            'Mozilla/5.0 (Windows; U; Windows NT 6.0) AppleWebKit/531.8.6 (KHTML, like Gecko) Version/5.0 Safari/531.8.6',
            'Mozilla/5.0 (Windows NT 4.0; tr-TR; rv:1.9.0.20) Gecko/2011-02-20 13:52:34 Firefox/3.6.13',
            'Mozilla/5.0 (compatible; MSIE 5.0; Windows NT 5.01; Trident/5.0)',
            'Mozilla/5.0 (Windows NT 6.1; ro-RO; rv:1.9.1.20) Gecko/2015-06-22 23:56:25 Firefox/6.0',
            'Opera/9.38.(Windows NT 6.2; ne-NP) Presto/2.9.181 Version/11.00',
            'Mozilla/5.0 (Windows NT 4.0; niu-NZ; rv:1.9.2.20) Gecko/2011-09-07 16:08:26 Firefox/3.8',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; Trident/3.0)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.2; Trident/4.0)',
            'Mozilla/5.0 (compatible; MSIE 5.0; Windows NT 5.1; Trident/3.0)',
            'Opera/8.76.(Windows NT 5.0; ak-GH) Presto/2.9.176 Version/12.00',
            'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 6.2; Trident/4.1)',
            'Mozilla/5.0 (Windows 98; Win 9x 4.90) AppleWebKit/531.2 (KHTML, like Gecko) Chrome/55.0.849.0 Safari/531.2',

        ];
        $rand = rand(0, count($data) - 1);
        return $data[$rand];
    }



    /**
     * 执行js脚本
     * @param $driver
     * @param $js
     */
    static function js($driver, $js)
    {
        $driver->executeScript($js);
    }

    /**
     * 刷新页面
     * @param $driver
     */
    static function reload($driver)
    {
        $driver->navigate()->refresh();
    }

    /**
     * 按下键  下拉窗口
     * @param $driver
     */
    static function key_down($driver)
    {
        $driver->getKeyboard()->pressKey(WebDriverKeys::PAGE_DOWN);
    }

    /**
     * 元素是否存在
     * @param $driver
     * @param $WebDriverBy
     * @return bool
     */
    static function isset($driver, $WebDriverBy)
    {
        try {
            $nextbtn = $driver->findElement($WebDriverBy);
            return true;
        } catch (WebDriverException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 切换最后一个window
     * @param $driver
     */
    static function switchToEndWindow($driver)
    {

        $arr = $driver->getWindowHandles();
        foreach ($arr as $k => $v) {
            if ($k == (count($arr) - 1)) {
                $driver->switchTo()->window($v);
            }
        }
    }

    /**
     * 切换至第一个window
     * @param $driver
     */
    static function switchToHomeWindow($driver)
    {

        $arr = $driver->getWindowHandles();
        foreach ($arr as $k => $v) {
            if ($k == 0) {
                $driver->switchTo()->window($v);
            }
        }
    }


}