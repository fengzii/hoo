<?php
set_time_limit(0);

class Http
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
        $url = parse_url($this->_url);
        $host = isset($url['host']) ? $url['host'] : '';
        $port = isset($url['port']) ? $url['port'] : 80;
        $path = (isset($url['path']) ? $url['path'] : '/') . (isset($url['query']) ? '?' . $url['query'] : '');
        $scheme = '';
        if ($url['scheme'] == 'https') {
            $scheme = 'ssl://';
            $port = 443;
        }
        $fp = @fsockopen($scheme.$host, $port, $errno, $errstr, $this->_timeout);
        if (!$fp) {
            do {
                if (++$this->_reconnect_count >= $this->_max_reconnects) {
                    $this->_reconnect_count = 0;
                    $this->_error = 'Could not open connection. Error '.$errno.':'.$errstr."\n";
                    return FALSE;
                }
                //echo 'why ? sleep'."\n";
                usleep(100);
                $fp = @fsockopen($scheme.$host, $port, $errno, $errstr, $this->_timeout);

            } while(!$fp);

        }
        $headers = array();
        $headers[] = $this->_method.' '.$path.' HTTP/1.0';
        $headers[] = 'Host: '.$host;
        $headers[] = 'User-Agent: '.$this->_user_agent;
        $headers[] = 'Accept: '.$this->_accept;
        if ($this->_gzip) {
            $headers[] = 'Accept-encoding: '.$this->_accept_encoding;
        }
        $headers[] = 'Accept-language: '.$this->_accept_language;
        if ($this->_referer) {
            $headers[] = 'Referer: '.$this->_referer;
        }
        if (!empty($this->_cookies)) {
            $cookie_headers = array();
            foreach ($array_expression as $key => $value) {
                $cookie_headers[] = $key.'='.urlencode($value).'; ';
            }
            $headers[] = implode('', $cookie_headers);
        }
        if (!empty($this->_username) && !empty($this->_password)) {
    	    $headers[] = 'Authorization: BASIC '.base64_encode($this->_username.':'.$this->_password);
    	}
        if ($this->_postdata) {
    	    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    	    $headers[] = 'Content-Length: '.strlen($this->_postdata);
    	}
    	$headers[] = 'Connection: Close';
    	$request = implode("\r\n", $headers)."\r\n\r\n".$this->_postdata;

        stream_set_timeout($fp, $this->_stream_timeout);
        //fwrite($fp, $request);
        if (fwrite($fp, $request, strlen($request)) === FALSE) {
            fclose($fp);
            $this->_error = "Error writing request type to socket\n";
            return FALSE;
        }
        $response = '';
        while (!feof($fp)) {
            $info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				$this->_error = "Connection Timed Out!\n";
				return FALSE;
			}
            $response .= fgets($fp, 1024);
        }
        fclose($fp);
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