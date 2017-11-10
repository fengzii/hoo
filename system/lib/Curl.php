<?php
set_time_limit(0);

class Curl
{

    private $_url;
    private $_method = 'GET';
    private $_postdata = '';
    private $_cookies = array();
    private $_referer;
    private $_accept = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
    private $_accept_encoding = 'gzip';
    private $_accept_language = 'en-us';
    private $_user_agent = 'HttpClient';
    private $_timeout = 20;
    private $_stream_timeout = 30;
    private $_gzip = FALSE;
    private $_max_reconnects = 5;
    private $_reconnect_count = 0;
    private $_max_redirects = 5;
    private $_redirect_count = 0;
    private $_username;
    private $_password;
    private $_results = FALSE;
    private $_error;
    private $_status = 0;
    private $_request_header = array();

    /**
     * 静态调用
     *
     * @author scan 232832288@qq.com
     */
    public static function instance()
    {
        return new self;
    }

    public function __construct()
    {
        ;
    }


    public function set($var)
    {
        foreach ( $var as $k => $v )
        {
            if ( isset( $this->$k ) )
            {
                $this->$k = $v;
            }
        }
        return $this;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }

    public function get($url)
    {
        $this->fetch($url);
        return $this->_results;
    }

    public function post($url, $data = '')
    {
        $this->_method = 'POST';
        $this->_url = $url;
        $this->_postdata = $data;
        $this->_request();
        return $this->_results;
    }

    public function fetch($url)
    {
        $this->_method = 'GET';
        $this->_url = $url;
        $this->_request();
    }

    private function _request()
    {
        $headers = array();
        $headers[] = 'Accept: '.$this->_accept;
        $headers[] = 'Connection: Keep-Alive';
        $s = curl_init();
        curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($s, CURLOPT_HEADER, 1);
        curl_setopt($s,CURLOPT_URL,$this->_url);
        curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout);
        //curl_setopt($s,CURLOPT_MAXREDIRS,$this->_max_redirects);
        curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
        //curl_setopt($s,CURLOPT_FOLLOWLOCATION,1);
        //curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
        //curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);
        //curl_setopt($s,CURLOPT_ENCODING , $this->_gzip);

        if(!empty($this->_username) && !empty($this->_password)){
            curl_setopt($s, CURLOPT_USERPWD, $this->_username.':'.$this->_password);
        }
        if ($this->_postdata) {
            curl_setopt($s,CURLOPT_POST,true);
            curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postdata);
        }
        curl_setopt($s,CURLOPT_USERAGENT,$this->_user_agent);
        if ($this->_referer) {
            curl_setopt($s,CURLOPT_REFERER,$this->_referer);
        }
        $response = curl_exec($s);
        curl_close($s);
        $this->_parse_response($response);

    }
    private function _parse_response($response)
    {

        if (empty($response)) {
            return FALSE;
        }
        $hunks = explode("\r\n\r\n", trim($response), 2);
        if (count($hunks) < 2) {
            return FALSE;
        }
        $headers = explode("\r\n", $hunks[0]);
        foreach($headers as $key => $header){
            $key = '';
            $val = '';
            if(  stripos($header, ':') !== FALSE  ){
                list($key, $val) = explode(':', $header, 2);
                $response_header[trim($key)] = trim($val);
            }
        }
        if (!empty($response_header['Location'])) {
            return $this->_redirect($response_header['Location']);
        }
        if (isset($response_header['Content-Encoding']) && $response_header['Content-Encoding'] == 'gzip') {
            $hunks[1] = substr($hunks[1], 10); // See http://www.php.net/manual/en/function.gzencode.php
            $hunks[1] = gzinflate($hunks[1]);
        }
        if(preg_match("'<meta[\s]*http-equiv[^>]*?content[\s]*=[\s]*[\"\']?\d+;[\s]*URL[\s]*=[\s]*([^\"\']*?)[\"\']?>'i",$hunks[1],$match))
		{
			return $this->_redirect($match[1]);
		}
        $this->_results = $hunks[1];
    }
    private function _redirect($url)
    {
        if (++$this->_redirect_count >= $this->_max_redirects) {
            $this->_error = 'Number of redirects exceeded maximum ('.$this->max_redirects.')';
            $this->redirect_count = 0;
            return FALSE;
        }
        return $this->fetch($url);
    }


}