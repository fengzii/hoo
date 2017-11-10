<?php

/* 
 * 请求服务器信息,生成日志
 */
$app_name = "app";

//定义时区
date_default_timezone_set('asia/shanghai');
define('APP_NAME', $app_name);
define('APP_CONTORLLER', $app_name);
define('APP_MODEL', 'app');
define('APP_DATA', $app_name);

//默认服务器列表文件
define('SFILE', 'server.list');
//重连次数
define('TIMES', 3);
//重试等待时间
define('SLEEP', 2);
//限制请求天数
define('DAY_LIMIT', 20);

require dirname(__FILE__) . '/../../system/dev.php';
require APP_PATH.'lib'.DIRECTORY_SEPARATOR.'D.php';
require APP_PATH.'function'.DIRECTORY_SEPARATOR.'fun.php';

fwrite(STDOUT, "\n\nstart at " . date('Y-m-d H:i:s') . "\n");
$data = array();
try{
    if ($argc == 1){
//        $agentId = 29;
//        $server = ServerLog::instance()->getServerByAgent($agentId);
//        $data = ServerLog::instance()->saveLastDay($server);
        $data = ServerLog::instance()->saveLastDay();
    } elseif ($argv[1] == '-l'){
        $data = ServerLog::instance()->saveLastDay();
    } elseif ($argv[1] == '-a'){
        $data = ServerLog::instance()->saveFromStart();
    } elseif ($argv[1] == '-q'){ 
        $servers = ServerLog::instance()->getServerByLog();
    }elseif ($argv[1] == '-t'){
        $agent_id = (int)$argv[2] ? (int)$argv[2] : 85;
        $function = $argv[3];
        $host = ServerLog::instance()->getServerInfo($agent_id);
        $host = $host[0];
        if (empty($host) || empty($function)){
            echo "error!\n";
            exit(1);
        }
        try{
            ServerLog::instance()->$function($host);
        } catch(Exception $e){
            fwrite(STDOUT,'Message:' . $e->getMessage());
        }
    }else {
        fwrite(STDOUT, "\nUsage: makelog [-h|-a|-l|-f]"
            . "\n\r-h : help"
            . "\n\r-a : Save log from server start"
            . "\n\r-l : Save log from lastday"
            . "\n\r-t [function] [host] : Test log "
            . "\n\r-q : Get server information from Datebase"
            . "\nDefault is makelog -l "
            . "\n");
        return 1;
    }
} catch(Exception $e){
     fwrite(STDOUT,'Message:' . $e->getMessage());
}
return $data['status'];
