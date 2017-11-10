<?php
/*
 * DNSPod API PHP Web 示例
 * http://www.zhetenga.com/
 *
 * Copyright 2011, Kexian Li
 * Released under the MIT, BSD, and GPL Licenses.
 *
 */

class dnspod {

    private static $_instance;
    private $login_email = '';
    private $login_password = '';
    private $domain_id = '';

    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $this->login_email = Config::instance()->dnspod['login_email'];
        $this->login_password = Config::instance()->dnspod['login_password'];
        $this->domain_id = Config::instance()->dnspod['domain_id'];
    }

    public function quick_add($sub_domain, $value)
    {
        $allow_record_line = array('默认','联通','教育网','移动');
        $ips = explode(',', $value);
        foreach ($ips as $key => $ip) {
            $ip = trim($ip);
            if (!empty($ip)) {
                $this->modify_record($sub_domain, $ip, $allow_record_line[$key]);
            }
        }
    }

    public function get_record_id($sub_domain, $record_line)
    {
        $info = $this->api_call('Record.List', array('sub_domain' => $sub_domain));
        foreach ($info['records'] as $value) {
            if ($value['line'] == $record_line) {
                return $value['id'];
            }
        }
        return 0;
    }

    public function modify_record($sub_domain='', $value, $record_line='默认', $record_type='A', $ttl=600)
    {
        //$sub_domain = strtolower($sub_domain);

        if (!preg_match('/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/', $value)) {
            $record_type = 'CNAME';
        }
        $record_id = $this->get_record_id($sub_domain, $record_line);
        if (empty($record_id)) {
            return $this->create_record($sub_domain, $value, $record_line);
        }
        $info = $this->api_call('Record.Modify', array(
                'record_id' => $record_id,
                'sub_domain' => $sub_domain,
                'record_type' => $record_type,
                'value' => $value,
                'record_line' => $record_line,
                'ttl' => $ttl)
        );
    }


    public function create_record($sub_domain='', $value, $record_line='默认', $record_type='A', $ttl=600)
    {
        //$sub_domain = strtolower($sub_domain);

        if (!preg_match('/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/', $value)) {
            $record_type = 'CNAME';
        }
        $info = $this->api_call('Record.Create', array(
                                                    'sub_domain' => $sub_domain,
                                                    'record_type' => $record_type,
                                                    'value' => $value,
                                                    'record_line' => $record_line,
                                                    'ttl' => $ttl)
                    );
    }

	public function api_call($api, $data)
	{
		if ($api == '' || !is_array($data)) {
			exit('内部错误：参数错误');
		}

		$api = 'https://dnsapi.cn/' . $api;
		$data = array_merge($data,
		        array('login_email' => $this->login_email,
    		        'login_password' => $this->login_password,
		            'domain_id' => $this->domain_id,
    		        'format' => 'json',
    		        'lang' => 'cn',
    		        'error_on_empty' => 'no')
		        );

		$result = $this->post_data($api, $data);
		if (!$result) {
			throw new exception('内部错误：调用失败');
		}

		$results = json_decode($result, TRUE);
		if (!is_array($results)) {
			throw new exception('内部错误：返回错误');
		}

		if ($results['status']['code'] != 1) {
			throw new exception($results['status']['message']);
		}

		return $results;
	}

	private function post_data($url, $data)
	{
		if ($url == '' || !is_array($data)) {
			return false;
		}

		$ch = @curl_init();
		if (!$ch) {
			throw new exception('内部错误：服务器不支持CURL');
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_USERAGENT, 'DNSPod API PHP Web Client/0.1 (shallwedance@126.com)');
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}