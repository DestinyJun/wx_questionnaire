<?php
namespace Admin\Model;
class AdminModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id','adminuser','adminpass','addtime');

  // 字段校验
  protected $_validate = array(
    array('adminuser','require','用户名是必填项'),
    array('adminpass','require','密码是必填项'),
  );

  // 自动完成
  protected  $_auto = array(
    array('adminpass','md5',3,'function'),
    array('addtime','time',1,'function')
  );
}
