<?php


namespace app\api\controller;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use think\Controller;

class Register extends Controller
{

    //前端获取手机号接口
    public function getphone()
    {

        //项目id
        $xmid = '14016';

        $hei = '';
        //米云账号
        $config = [
            'name' => 'MY.231220714',
            'pwd' => '150638',
        ];
        $glhmd = '162,165,163,166,167,169,171,170,172,175,173,177,179';
        $jiema = new \Jiema\Miyun($config);
        $res = $jiema->getphone($xmid, '', '', 0, $glhmd, '', 'ehua999');
        if ($res['message'] == 'ok') {
            db('getsms')->insert([
                'phone' => $res['mobile'],
                'create_time' => time(),
            ]);


            $this->success($res['message'], '', $res['mobile']);

        } else {
            $this->error($res['message'], '', $res['mobile']);
        }
    }


    public function sendmsg($phone)
    {
        $session_id = $this->getcode($phone);//后台操作发送验证码
        if ($session_id['code'] == -1) {
            $this->error($session_id['msg'], '', ['phone' => $phone, 'session' => $session_id['sessionid']]);
        } else {
            $this->success($session_id['msg'], '', ['phone' => $phone, 'session' => $session_id['sessionid']]);
        }
    }

    //前端获取验证码接口
    public function getsms($phone = '13323924957', $ctime = '15115111')
    {


        $xmid = '14016';

        $config = [
            'name' => 'MY.231220714',
            'pwd' => '150638',
        ];
        $jiema = new \Jiema\Miyun($config);

        $res = $jiema->getsms($xmid, $phone);
        if (strlen($res['modle']) > 4) {
            $jiema->getdel($xmid, $phone);

            preg_match("/证码是 \w+ 10分钟/", $res['modle'], $r);
            $r = str_replace(' 10分钟', '', str_replace('证码是 ', '', $r[0]));

            $this->success($res['message'], '', $r);
        } else {
            $this->error($res['message'], '', $res['code']);
        }

    }


    //后台模拟发送验证码
    public function getcode($phone = null)
    {
//        $phone='15725517053';

        $chrome = new Login();
        $driver = $chrome->init();
        $SessionID = $driver->getSessionID();
        $r = $driver->get("https://bbs.nga.cn/nuke.php?__lib=login&__act=login_ui");
        $chrome->setcookie($driver);
//        $cookies = $driver->manage()->getCookies();

        //切换iframe
        $r = $driver->switchTo()->frame(0);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/div[1]/a[1]'))->click(' ');

        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[3]'))->sendKeys($phone);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/a[1]'))->click(' ');

        $file_dir = "test.jpg";

        //截屏
        $driver->takeScreenshot($file_dir);

        //识别二维码
        $code = $chrome->orc($file_dir);
//识别失败跳出
        if (!$code) {
            db('log')->insert(['msg' => '打码失败', 'data' => 0, 'create_time' => date('Y-m-d H:i:s', time())]);
            $driver->close();
            return ['code' => -1, 'msg' => '打码失败', 'data' => '', 'sessionid' => $SessionID];
        }

        //输入二维码
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="name"]'))->sendKeys($code);
        $ul = $driver->findElement(WebDriverBy::xpath('/html/body/div/a[1]'))->click(' ');

        sleep(3);


        //获取弹窗内容
        $alt = $driver->switchTo()->alert();
        $ress = $alt->getText();
        $alt->accept();

        if (preg_match("/发送成功/", $ress)) {
            return ['code' => 1, 'msg' => $ress, 'data' => $SessionID, 'sessionid' => $SessionID];
        } else {
            $driver->close();
            return ['code' => -1, 'msg' => $ress, 'data' => '', 'sessionid' => $SessionID];
        }
    }


    public function init($sessionid = null, $name = null, $pwd = null, $phone = null, $code = null, $mail = null)
    {

        $dat = [
            'name' => $name,
            'pwd' => $pwd,
            'phone' => $phone,
            'code' => $code,
            'mail' => $mail,
        ];
//        $dat = [
//            'name' => 'ORVHBDEV',
//            'pwd' => 'STTDPJEX',
//            'phone' => '18003925441',
//            'code' => 'KWRNM5',
//            'mail' => 'LWGLPKMI@qq.com',
//        ];

        //初始化
        $chrome = new Login();
//        $sessionid = '960020572fb5d1dc91d505e7a51ee09b';
        $host = 'http://localhost:9515'; // this is the default

        //使用共享sessionid
        $driver = RemoteWebDriver::createBySessionID($sessionid, $host, 2000);

        $file_dir = "test.jpg";


//        $r = $driver->get("https://bbs.nga.cn/nuke.php?__lib=login&__act=login_ui");
//        $chrome->setcookie($driver);
////        $cookies = $driver->manage()->getCookies();
//
//        //切换iframe
//        $r = $driver->switchTo()->frame(0);
//
//        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/div[1]/a[1]'))->click(' ');

        //录入注册信息
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[1]'))->clear()->sendKeys($dat['name']);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[2]'))->clear()->sendKeys($dat['mail']);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[3]'))->clear()->sendKeys($dat['phone']);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[4]'))->clear()->sendKeys($dat['code']);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[5]'))->clear()->sendKeys($dat['pwd']);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/input[6]'))->clear()->sendKeys($dat['pwd']);
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="main"]/div/a[2]'))->click(' ');
        //点击用户协议
        $ul = $driver->findElement(WebDriverBy::xpath('/html/body/div[2]/a'))->click(' ');

        //截屏
        $driver->takeScreenshot($file_dir);

        //识别二维码
        $code = $chrome->orc($file_dir);
//识别失败跳出
        if (!$code) {
            db('log')->insert(['msg' => '打码失败', 'data' => 0, 'create_time' => date('Y-m-d H:i:s', time())]);
            return false;
        }

        //输入二维码
        $ul = $driver->findElement(WebDriverBy::xpath('//*[@id="name"]'))->sendKeys($code);
        $ul = $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/a[1]'))->click(' ');

        sleep(1);


        //获取弹窗内容
        $alt = $driver->switchTo()->alert();
        $ress = $alt->getText();
        $alt->accept();
        if (!preg_match("/成功/", $ress)) {
            $this->error($ress);die;
        }


        $datas = [
            'cookie' => '',
            'name' => $dat['name'],
            'pwd' => $dat['pwd'],
            'mail' => $dat['mail'],
            'phone' => $dat['phone'],
            'msg' => $ress
        ];
        db('account')->insert($datas);
        $driver->quit();
        $this->success('注册成功');die;
    }
}