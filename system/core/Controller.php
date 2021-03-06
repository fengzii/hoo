<?php
/**
 * 控制器
 *
 * @package controller
 */
abstract class Controller {

    public $view;
    //public $db;
    public $params;
    public $tpl = '';
    private $_prefix_action = '';

    /**
     * 构造
     *
     */
    public final function __construct()
    {
        //初始化
        $this->_init();
        $this->_before();
    }

    /**
     * 析构
     *
     * @author scan 232832288@qq.com
     */
    public final function __destruct()
    {
        $this->_after();
    }


    /**
     * 初始化
     *
     */
    public function _init()
    {
        //自动载入默认类
        //Application::autoloader();
        //载载视图
        $this->view   = View::instance();
        //$this->db     = Model::instance();
        $this->params = Request::instance()->params();
        //加载自定义函数
        if (is_file( APP_PATH.'function/function.php' )){
            include( APP_PATH.'function/function.php'  );
        }
    }

    /**
     * 执行动作
     *
     */
    public function exec()
    {
        $action = $this->action(ACTION);
        if (!method_exists($this, $action)) {
            //throwException("can't find action '{$method}' in controller '{$controller}'");
            throw new HttpExceptions('can\'t find action '.ACTION.' in controller '.CONTROL, 404);
        }
        empty($this->tpl) && $this->tpl = CONTROL.DIRECTORY_SEPARATOR.ACTION;

        $ret = $this->$action();
        $GLOBALS['_endTime'] = microtime(TRUE);
        $this->view->put($ret);
        $this->view->put(get_object_vars($this));

        //if (empty($this->tpl)) $this->tpl = CONTROL.DIRECTORY_SEPARATOR.ACTION;
        $this->view->display($this->tpl);
    }

    /**
     * 取得动作
     * @param string $action
     * @author scan 232832288@qq.com
     */
    protected  function action($action)
    {
        return $this->_prefix_action . $action;
    }

    /**
     * 魔术方法
     *
     * @author scan 232832288@qq.com
     */
    public function __get($name)
    {
        return Base::instance()->model($name);
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function _before()
    {
        if (method_exists($this, 'before')){
            $this->before();
        }
    }

    public function _after()
    {
        if (method_exists($this, 'after')){
            $this->after();
        }
    }


}