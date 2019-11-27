<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends  Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->checkLogin();
  }

  // 检查用户登陆状态
  public function checkLogin() {
    $admin = session('admin');
    if (!$admin) {
      $this->error('您还未登陆，请登陆后再进入本系统！',U('Login/index'));
    }
  }
}
