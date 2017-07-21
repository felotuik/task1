<?php


//全局常量
define('PATH', dirname(__FILE__)); //框架目录
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

//环境配置
date_default_timezone_set('PRC');
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors', 'Off');
ini_set('max_execution_time', '0');

//服务开关
define('SET_apiServer', true); //接口服务器

//其它
include 'common/function.php';
spl_autoload_register('autoLoadClass');

//自动加载类
function autoLoadClass($class)
{
    $libPath = PATH.'/'.str_replace('\\', '/', $class).'.php';
    if (is_file($libPath)) {
        include $libPath;
    }
}


//GET方法封装
function get($name, $fitt = '')
{
    if (isset($_GET[$name])) {
        if ($fitt !== '') {
            switch ($fitt) {
                case 'int':
                    return is_numeric($_GET[$name]) ? $_GET[$name] : false;
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return $_GET[$name];
        }
    } else {
        return false;
    }
}


//POST方法封装
function post($name, $fitt = '')
{
    if (isset($_POST[$name])) {
        if ($fitt !== '') {
            switch ($fitt) {
                case 'int':
                    return is_numeric($_POST[$name]) ? $_POST[$name] : false;
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return $_POST[$name];
        }
    } else {
        return false;
    }
}


//接口处理函数：返回JSON
function show($code = 0, $msg = '', $data = array()){
    if (!is_numeric($code)) {
        return false;
    }
    $result = array(
        'code' => $code,
        'msg' => urlencode($msg),
        'data' => $data
    );
    if ($data == array()) {
        unset($result['data']);
    }
    return urldecode(json_encode($result));
}





function formatSize($b, $times = 0) {
    if ($b > 1024) {
        $temp = $b / 1024;
        return formatSize($temp, $times + 1);
    } else {
        $unit = 'B';
        switch ($times) {
            case '0' : $unit = 'B';
                break;
            case '1' : $unit = 'KB';
                break;
            case '2' : $unit = 'MB';
                break;
            case '3' : $unit = 'GB';
                break;
            case '4' : $unit = 'TB';
                break;
            case '5' : $unit = 'PB';
                break;
            case '6' : $unit = 'EB';
                break;
            case '7' : $unit = 'ZB';
                break;
            default : $unit = '单位未知';
        }
        return sprintf('%.2f', $b).$unit;
    }
}


function randString($length, $allRandStr = false){
    $str = '';
    $strPol = 'a0b1c2d3e4f5g6h7i8j9k0l1m2n3o4p5q6r7s8t9u0v1w2x3y4z5';
    if ($allRandStr == true) {
        $strPol = 'Aa0Bb1Cc2Dd3Ee4Ff5Gg6Hh7Ii8Jj9Kk0Ll1Mm2Nn3Oo4Pp5Qq6Rr7Ss8Tt9Uu0Vv1Ww2Xx3Yy4Zz5';
    }
    $max = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

function bcurl($data){
    $ch = curl_init(); //创建curl

    if (is_string($data)) {
        $data = array(
            'url' => $data
        );
    }

    curl_setopt($ch, CURLOPT_URL, $data['url']);
    
    //设置了post数据则用post方式提交
    if (isset($data['post'])) {
        curl_setopt($ch, CURLOPT_POST, 1); //设置POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']); //POST数据
    }

    //有设置数据则提交HTTP请求头
    if (isset($data['referer'])) {
        curl_setopt($ch, CURLOPT_REFERER, $data['referer']); //请求头
    }

    //是否提交cookie,默认否
    if (isset($data['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIE, $data['cookie']); //cookie
    }

    if (isset($data['header'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data['header']);
    }

    //请求超时默认值
    if (!isset($data['timeout'])) {
        $data['timeout'] = 45;
    }

    //跳过SSL验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //获取页面内容，不直接输出到页面
    curl_setopt($ch, CURLOPT_HEADER, 0); //1输出文件头
    curl_setopt($ch, CURLOPT_TIMEOUT, $data['timeout']); //超时20s
    $msg = curl_exec($ch); //执行
    curl_close($ch);
    return($msg);
}


/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = 1) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return false;
        }
    }
}


    function moveDir($oldDir, $aimDir, $overWrite = false) {
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        $oldDir = str_replace('', '/', $oldDir);
        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!file_exists($aimDir)) {
            FileUtil :: createDir($aimDir);
        }
        @ $dirHandle = opendir($oldDir);
        if (!$dirHandle) {
            return false;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                FileUtil :: moveFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
                FileUtil :: moveDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
        }
        closedir($dirHandle);
        return rmdir($oldDir);
    }


?>