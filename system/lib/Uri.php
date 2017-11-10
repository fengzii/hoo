<?php


class Uri
{
    private $uri;
    private $query = array();
    private static $config = array();

    function __construct()
    {
        $this->config = Config::load('uri');
        $this->fetch_uri();
    }

    public function fetch_uri()
    {
        if ( $this->config['uri'] == 'auto' )
        {

        }else if ( $this->config['uri'] == 'REQUEST_URI' )
        {
            $this->query =  Request::get_instance()->gets();
        }
    }


    public function UrlToPath( $uri,$ext = false )
    {

        $uri = str_replace( "?",'/',$uri );
        $uri = str_replace( "=",$this->config['_varexp'],$uri );
        $uri = str_replace( "&",$this->config['_paramexp'],$uri );

        if ( $ext )
        {
            $uri = str_replace($this->config['_ext'],'',$uri );
            return $uri;
        }
        return $uri.$this->config['_ext'];
    }

    public function UrlParse($uri='')
    {
        if ( empty( $uri ) )
        {
            $uri = $this->uri;
        }



        $uri = self::UrlToPath( $uri, true);




        $parsed_uri = explode("/", $uri);

        $query = array();

        foreach (  $parsed_uri as $key => $v )
        {
            if (strstr($v, "=")) {
                list($name, $value) = explode("=", $v, 2);
                $query[$name] = $value;
            }
        }

        $this->query = $query;

        return $query;

    }

    public function value($name)
    {
        if (isset($this->query[$name])) {
            return $this->query[$name];
        }
        return null;
    }

    public function act()
    {
        return $this->value('act');
    }
    function __destruct ()
    {
    }
}