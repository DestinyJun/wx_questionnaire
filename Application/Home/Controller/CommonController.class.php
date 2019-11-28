<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends  Controller
{
  public function __construct()
  {
    parent::__construct();
    // 跨域处理
    _A();
//   $this->getClientIp();
  }
  // 检查openid是否存在
  public function checkOpenid() {

  }

  // 限定IP访问
  public function getClientIp() {
    // 获取客户端的IP地址
    $ip = get_client_ip();
    // 限制允访问的IP
    $allow = array('127.0.0.1','localhost');
    // 根据请求的IP地址检查是否允许访问
    if (!in_array($ip,$allow)) {
      $this->ajaxReturn(array('status'=>1001,'msg'=>'没有访问权限'));
    }
  }
}
