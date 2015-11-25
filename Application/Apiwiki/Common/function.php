<?php

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function data_md5($str, $key = AUTH_SALT) {
    return !$str ? '' : md5(sha1($str) . $key);
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string
 */
function data_encrypt($data, $key = AUTH_SALT, $expire = 0) {
    $key  = md5($key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char =  '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x=0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
    }
    $str = base64_encode($str);
    $str = str_replace(array('+','/'),array('o000o','oo00o'),$str);
    return $str;
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串
 * @param string $key  加密密钥
 * @return string
 */
function data_decrypt($data, $key = AUTH_SALT) {
    $data = str_replace(array('o000o','oo00o'), array('+','/'),$data);
    $key    = md5($key);
    $x      = 0;
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);
    if($expire > 0 && $expire < time()) {
        return null;
    }
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 判断
 * @return bool
 */
function is_mobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array ('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}

/**
 * 构建数组树状结构。传入的array要求：parent_id对应的元素，必须再该元素之前载入到result中，否则该元素找不到一个元素作为父元素的元素被放置到children元素中（parent_id小于id）
 * @param $array
 * @param string $parent_key
 * @return array
 */
function array_tree($array,$parent_key = 'parent_id') {
    $result = array();
    $tmp = array();
    foreach($array as $item) {
        if($item[$parent_key] == 0) {
            $i = count($result);
            $result[$i] = $item;

            $id = $item['id'];
            $tmp[$id] = & $result[$i];
        }
        else {
            $id = $item['id'];
            $parent_id = $item[$parent_key];
            $parent = $tmp[$parent_id];
            $i = count($parent['children']);

            $tmp[$parent_id]['children'][$i] = $item;
            $tmp[$id] = & $tmp[$parent_id]['children'][$i];
        }
    }
    return $result;
}

/**
 * 统计字符串长度
 * @param $str
 * @return int
 */
function mb_str_length($str,$charset = 'utf-8') {
    if(empty($str)){
        return 0;
    }
    if(function_exists('mb_strlen')){
        return mb_strlen($str,$charset);
    }
    else {
        preg_match_all("/./u", $str, $ar);
        return count($ar[0]);
    }
}

/**
 * utf8截词
 * @param $str
 * @param int $start
 * @param bool|false $length
 * @return bool|string
 */
function mb_substr_utf8($str,$start = 0,$length = false) {
    if(empty($str)){
        return false;
    }
    if(function_exists('mb_substr')){
        if($length < mb_str_length($str)) {
            return mb_substr($str,$start,$length,'utf-8').'...';
        }
        else {
            mb_internal_encoding("UTF-8");
            return mb_substr($str,$start);
        }
    }
    else {
        preg_match_all("/./u", $str, $arr);
        if($length < count($arr[0])) {
            return implode('',array_slice($arr[0],$start,$length)).'...';
        }
        else {
            return implode('',array_slice($arr[0],$start));
        }
    }
}

/**
 * 可以传入字符编码的截词
 * @param $str
 * @param int $start
 * @param bool|false $length
 * @param string $charset
 * @param bool|true $suffix
 * @return string
 */
function mb_substr_charset($str, $start = 0, $length = false, $charset = "utf-8", $suffix = true) {
    if(function_exists("mb_substr")) {
        if($length < mb_str_length($str, $charset)) {
            return mb_substr($str,$start,$length,$charset);
        }
        else {
            mb_internal_encoding(strtoupper($charset));
            return mb_substr($str,$start);
        }
    }
    else {
        $re['utf-8']    = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312']   = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']      = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']     = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $arr);
        if($length < count($arr[0])) {
            $slice =  implode('',array_slice($arr[0],$start,$length));
        }
        else {
            $slice =  implode('',array_slice($arr[0],$start));
        }
    }
    if($suffix) return $slice."…";
    return $slice;
}