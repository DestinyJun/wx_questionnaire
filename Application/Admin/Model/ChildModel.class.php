<?php
namespace Admin\Model;
class ChildModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id','user_id','report_id','ptel','name','sex','age','height','weight','nation','address','answer','addtime');

  protected $_auto = array(
    array('addtime','time',1,'function')
  );
}
