<?php
namespace Admin\Model;
use Think\Page;

class UserModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id','openid','nikename','sex','tel','addtime');

  // 自动完成
  protected $_auto = array(
    array('addtime','time',3,'function')
  );

  // 字段校验
  protected $_validate = array(
    array('openid','','openid已经存在',0,'unique',3),
    array('nikename','require','昵称是必填项'),
    array('tel','require','电话是必填项'),
  );

  // 分页查询
  public function selectAll() {
    $count = $this->count();
    $pagesize = 10;
    $page = new Page($count,$pagesize);
    $page->setConfig('prev','上一页');
    $page->setConfig('next','下一页');
    $page->setConfig('first','首页');
    $page->setConfig('last','尾页');
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    $str = $page->show();
    $p = I('get.p');
    $data = $this->page($p,$pagesize)->select();
    return array(
      'data'=>$data,
      'page'=>$str
    );
  }
}
