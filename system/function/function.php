<?php

/**
 * 内部调用的函数集
 */



/**
 * 自动加载类
 *
 * @param string $class 类
 * @since 1.0
 */
function __autoload($class)
{
    static $objects = array();

    if (isset($objects[$class])){
        return $objects[$class];
    }
    $class_file = '';
    foreach (array(LIB_PATH, APP_LIB_PATH) as $path) {
        $class_file = $path.$class.'.php';
        if (file_exists($class_file)) {
            break;
        }
    }
    if ( empty($class_file))
    {
        throw new HttpExceptions('not fount lib: '.$class);
    }
    require( $class_file);
    $objects[$class] =  $class;
    return TRUE;

}

/**
 * 导入类
 *
 * @param string $class 类 支持 , . *
 * @param string $subdir 是否导入子目录 默认false
 */
function import($class, $subdir = FALSE, $is_dir = FALSE)
{
    static $objects = array();

    if (isset($objects[$class])){
        return TRUE;
    }
    if (class_exists($class, FALSE)){
        return TRUE;
    }
    if (strstr($class,',')){
        $classes = explode( ',', $class );
        foreach ( $classes as $v )
        {
            import($v);
        }
    }
    if (is_file($class)){
        require($class);
        $objects[$class] = TRUE;
        return TRUE;
    }
    if (strstr($class,'.')){
        $class = str_replace( '.', '/', $class);
    }
    $search_dir = array(LIB_PATH, PLU_PATH, MOD_PATH, APP_PATH.'lib/', APP_PATH.'module/');
    //已经是路径
    foreach ($search_dir as $dir)
    {
        $ClassName = $dir.$class.'.php';
        if ( is_file( $ClassName ) ){
            require( $ClassName );
            $objects[$class] = TRUE;
            return TRUE;
        }
    }
    //*载入
    if (strstr(SYS_PATH.$class, '*')){
        $array = Fso::fileList(SYS_PATH.str_replace('*', '', $class ), TRUE, '*.php');
        foreach ($array as $path) {
            if (is_file($path)) {
                import($path);
            }
        }

    }
    return TRUE;
}

/**
 * 名称 取得系统运行地址
 * @return string
 * @since 1.0
 */
function get_base()
{
    if ( preg_match("/^\/".APP_NAME."/", $_SERVER['SCRIPT_NAME'], $match) )
    {
        return str_replace('index.php', "", $_SERVER['SCRIPT_NAME']);
    }else
    {
        return str_replace('index.php', "", $_SERVER['SCRIPT_NAME']).APP_NAME.'/';
    }
}

/**
 * 自定义异常处理
 *
 * @param string $msg 错误信息
 * @param string $type 异常类型 默认为Exceptions
 * 如果指定的异常类不存在，则直接输出错误信息
 * @return void
 * @version 1.0
 */

function throwException($msg, $type = 'Exceptions', $code=0)
{
    if(class_exists($type,FALSE)){
        throw new $type($msg, $code, TRUE);
    }else {
        // 异常类型不存在则输出错误信息字串
        exit($msg);
    }
}




/**
 * get
 * @param unknown_type $key
 * @author scan 232832288@qq.com
 */
function G($key)
{
    return Request::instance()->get($key);
}

function P($key)
{
    return Request::instance()->post($key);
}

/**
 * 导入 model 方法
 *
 * @example m()->static_table
 * @return base|object
 */
function M()
{
    return Base::instance();
}

/**
 * 按 $pk 组成新的数组
 * @param array $arr
 * @param string $pk
 * @return array
 */
function pk($arr, $pk, $key=FALSE)
{
    if ( empty($arr) ){
        return array();
    }
    $newarr = array();
    if (!$key){
        foreach( $arr as $k=>$v){
            $newarr[$v[$pk]] = $v;
        }
    } else {
        foreach($arr as $k=>$v){
            $newarr[$v[$pk]] = $v[$key];
        }
    }
    unset($arr);
    return $newarr;
}

/**
 * 按 $pk 组成新的数组
 * @param array $arr
 * @param string $key
 * @return array
 */
function make_arr_key($arr, $key)
{
    if ( empty($arr) || empty($key) ){
        return array();
    }
    $rtn = array();
    foreach ($arr as $val){
        $rtn[] = $val[$key];
    }
    return $rtn;
}


function html_option($data, $selected=FALSE, $key=FALSE, $val=FALSE){
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

function html_radio($data, $name, $selected, $key=FALSE, $val=FALSE, $fix='<br />'){
    if (!$key && !$val){
        foreach ($data as $k =>$d){
            $s = '';
            if ($k == $selected) $s = 'checked="checked"';
            $rtn.= '<label><input type="radio" name="'.$name.'[]" value="'.$k.'" '.$s.' />'.$d.'</label>'.$fix;
        }
    } else {
        foreach ($data as $d){
            $s = '';
            if ($d[$key] == $selected) $s = 'checked="checked"';
            $rtn.= '<label><input type="radio" name="'.$name.'[]" value="'.$d[$key].'" '.$s.' />'.$d[$val].'</label>'.$fix;
        }    
    }
    return $rtn;
}


