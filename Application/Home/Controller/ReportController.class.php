<?php
namespace Home\Controller;
class ReportController extends CommonController {
    public function getReport() {
      $openid = _I('openid');
      if (!$openid) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      $userInfo = M('user')->where("openid='{$openid}'")->find();
      if (!$userInfo) {
        $this->ajaxReturn(array('status'=>'1003','msg'=>'此账户没有进行任何问卷调查'));
      }
      $report = M('child')->alias('a')->field('a.name,a.report_id')->where("a.user_id={$userInfo['id']}")->select();
      if (!$report) {
        $this->ajaxReturn(array('status'=>'1003','msg'=>'此账户没有进行任何问卷调查'));
      }
      $this->ajaxReturn(array('status'=>'1000','msg'=>'查询成功','data'=>$report));
    }
    public function getReportResult() {
      $report_id = intval(_I('report_id'));
      $openid = _I('openid');
      if ($report_id<1 || !$openid) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      $report = M('physique_report')->where("id={$report_id}")->find();
      if (!$report) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      $this->ajaxReturn(array('status'=>'1000','msg'=>'请求成功','data'=>$report));
    }
    public function addReportFamily() {
      $child_id = intval(_I('child_id'));
      $answer = _I('answer');
      if ($child_id<1 || !$answer) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误！'));
      }
      $model = M('child');
      $info = $model->where("id={$child_id}")->find();
      if (!$info) {
        $this->ajaxReturn(array('status'=>'1003','msg'=>'此账户没有进行任何问卷调查！'));
      }
      $res= $model->where("id={$child_id}")->setField("answer","'{$answer}'");
      if (!$res){
        $this->ajaxReturn(array('status'=>'1004','msg'=>'提交失败，请重新提交！'));
      }
      $this->ajaxReturn(array('status'=>'1000','msg'=>'提交成功！'));
    }
}
