<?php
namespace Admin\Model;
class ChildModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id','user_id','report_id','ptel','name','sex','age','height','weight','flag','nation','address','answer','addtime');

  protected $_auto = array(
    array('addtime','time',1,'function')
  );

  // 添加孩子
  public function addRepotr($user_info,$answer,$child) {
    $this->startTrans();
    // 登记用户
    $userModel = D('Admin/User');
    $user_info['addtime'] = time();
    $user_res = $userModel->where("openid='{$user_info['openid']}'")->find();
    if (!$user_res) {
      $user_info['sex'] = $user_info['sex'] == '0'?'未知':($user_info['sex'] == '2'?'女':'男');
      $user_res = $userModel->add($user_info);
      if (!$user_res) {
        $this->rollback();
        $this->error = '用户登记失败，请重新提交';
        return false;
      }
    }
    // 添加报告
    $reportModel = M('physique_report');
    $answer['addtime'] = time();
    $report_res = $reportModel->add($answer);
    if (!$report_res) {
      $this->rollback();
      $this->error = '问卷报告登记失败，请重新提交';
      return false;
    }

    // 登记孩子
    $child['user_id']=$user_res;
    $child['answer']='';
    $child['report_id']=$report_res;
    $child['sex'] = $child['sex'] == '0'?'未知':($child['sex'] == '2'?'女':'男');
    $child_res = $this->add($child);
    if (!$child_res) {
      $this->rollback();
      $this->error = '孩子登记失败，请重新提交';
      return false;
    }
    $this->commit();
    return $child_res;
  }
}
