<?php

return array(
		'control_action'     => 'index.index', // 默认的入口控制器
		'route'				 => 'get',        //支持 get path_info regex
		'ext'     => '.html',
        'urls'     => array(
			  'test'        => array('act' => 'test.s', 'params' => '(?P<name>\w+)/(?P<digit>\w+)'),
			  'test'        => array('act' => 'test.s', 'params' => ''),
			  'login'        => array('act' => 'user.login', 'params' => ''),

)
);
?>