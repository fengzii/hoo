<?php

class Router
{
    private static $_instance;
    private  $_action = '';
    private  $_control ='';

    public function __construct()
    {
        if (!in_array(Request::instance()->method(), array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new HttpExceptions('HTTP Method not supported.', 405);
            return;
        }
        $this->set_control_action(Config::instance()->router['control_action']);
        $this->init(Config::instance()->router['route']);
    }

    public function __destruct()
    {
    }


    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function init($type)
    {
        switch ($type) {
            case 'regex':
                $this->regex();
                break;

            default:
                $this->get();
            break;
        }
        return ;
    }

    private function get()
    {
        $this->set_control_action(Request::instance()->get('act'));
    }

    private function pathinfo()
    {

        //默认控制器
        $this->default_controller();

        //preg_match("/^(.*)((\/\d+)+)\/?$/U", $path_info, $match);
        if ( preg_match("/^\/(\w+)/", $path_info, $match) )
        {
            if($match[1]) $this->action = $match[1];
        };

        if ( preg_match("/^\/(\w+)\/(\w+)/", $path_info, $match) )
        {
            //取得controller action
            if($match[1]) $this->action = $match[1];
            if($match[2]) $this->method = $match[2];
            //取得剩下参数
            $res = preg_replace('@(\w+)\/([^,\/]+)@e', 'Request::instance()->set(\'\\1\',"\\2");', str_replace($match[0],'',$path_info));
        }

    }
    /**
     * 正则路由
     *
     * @author scan 232832288@qq.com
     */
    private function regex()
    {

        $alias = $params = NULL;
        $alias = $this->_get_path_info();
        if (strstr($alias, '/')) {
            list($alias, $params) = explode('/', $this->_get_path_info(), 2);
        }
        if (empty($alias)) {
            return FALSE;
        }
        if (empty(Config::instance()->router['urls'][$alias])) {
            throw new Http404Exceptions('not fount');
        }
        $this->set_control_action(Config::instance()->router['urls'][$alias]['act']);
        if (empty($params) || empty(Config::instance()->router['urls'][$alias]['params'])) {
            return FALSE;
        }
        if (preg_match('#^'.Config::instance()->router['urls'][$alias]['params'].'/?$#', $params, $matches)) {
            Request::instance()->sets($matches);
            return FALSE;
        }
    }

    /**
     * 初始化 path_info
     *
     * @author scan 232832288@qq.com
     */
    private function _get_path_info()
    {

        $path_info = $_SERVER[Config::instance()->router['protocol']];
        //$path_info = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REQUEST_URI']);
        if (empty($path_info)) {
            return NULL;
        }
        if (strstr( $path_info, '/favicon.ico' )){
            Response::instance()->sendStatus(404);
            exit;
        }
        if (!empty(Config::instance()->router['ext'])){
            $path_info = str_replace(Config::instance()->router['ext'],'', $path_info);
        }
        return ltrim($path_info, '/');
    }



    public function get_action()
    {
        return $this->_action;
    }

    public function get_control()
    {
        return $this->_control;
    }

    public function set_action($action)
    {
        if ( !empty($action) ){
			$this->_action = $action;
		}
    }

    public function set_control($control)
    {
        if ( !empty($control) ){
			$this->_control = $control;
		}
    }

    /**
     * 设置默认控制器
     * @param unknown_type $data
     * @author scan 232832288@qq.com
     */
    public function set_control_action($data = '')
    {
        if (empty($data)) {
            return ;
        }
        if (!preg_match('/^[0-9a-zA-Z_\.]+$/', $data)) {
            throw new Http500Exceptions('The server is busy, please try again later.');
        }
        if (strstr($data, '.')) {
            list($control, $action) = explode('.', $data);
            $this->set_control($control);
            $this->set_action($action);
        }else{
            $this->set_action($data);
        }

    }

}