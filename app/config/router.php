<?php

return array(
		'control_action'     => 'index.index', // 默认的入口控制器
		'route'				 => 'get',        //支持 get path_info regex
		'ext'     => '.html',
        'urls'     => array(
			  'dianying'        => array('act' => 'index.dianying', 'params' => '(?P<name>(.*)+)'),
			  'dianshi'        => array('act' => 'index.dianshi', 'params' => '(?P<name>(.*)+)'),
			  'zongyi'        => array('act' => 'index.zongyi', 'params' => '(?P<name>(.*)+)'),
			  'dongman'        => array('act' => 'index.dongman', 'params' => '(?P<name>(.*)+)'),
			  'v'        => array('act' => 'index.view', 'params' => '(?P<name>(.*)+)'),
			  'yun_full'        => array('act' => 'yun.full', 'params' => ''),
			  'search'        => array('act' => 'yun.search', 'params' => ''),
			  'push'        => array('act' => 'yun.push', 'params' => '')

)
);
?>