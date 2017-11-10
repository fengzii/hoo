<?php

/**
 * 后台管理
 * Enter description here ...
 * @author hokit <232832288@qq.com>
 * @property m_menu $m_menu
 * @property priv $priv
 * @property group $group
 * @property admin $admin
 * @property menu $menu
 * @property column $column
 * @property game_manu $game_manu
 * @property game $game
 * @property server_safe $server_safe
 * @property server $server
 * @property news $news
 * @property sub_column $sub_column
 * @property card_cate $card_cate
 * @property card $card
 * @property friendly_link $friendly_link
 * @property category $category
 * @property slide $slide
 * @property picture $picture
 * @property payment $payment
 * @property material $material
 * @property ads $ads
 * @property ads_log $ads_log
 * @property game_log $game_log
 * @property material_log $material_log
 * @property bill $bill
 * @property ads_log_day $ads_log_day  
 * @property login_day_log $login_day_log
 * @property ads_link $ads_link
 * @property user_whole $user_whole
 * @property static_game $static_game
 * @property static_table $static_table
 * @property comm $comm
 */
class App extends Controller {

    const META_CSS = "<link rel=\"stylesheet\" href=\"css/%s.css\" type=\"text/css\" />\n";
    const META_SCRIPT = "<script language=\"javascript\" src=\"js/%s.js\"></script>\n";
    const META_PLUGINS = "<script language=\"javascript\" src=\"plugins/%s.js\"></script>\n";
    const META_JS = '
<script language="javascript" type="text/javascript">
		%s
</script>
    ';
    const META_STYLE = "<link rel=\"stylesheet\" href=\"%s.css\" type=\"text/css\" />\n";
    const PASSWORD_KEY = 'T$%gr43';

    protected $admin_info;
    static $UNLIMIT = array(
        'login.no_login' => '未登录',
        'login.do_login' => '登录中',
        'login.checkpicone' => '校验码',
        'login.nologin' => '未登录',
    );
    static $UNRANK = array(
        'login.no_login' => '未登录',
        'login.do_login' => '登录中',
        'login.nologin' => '未登录',
        'index.index' => '主页'
    );
    static $GAME_CATEGORY = array(
        '1' => '角色扮演',
        '2' => '战争策略',
        '3' => '模拟经营',
        '4' => '其他类型',
    );
    public static $STATUS = array(
        0 => '正常',
        1 => '限制登录',
    );

    public function _before() {
        $this->_render();
        $this->_acl();
    }

    protected function _render() {
        $this->css = '';
        $this->script = '';

        $this->act = CONTROL . '.' . ACTION;
        $this->msg = array();
        $this->msg['title'] = '信息提示';
        $this->msg['body'] = '';
        $this->msg['delay'] = 3;
        $this->msg['type'] = 0;
        $this->msg['redirect_url'] = 'index.index';
    }

    protected function _acl() {
        if (!$this->_check_login()) {
            $this->_no_login();
        }

        $this->admin_info = array(
            'name' => Session::instance()->admin['username'],
            'id' => Session::instance()->admin['id'],
            'group_name' => Session::instance()->admin['group_name'],
            'full_name' => Session::instance()->admin['email'],
            'menu_id' => Session::instance()->admin['menu_id'],
            'priv' => Session::instance()->admin['priv'],
            'agent_id' => Session::instance()->admin['agent_id']
        );

        $this->_siderbar();
        $this->_server_list();
        if (!$this->_check_rank()) {
            $this->_no_rank();
        }
    }

    protected function _check_login() {
        if (in_array(CONTROL . '.' . ACTION, array_flip(self::$UNLIMIT))) {
            return TRUE;
        }
        if (!Auth::instance()->set(array('session_name' => 'admin', 'login_url' => '?act=login.nologin'))->checkLogin()) {
            return FALSE;
        }
        return TRUE;
    }

    protected function _check_rank() {
        if (in_array(CONTROL . '.' . ACTION, array_flip(self::$UNRANK))) {
            return TRUE;
        }

        $rs = $this->privilege->get_by_key(CONTROL, ACTION);
      
       
        if (empty($rs)) { 
            return true;
        }
        $priv = Session::instance()->admin['priv'];
      
        if (!in_array($rs['id'], $priv)) {
            return false;
        }
        return TRUE;
    }

    public function _no_login() {
        Session::instance()->destroy();
        echo "<script language=javascript>location.href='?act=login.nologin';</script>";
        exit;
    }

    public function _no_rank() {
        $this->view->put(get_object_vars($this));
        $this->view->display('index.norank');
        exit;
    }
    
    public function _no_allow() {
        $this->view->put(get_object_vars($this));
        $this->view->display('index.norank');
        exit;
    }    

    protected function _siderbar() {
        if (empty($this->admin_info['menu_id'])) {
            return ;
        }
        $parent = $this->m_menu->get_by_pid(0);
        $parent = pk($parent, 'id');
        $data = array();

        foreach ($parent as $k => $p) {
            if (!in_array($p['id'], $this->admin_info['menu_id']))
                continue;
            $p['parent_name'] = '★☆顶层菜单';
            $data[$k] = $p;
        }

        $child = $this->m_menu->get_by_child();
        foreach ($child as $c) {
            if (!in_array($c['id'], $this->admin_info['menu_id']))
                continue;
            $c['parent_name'] = $parent[$c['parent_id']]['name'];
            $data[$c['parent_id']]['child'][] = $c;
        }
        $this->sidebar = $data;
    }

