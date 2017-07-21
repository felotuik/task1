<?php

    include 'basic.php';


    $a = get('a');
    $m = get('m');
    if ($a !== false) {
        $apiPath = 'Ports/'.$a.'.php';
        if (is_file($apiPath)) {
            $apiPath = 'Ports\\'.$a;
            $app = new $apiPath;
            if ($m !== false) {
                if (method_exists($app, $m)) {
                    $app->$m();
                } else {
                    exit(show(2, '请求方法不存在'));
                }
            }
        } else {
            exit(show(1, '请求服务不存在'));
        }
    }








?>