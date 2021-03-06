<?php

class Cookie{
	private static $_instance;

	public static function instance()
	{
	    if(self::$_instance == NULL){
	        self::$_instance = new self;
	    }
	    return self::$_instance;
	}

	public function __isset($name)
	{
		return isset($_COOKIE[$name]);
	}

	public function __get($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
	}

	public function __set($name, $value)
	{
	    $this->set($name, $value);
	}

	public function set($name, $value, $expire='')
	{
		$expire = empty($expire) ? time()+Config::instance()->cookie['expire'] : time()+$expire;
		setcookie($name, $value, $expire, Config::instance()->cookie['path'], Config::instance()->cookie['domain']);
	}

	public function __unset($name)
	{
		$this->set($name, '', '-3600');
		unset($_COOKIE[$name]);
	}
}
?>