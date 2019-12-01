<?php
/**
 * 跨域处理
 */
function _A(){
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,token');
  header('Access-Control-Allow-Credentials:true');
  header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
  header("Content-type: text/json; charset=utf-8");
}

/**
 * 请求方式为content-type时收收参数
 * @param string $param
 * @return mixed
 */
function _I($param=''){
  $data = json_decode(file_get_contents('php://input'), true);
  return $data[$param];
}

/**
 * 封装curl请求
 * @param string $url 请求的接口地址
 * @param array $data 请求的接口参数
 * @param string $method 请求方式
 * @return mixed
 */
function http_curl($url='',$data=array(),$method='get',$https=false) {
  if (!function_exists('curl_init')) {
    echo 'curl扩展没有开启！'; exit();
  }
  /* 使用curl的相关步骤 */
  // 1、打开会话（可以理解为MySQL建立连接，初始化curl会话）
  $ch = curl_init();
  // 检查是否是https请求
  if ($https) {
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
  }
  // 3、设置参数信息，需要指定具体的请求地址、参数以及具体的请求方式
  if ($method == 'post') {
    // post请求
    curl_setopt($ch,CURLOPT_POST,true); // 默认请求方式时get,这里设置为post
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data); // 设置具体的请求参数及请求方式，如请求头等
  } else {
    // get请求拼接请求url
    $url .= '&'.http_build_query($data);
  }
  curl_setopt($ch,CURLOPT_URL,$url); // 设置请求地址
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,$url); // 设置获取到的信息以文件流的形式返回，而不是直接输出
  // 4、执行具体的请求操作
  $res = curl_exec($ch);

  // 5、关闭会话
  curl_close($ch);
  // 6、将具体请求到的数据转化为PHP的数组格式并返回
  return json_decode($res,true);
}
