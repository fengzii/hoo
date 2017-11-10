<?php

function fun_option($data, $selected=FALSE, $key=FALSE, $val=FALSE){
    $rtn = '';
    $cond = $selected===FALSE ? FALSE : TRUE;
    if (!$key && !$val){
        foreach ($data as $k =>$d){
            if (!$cond){ 
                $rtn.= '<option value="'.$k.'" label="'.$d.'">'.$d.'</option>';
            } else {
                $s = '';                
                if ($selected == $k) $s = 'selected="selected"';             
//var_dump($k , $selected, $s);                            
                $rtn.= '<option value="'.$k.'" label="'.$d.'"  '.$s.' >'.$d.'</option>';
            }      
        }
    } else {
        foreach ($data as $d){
            if (!$cond){ 
                $rtn.= '<option value="'.$d[$key].'" label="'.$d[$val].'">'.$d[$val].'</option>';
            } else {
                $s = '';
                if ($selected == $d[$key]) $s = 'selected="selected"';
                $rtn.= '<option value="'.$d[$key].'" label="'.$d[$val].'" '.$s.'>'.$d[$val].'</option>';
            }
                     
        }    
    }
    return $rtn;
}


function fun_re_option($data, $selected, $key, $val, $disable=FALSE, $i=0){
    $i++;
    foreach ($data as $d){
        $s = '';
        $s2 = '';
        if ($selected == $d[$key]) $s = 'selected="selected"';
        if ($disable && $disable == $d[$key] ) $s2 = 'disabled="disabled"'; 
        echo  '<option value="'.$d[$key].'" '.$s.' '.$s2.' >';
        if ($i>1){
            echo str_repeat("&nbsp;&nbsp;", $i-1).'┡ ';
        }
        echo $d[$val].'</option>';
        if (!empty($d['childs'])) fun_re_option($d['childs'], $selected, $key, $val, $disable, $i);
    }    
}


/**
 * 去的客户端ip
 *
 * @param bool $long
 * @return string
 */
function getClientIP($long=FALSE)
{
	if (isset($_SERVER))
	{
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR"))
		{
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		} else if (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
    }
    if ($long) {
        return ip2long($realip);
    }
    return addslashes($realip);
}

/**
 * 二维数组转成一维数组
 *
 * @param array $array
 * @return array
 */

function array_multi2single($array)
{
    static $result_array=array();
    foreach($array as $value)
    {
        if(is_array($value))
        {
            array_multi2single($value);
        }
        else  
            $result_array[]=$value;
    }
    return $result_array;
}

/**
 * 两个数组交集
 *
 * @param array $array， array $array
 * @return array
 */
function array_mix($arr1,$arr2)
{
    foreach($arr1 as $key=>$val)
    {
        if(array_key_exists($key,$arr2))
        {
            foreach($val as $k => $v)
            {
                if(in_array($k,$arr2[$key]))
                {
                    $arr1[$key][$k] = 1;
                }
            }
        }
    }
    return $arr1;
}


function create_token($client_data)
{
	if ( empty($client_data) || !is_array($client_data)){
		return false;
	}
	if ( !defined('GAME_GLOBAL_KEY') ){
		return false;
	}
	ksort($client_data);
	$str = implode('-',$client_data);
	return md5($str.GAME_GLOBAL_KEY); // 'dtf8yQ4kt3pnSNrfATpzb6BQ5JTjNaEY'
}

function check_token($client_data = array(), $client_token)
{
	$client_legitimate = false;

	$get_token = array();

	if (empty($client_data) || !is_array($client_data))
	{
		return false;
	}
	if (!defined('GAME_GLOBAL_KEY')) {
		return false;
	}
    if ( $client_token != create_token($client_data) ){
		return false;
    }else{
		return true;
	}
}

