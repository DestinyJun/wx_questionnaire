<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Verify;

class LoginController extends Controller
{
  // 登陆验证
  public function index() {
    if (IS_GET) {
      $this->display();
    } else {
      $code_num = I('post.captcha');
      if (!$code_num) {
        $this->error('请输入验证码');
      }
      $code = new Verify();
      if (!$code->check($code_num)){
        $this->error('验证码错误！');
      }
      $adminuser = I('post.adminuser');
      $adminpass = I('post.adminpass');
      $adminModel = D('Admin');
      $res = $adminModel->login($adminuser,$adminpass);
      if (!$res){
        $this->error($adminModel->getError());
      }
      $this->success('登陆成功！',U('Index/index'));
    }
  }

  // 生成验证码
  public function verify() {
    $config = array(
      'length' => 4,
      'imageH' => 34,
      'imageW' => 130,
      'fontSize' => 18
    );
    $code = new verify($config);
    $code->entry();
  }

  // 退出
  public function logout() {
    session('admin', null);
    $this->redirect('/');
  }
}
