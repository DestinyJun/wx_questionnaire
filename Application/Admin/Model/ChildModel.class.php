<?php
namespace Admin\Model;
class ChildModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id','user_id','report_id','ptel','name','sex','age','height','weight','nation','address','answer','addtime');

  protected $_auto = array(
    array('addtime','time',1,'function')
  );

  // 添加孩子
  public function addRepotr($user_info,$answer,$child) {
    $this->startTrans();
    // 注册用户
    $user_data = array(
      "openid" => $user_info['openid']
    );
  }
}
