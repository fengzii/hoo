<?php

class Queue
{
    private static $_instance;
    public static function instance()
    {
        if (self::$_instance == null) {
            $driver = Config::instance()->queue['driver'];
            self::$_instance = new $driver;
        }
        return self::$_instance;
    }
    public function __construct()
    {
        $this->_init();
    }

    public function __destruct()
    {
    }

    public function __get($key)
    {
        return $this->_get($key);
    }

    public function __set($key, $data)
    {
        return $this->_set($key, $data);
    }

    public function __unset($key)
    {
        return $this->_del($key);
    }

    public function encode($data)
    {
        return json_encode($data);
    }

    public function decode($data)
    {
        return json_decode($data, true);
    }
}



class cache_queue extends Cache
{
    private $_queue_data = array();

    protected function _init()
    {
        $queue = Cache::instance()->$key;
        if (empty($queue)) {
            return FALSE;
        }
        $this->_queue_data = $queue;
    }

    protected function _get($key)
    {
        if (empty($this->_queue_data)) {
            return FALSE;
        }
        return array_shift($this->_queue_data);
    }

    protected function _set($key, $data)
    {
        $this->_queue_data[] = $data;
        return Cache::instance()->$key = $this->_queue_data;
    }


}


class redis_queue extends Cache
{
    private $_redis = null;


    protected function _init()
    {
        if ( $this->_redis == null )
        {
            $this->_redis = new Redis();
            list($server,$port) = explode(':', Config::instance()->queue['server']);
            if ( !$this->_redis->pconnect($server, $port, Config::instance()->queue['timeout']) )
            {
                throw new Http503Exceptions('Can\'t connect to cache Redis server ');
            }
        }
        return true;

    }

    protected function _get($key)
    {
        $data = $this->_redis->rpop($key);
        if ($data == FALSE) {
            return FALSE;
        }
        return $this->decode( $data );
    }

    protected function _set($key, $data)
    {
        $data = $this->encode($data);
        return $this->_redis->lpush($key, $data);
    }

}