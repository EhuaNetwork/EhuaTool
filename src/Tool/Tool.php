<?php

namespace Ehua\Tool;

class Tool
{

    /**
     * file_put_contents 优化版 自动创建目录
     * @param $path
     * @param $body
     */
    static function file_put_contents_ehua($path, $body)
    {
        $path = explode('/', $path);
        $temp = array_pop($path);
        $path = implode('/', $path);
        self::dir_create($path);
        file_put_contents($path . '/' . $temp , $body);
    }

    /**
     * 获取文件数组
     * @param $file_path
     * @return array
     */
    function file_to_array($file_path)
    {
        $file = $fp = fopen($file_path, 'r') or die("Unable to open file!");
        while (!feof($file)) {
            $fp = fgets($file);
            if ($fp) {
                $content[] = $fp;
            }
        }

        fclose($file);
        return $content;
    }
    /**
     * 递归创建文件目录
     * @param $dir
     */
    static function dir_create($dir)
    {
        if (is_dir($dir) || @mkdir($dir, 0777)) {
        } else {
            self::dir_create(dirname($dir));
            if (@mkdir($dir, 0777)) {
            }
        }
    }

    /**
     * 只取中文
     * @param $chars
     * @param string $encoding
     * @return string
     */
    static function str_To_chinese($chars, $encoding = 'utf8')
    {
        $pattern = ($encoding == 'utf8') ? '/[\x{4e00}-\x{9fa5}]/u' : '/[\x80-\xFF]/';
        preg_match_all($pattern, $chars, $result);
        $temp = join('', $result[0]);
        return $temp;
    }

    /**
     * 字符串转utf8编码
     * @param $str
     * @return string
     */
    static function str_To_Utf8($str)
    {
        $encode = mb_detect_encoding($str, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        if ($encode == 'UTF-8') {
            return $str;
        } else {
            return mb_convert_encoding($str, 'UTF-8', $encode);
        }
    }

    /**
     * 字符串 转url格式
     * @param $str
     * @return mixed|string|string[]
     */
    static function str_To_Url($str)
    {
        preg_match_all("/[\x{4e00}-\x{9fff}]+/u", $str, $matches);
        if (!empty($matches[0])) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $str = str_replace($matches[0][$i], urlencode($matches[0][$i]), $str);
            }
        }
        return $str;
    }

    /**
     * 字符串加密
     * @param $string
     * @param string $key
     * @param int $expiry
     * @param string $default_key
     * @return string|string[]
     */
    static function str_encode($string, $key = '', $expiry = 0, $default_key = 'a!takA:dlmcldEv,e')
    {
        $ckeyLength = 4;
        $key = md5($key ? $key : $default_key); //解密密匙
        $keya = md5(substr($key, 0, 16)); //做数据完整性验证
        $keyb = md5(substr($key, 16, 16)); //用于变化生成的密文 (初始化向量IV)
        $keyc = substr(md5(microtime()), -$ckeyLength);
        $cryptkey = $keya . md5($keya . $keyc);
        $keyLength = strlen($cryptkey);
        $string = sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $stringLength = strlen($string);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLength]);
        }
        $box = range(0, 255);
// 打乱密匙簿，增加随机性
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 加解密，从密匙簿得出密匙进行异或，再转成字符
        $result = '';
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        $result = $keyc . str_replace('=', '', base64_encode($result));
        $result = str_replace(array('+', '/', '='), array('-', '_', '.'), $result);
        return $result;
    }

    /**
     * 字符解密，一次一密,可定时解密有效
     *
     * @param string $string 密文
     * @param string $key 解密密钥
     * @return string 解密后的内容
     */
    static function str_decode($string, $key = '', $default_key = 'a!takA:dlmcldEv,e')
    {
        $string = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
        $ckeyLength = 4;
        $key = md5($key ? $key : $default_key); //解密密匙
        $keya = md5(substr($key, 0, 16)); //做数据完整性验证
        $keyb = md5(substr($key, 16, 16)); //用于变化生成的密文 (初始化向量IV)
        $keyc = substr($string, 0, $ckeyLength);
        $cryptkey = $keya . md5($keya . $keyc);
        $keyLength = strlen($cryptkey);
        $string = base64_decode(substr($string, $ckeyLength));
        $stringLength = strlen($string);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLength]);
        }
        $box = range(0, 255);
