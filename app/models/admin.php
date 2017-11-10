<?php

class admin extends Model{
	protected $_tableName = 'admin';
	protected $_PK = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function get($where)
    {
        return $this->select()->where($where)->fetch_one();
    }

    public function get_by_id($id)
    {
        return $this->select()->where(array('id'=>$id))->fetch_one();
    }

    public function get_by_name($name)
    {
        return $this->select()->where(array('username'=>$name))->fetch_one();
    }

    public function check_pass($pass, $info)
    {
        if ($info['password'] == md5($pass.App::PASSWORD_KEY.$info['key'])){
            return true;
        }
        return false;
    }

    public function save($data)
    {
        return $this->insert($data);
    }

    public function all()
    {
        return $this->select()->where(array('1'=>'1'))->fetch_array();
    }

    public function edit($data, $id, $field=false)
    {
        $fieldVal = !$field ? $this->_PK : $field;
        return $this->where(array($fieldVal => $id))->update($data);
    }

    public function del($id, $field=false)
    {
        $fieldVal = !$field ? $this->_PK : $field;
        return $this->where(array($fieldVal => $id))->delete();
    }


    public function lists($page, $size, $fields='*')
    {
        $offset = ($page-1) * $size;
        return $this->select($fields)->by($this->_PK, 'ASC')
            ->limit($size, $offset)->fetch_array();
    }

    public function lists_total($lock=0)
    {
        return $this->count()->fetch_one('count');
    }

    public function login($id)
    {
        return $this->edit(array('last_visit'=>time(), 'ip'=>getClientIP()), $id);
    }

    public function get_limit_join($value,$offset)
    {
        $table = array('`group`'=>"id=group_id");
        return $this->select("admin.id,username,email,group.name,last_visit,date_time,status")->join($table)->limit($value,$offset)->fetch_array();
    }

    public function get_by_id_join($id)
    {
        $table = array('`group`'=>"id=group_id");
        return $this->select("username,group.name,status,agent_id")->join($table)->where(array('admin.id'=>$id))->fetch_one();
    }

    public function upd($data,$where)
    {
        return $this->update($data)->where($where)->fetch_one();
    }

    public function get_num() {
        return $this->count()->fetch_one('count');
    }
}
