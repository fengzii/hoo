<?php
/**
 * 数据库相关的配置
 */
return array(
    'driver' => 'mysql',
	'master' => array('m1'),
	'slave' => FALSE,
	'm1' => array(
		'host' => 'localhost:3306',//主机地址
	       'dbname' => 'app_cms',//数据库名
	       'username' => 'root', //连接用户名
	       'password' => '',//连接密码
	       'pconnect' => false,//是否持久化链接
	       'charset' => 'utf8'//连接用的字符集
	)
);


?>