<?php
/**
 * 数据库相关的配置
 */
return array(
	'driver' => 'Mysql',
	"host" => "10.0.0.21:3306",//主机地址
	"dbname" => "legend_manage",//数据库名
	"username" => "legend_admin", //连接用户名
	"password" => "legend@scan",//连接密码
	"pconnect" => false,//是否持久化链接
	"charset" => "utf8"//连接用的字符集
);


?>