    protected function _server_list() {
        $this->_servers = array();
        if (!empty($this->admin_info ['agent_id'])) {
            $agent = explode(',', $this->admin_info ['agent_id']);
           
            foreach($agent as $v){
                #获取agent信息
                $agent = $this->agent->get_name_by_id($v);
                if(empty($agent)){
                    continue;
                }
                //获取对应服务器列表
               $this->_servers[ $agent] = $this->server->all(array('agent_id'=>$v));
            }
        }
       
    }

    protected function trim(&$data) {
        if (!empty($data) && is_array($data)) {
            foreach ($data as $v) {
                $this->trim($v);
            }
        } elseif (!empty($data)) {
            trim($data);
        }
    }

    protected function _special_id() {
        return sprintf('%u', crc32(date('Ymd') . sprintf('%06d', hexdec(substr(uniqid(), 8, 13))) . sprintf('%01d', rand(0, 9))));
    }

    /**
     * 提示页输出
     * $log 是否写入日志
     * $log 日志相关数据
     * @param unknown_type $msg
     * @param unknown_type $status
     * @param unknown_type $log
     * 
     */
    protected function _goto($msg, $status = 0, $log_data = true) {
        if (is_array($msg)) {
            $msg = implode('<br/>', $msg);
        }
        $this->msg['body'] = $msg;
        $typeArr = array(
            '0' => 'n_ok',
            '1' => 'n_info',
            '2' => 'n_error',
            '3' => 'n_warning',
        );

        !isset($typeArr[$status]) && $status = 0;
        //目前只存储操作成功的日志
        if ($status == 0 && !empty($log_data)){
        	$this->ll($status, $msg ,$log_data);
        }
        $this->msg['type'] = $typeArr[$status];

        $this->view->put(get_object_vars($this));

        $this->view->display('message');

        exit();
    }

    public function ss($fun, $args, $socket = true) { // socket send

         //默认发送agent_id
        $agent_id = Session::instance()->server['agent_id'];
        $args = array_merge(array('agent_id' => $agent_id), $args);
        if(empty(Session::instance()->server)){
             $this->_goto('服务器不存在，请选择一个服务器来管理', 2);
        }
        /*
        if($_SERVER['SERVER_NAME']=='www.7837.net'){
            Session::instance()->server = array('domain'=>'127.0.0.1:8080');
        }else{
              Session::instance()->server = array('domain'=>'192.168.1.10:8080');
        }
       */
        // Session::instance()->server['domain'] = '192.168.1.10:8080';
        // Session::instance()->server = array('soap' => 'http://127.0.0.1/lw/');
		$socket = true;
        if ($socket){
            if (empty(Session::instance()->server['domain'])){
                return $this->_no_allow();
            }            
            $rtn = $this->comm->get(Session::instance()->server['domain'], $fun, $args);
        } else {
            if (empty(Session::instance()->server['soap'])){
                return $this->_no_allow();
            }            
            $rtn = $this->comm->soap(Session::instance()->server['soap'], $fun, $args);
        }
        // get(Session::instance()->server['domain'], $fun, $args);
//var_dump($rtn);
//exit();
        if ($rtn['status'] != 0) {
            /**
             * @todo 错误码 提示
             */
            return $this->_no_allow();
        }
        return $rtn['content'];
    }
    
    /**
     * 添加管理日志
     * $data = array('users', 'comment', 'debug_data', 'item_info')
     */
    public function ll($type, $msg, $data = false){
    	$log = array();
    	$param = $this->params;
    	$log['action'] = $param['act'];
    	unset($param['act']);
    	$log['param'] = FastJSON::convert($param, false);
    	$log['manager'] = Session::instance()->admin['username'];
    	$log['type'] = $type;
    	if (isset($data['users'])){
    		if(is_array($data['users'])){
    			$log['users'] = implode(',', $data['users']);
    		} else {
    			$log['users'] = $data['users'];
    		}
    	}
    	if (isset($data['comment'])){
    		$log['comment'] = $data['comment'];
    	} else {
    		$log['comment'] = $msg;
    	}
    	$insertid = $this->manage_log->save($log);
    	if (isset($data['item'])){
    		$this->award_log->save_list(array(
    			'manager' => $log['manager'],
    			'manage_id' => $insertid,
    			'itemlist'  => $data['item'],
    			'users'	=> $data['users'],
    		));
    	}
    }

    protected function check_servers() {
        
    }

    protected function get_serverlist() {
        
    }

    protected function get_servers() {
        
    }

    protected function get_my_servers() {
        
    }

    /*
      public function checkservers()
      {

      if ( empty( $this->cout['temp_server'] ) )
      {
      location("","请先选择服务器!");
      }
      //print_r($this->cout['temp_server']);exit;
      if ( !array_key_exists( $this->cout['temp_server'],$this->cout['game_servers'] ) )
      {
      location("","所选择服务器不存在!");
      }
      $my_game = explode( ',',  $_SESSION['servers']);
      //print_r($this->cout['server_list']);exit;

      if ( !in_array( $this->cout['temp_server'],$my_game ) )
      {
      location("","无此权限!");
      }
      return $this->cout['server_list'][$this->cout['temp_server']]['url'];
      }
     */

    protected function _dump($var) {
        var_dump($var);
        exit();
    }

}
