<?php
//phpinfo();
//$num = 10;
//$num=str_pad($num,2,"0",STR_PAD_LEFT);
//echo $num;

function getApi($url, $data=[],  $cookie_file='', $timeout = 100){
    if(empty($cookie_file)) {
        $cookie_file = '.cookie';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if(!empty($data)) {
        $header=array(
            "Content-Type: application/json",
            "User-Agent  : finbtc/1.7.0(iphone;IOS 11.3.1;Scale/2.00)",
            "token       : dab5d92ca5804bcfbff6107526f11add",
            "X-App-Info  : 1.7.0/appstore/ios",
        );

        curl_setopt($ch, CURLOPT_HEADER,$header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch,  CURLOPT_COOKIEJAR, $cookie_file);// 取cookie的参数是
    curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie_file); //发送cookie
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    try {
        $handles = json_decode(curl_exec($ch),true);

        curl_close($ch);
        return $handles;
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    unlink($cookie_file);
}
//$re = getApi("http://www.xuncen.com:18080/get_currency_list/price_scope/desc/1/8/");
//$re = getApi("https://api.finbtc.net/app/market/coin/toplist");
//var_dump($re);


function response($text){
    $headers = explode(PHP_EOL, $text);
    $items = array();
    foreach($headers as $header) {
        $header = trim($header);
        if(strpos($header, '{') !== False){
            $items = json_decode($header, 1);
            break;
        }
    }
    return $items;
}


function request($path, $method, $headers = null, $body = null,$timeout = 10){
    $ch  = curl_init($path);

    $_headers = array('Expect:');
    if (!is_null($headers) && is_array($headers)){
        foreach($headers as $k => $v) {
            array_push($_headers, "{$k}: {$v}");
        }
    }

    $length = 0;
    $date   = gmdate('D, d M Y H:i:s \G\M\T');

    if (!is_null($body)) {
        if(is_resource($body)){
            fseek($body, 0, SEEK_END);
            $length = ftell($body);
            fseek($body, 0);

            array_push($_headers, "Content-Length: {$length}");
            curl_setopt($ch, CURLOPT_INFILE, $body);
            curl_setopt($ch, CURLOPT_INFILESIZE, $length);
        } else {
            $length = @strlen($body);
            array_push($_headers, "Content-Length: {$length}");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
    } else {
        array_push($_headers, "Content-Length: {$length}");
    }

    // array_push($_headers, 'Authorization: ' . $this->sign($method, $uri, $date, $length));
    array_push($_headers, "Date: {$date}");

    curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($method == 'PUT' || $method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
    } else {
        curl_setopt($ch, CURLOPT_POST, 0);
    }

    if ($method == 'HEAD') {
        curl_setopt($ch, CURLOPT_NOBODY, true);
    }

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    list($header, $body) = explode("\r\n\r\n", $response, 2);
    if ($status == 200) {
        if ($method == 'GET') {
            return $body;
        } else {
            return response($response);
        }
    } else {
        $arrerror = [$headers,$body];
        return $arrerror;
    }
}
$headers = [ "Content-Type: application/json","X-App-Info:2.1.0/appstore/ios","User-Agent:finbtc/1.7.0(ipone;iOS 11.3.1;Scale/2.00)","token:dab5d92ca5804bcfbff6107526f11add"];
$re = request("https://api.finbtc.net/app/market/coin/toplist",'post',$headers);
//$re = request("http://www.xuncen.com:18080/get_currency_list/price_scope/desc/1/8/","get",$headers);
echo "<pre>";
var_dump($re);
echo "</pre>";
//调用别的网站用的还是自己的域名
/*$opts = array('http'=>array('method'=>"POST", 'timeout'=>10,));
$context = stream_context_create($opts);

$html =file_get_contents('http://www.hao123.com', false, $context);
echo $html;*/