<?php
namespace Admin\Model;
class AdminModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id','adminuser','adminpass','addtime');

  // 字段校验
  protected $_validate = array(
    array('adminuser','require','用户名是必填项'),
    array('adminuser','','帐号名称已经存在！',1,'unique',3),
    array('adminpass','require','密码是必填项'),
  );

  // 自动完成
  protected  $_auto = array(
    array('adminpass','md5',3,'function'),
    array('addtime','time',1,'function')
  );

  // 登陆验证
  public function login($adminuser,$adminpass) {
    $info = $this->where("adminuser='{$adminuser}'")->find();
    if (!$info){
      $this->error = '用户名或密码错误';
      return false;
    }
    if ($info['adminpass'] != md5($adminpass)) {
      $this->error='用户名或密码不正确';
      return false;
    }
    // 保存用户登陆状态
    session('admin',$info);
    return true;
  }
}
