<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends  Controller
{
  public function __construct()
  {
    parent::__construct();
    _A();
  }
  // 检查openid是否存在
  public function checkOpenid() {

  }
}