// 打乱密匙簿，增加随机性
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
// 加解密，从密匙簿得出密匙进行异或，再转成字符
        $result = '';
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0)
            && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
        ) {
            return substr($result, 26);
        } else {
            return '';
        }
    }


    /**
     * 图片等比压缩到指定指定尺寸
     * @param string $source_path
     * @param string $target_width
     * @param string $target_height
     */
    static function image_Cropper($source_path, $target_width, $target_height)
    {
        $source_info = getimagesize($source_path);
        $source_width = $source_info[0];
        $source_height = $source_info[1];
        $source_mime = $source_info['mime'];
        $source_ratio = $source_height / $source_width;
        $target_ratio = $target_height / $target_width;
        if ($source_ratio > $target_ratio) {
            // image-to-height
            $cropped_width = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        } elseif ($source_ratio < $target_ratio) {
            //image-to-widht
            $cropped_width = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        } else {
            //image-size-ok
            $cropped_width = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }
        switch ($source_mime) {
            case 'image/gif':
                $source_image = imagecreatefromgif($source_path);
                break;
            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($source_path);
                break;
            default:
                return;
                break;
        }
        $target_image = imagecreatetruecolor($target_width, $target_height);
        $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
        // copy
        imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
        // zoom
        imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
        header('Content-Type: image/jpeg');
        imagejpeg($target_image);
        imagedestroy($source_image);
        imagedestroy($target_image);
        imagedestroy($cropped_image);
    }


    /**
     * 图片裁剪函数，支持指定定点裁剪和方位裁剪两种裁剪模式
     * @param <string> $src_file 原图片路径
     * @param <int> $new_width 裁剪后图片宽度（当宽度超过原图片宽度时，去原图片宽度）
     * @param <int> $new_height 裁剪后图片高度（当宽度超过原图片宽度时，去原图片高度）
     * @param <int> $type 裁剪方式，1-方位模式裁剪；0-定点模式裁剪。
     * @param <int> $pos 方位模式裁剪时的起始方位（当选定点模式裁剪时，此参数不起作用）
     *                                      1为顶端居左，2为顶端居中，3为顶端居右；
     *                                      4为中部居左，5为中部居中，6为中部居右；
     *                                      7为底端居左，8为底端居中，9为底端居右；
     * @param <int> $start_x 起始位置X （当选定方位模式裁剪时，此参数不起作用）
     * @param <int> $start_y 起始位置Y（当选定方位模式裁剪时，此参数不起作用）
     * @return <string>                 裁剪图片存储路径
     */
    static function image_thumb($src_file, $new_width, $new_height, $type = 1, $pos = 5, $start_x = 0, $start_y = 0)
    {
        $pathinfo = pathinfo($src_file);
        $dst_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $new_width . 'x' . $new_height . '.' . $pathinfo['extension'];
        if (!file_exists($dst_file)) {
            if ($new_width < 1 || $new_height < 1) {
                echo "params width or height error !";
                return;
                //exit();
            }
            if (!file_exists($src_file)) {
                echo $src_file . " is not exists !";
                return;
                // exit();
            }
            $img_type = pathinfo($src_file, PATHINFO_EXTENSION);
            $img_type = strtolower($img_type);
            /* 载入图像 */
            switch ($img_type) {
                case 'jpg':
                    if (@!($src_img = imagecreatefromjpeg($src_file))) {
                        if (@!($src_img = imagecreatefrompng($src_file))) {
                            $src_img = imagecreatefromgif($src_file);
                        }
                    }
                    break;
                case 'png':
                    if (@!($src_img = imagecreatefrompng($src_file))) {
                        if (@!($src_img = imagecreatefromjpeg($src_file))) {
                            $src_img = imagecreatefromgif($src_file);
                        }
                    }
                    break;
                case 'gif':
                    if (@!($src_img = imagecreatefromgif($src_file))) {
                        if (@!($src_img = imagecreatefrompng($src_file))) {
                            $src_img = imagecreatefromjpeg($src_file);
                        }
                    }
                    break;
                default:
                    echo "载入图像错误!";
                    return;
                //exit();
            }
            /* 获取源图片的宽度和高度 */
            $src_width = imagesx($src_img);
            $src_height = imagesy($src_img);
            /* 计算剪切图片的宽度和高度 */
            $mid_width = ($src_width < $new_width) ? $src_width : $new_width;
            $mid_height = ($src_height < $new_height) ? $src_height : $new_height;
            /* 初始化源图片剪切裁剪的起始位置坐标 */
            switch ($pos * $type) {
                case 1://1为顶端居左
                    $start_x = 0;
                    $start_y = 0;
                    break;
                case 2://2为顶端居中
                    $start_x = ($src_width - $mid_width) / 2;
                    $start_y = 0;
                    break;
                case 3://3为顶端居右
                    $start_x = $src_width - $mid_width;
                    $start_y = 0;
                    break;
                case 4://4为中部居左
                    $start_x = 0;
                    $start_y = ($src_height - $mid_height) / 2;
                    break;
                case 5://5为中部居中
                    $start_x = ($src_width - $mid_width) / 2;
                    $start_y = ($src_height - $mid_height) / 2;
                    break;
                case 6://6为中部居右
                    $start_x = $src_width - $mid_width;
                    $start_y = ($src_height - $mid_height) / 2;
                    break;
                case 7://7为底端居左
                    $start_x = 0;
                    $start_y = $src_height - $mid_height;
                    break;
                case 8://8为底端居中
                    $start_x = ($src_width - $mid_width) / 2;
                    $start_y = $src_height - $mid_height;
                    break;
                case 9://9为底端居右
                    $start_x = $src_width - $mid_width;
                    $start_y = $src_height - $mid_height;
                    break;
                default://随机
                    break;
            }
            // 为剪切图像创建背景画板
            $mid_img = imagecreatetruecolor($mid_width, $mid_height);
            //拷贝剪切的图像数据到画板，生成剪切图像
            imagecopy($mid_img, $src_img, 0, 0, $start_x, $start_y, $mid_width, $mid_height);
            // 为裁剪图像创建背景画板
            $new_img = imagecreatetruecolor($new_width, $new_height);
            //拷贝剪切图像到背景画板，并按比例裁剪
            imagecopyresampled($new_img, $mid_img, 0, 0, 0, 0, $new_width, $new_height, $mid_width, $mid_height);
            /* 按格式保存为图片 */
            switch ($img_type) {
                case 'jpg':
                    imagejpeg($new_img, $src_file, 100);
                    break;
                case 'png':
                    imagepng($new_img, $src_file, 9);
                    break;
                case 'gif':
                    imagegif($new_img, $src_file, 100);
                    break;
                default:
                    break;
            }
        }
        return ltrim($src_file, '.');
    }
}
