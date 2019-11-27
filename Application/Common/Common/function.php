<?php
function _A(){
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Headers:Accept,Referer,Host,Keep-Alive,User-Agent,X-Requested-With,Cache-Control,Content-Type,Cookie,token');
  header('Access-Control-Allow-Credentials:true');
  header('Access-Control-Allow-Methods:GET,POST,OPTIONS');
  header("Content-type: text/json; charset=utf-8");
}

/**
 * @param string $param
 * @return mixed
 */
function _I($param=''){
  $data = json_decode(file_get_contents('php://input'), true);
  return $data[$param];
}
