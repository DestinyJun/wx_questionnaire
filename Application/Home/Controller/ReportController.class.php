<?php

namespace Home\Controller;
require PROJECT_ROOT.'vendor'.DS.'autoload.php';
use FangStarNet\PHPValidator\Validator;
class ReportController extends CommonController {
    public function getReport() {
      $openid = _I('openid');
      $flag = _I('flag');
      $flag_data = array(1,2);
      if (!$openid) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      if (!$flag || !in_array(intval($flag),$flag_data)) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      $userInfo = M('user')->where("openid='{$openid}'")->find();
      if (!$userInfo) {
        $this->ajaxReturn(array('status'=>'1003','msg'=>'此账户没有进行任何问卷调查'));
      }
      $report = M('child')->alias('a')->field('a.name,a.report_id')->where("a.user_id={$userInfo['id']} AND a.flag={$flag}")->select();
      if (!$report) {
        $this->ajaxReturn(array('status'=>'1005','msg'=>'该账户在此年龄段下没有进行任何问卷调查'));
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
    public function addReport()
    {
      $user_info = _I('user_info');
      $answer = _I('answer');
      $child = _I('child');

      // $user_info校验
      Validator::make($user_info, [
        "openid" => "present|alpha_num",
        "nikename" => "present",
        'sex' => "in:0,1,2",
        "tel" => "present|mobile",
      ]);
      if (Validator::has_fails()) {
        $this->ajaxReturn(array("status" =>'1001',"msg"=>Validator::error_msg()));
      }

      // $answer校验
      Validator::make($answer, [
        "physique_type" => "present",
      ]);
      if (Validator::has_fails()) {
        $this->ajaxReturn(array("status" =>'1001',"msg"=>Validator::error_msg()));
      }

      // $child校验
      Validator::make($child, [
        "ptel" => "present|mobile",
        "name" => "present",
        "sex" => "in:0,1,2",
        "age" => "present",
        "height" => "present|numeric_str",
        "weight" => "present|numeric_str",
        "flag" => "present|in:1,2",
        "nation" => "present",
        "address" => "present",
      ]);
      if (Validator::has_fails()) {
        $this->ajaxReturn(array("status" =>'1001',"msg"=>Validator::error_msg()));
      }
      if ($child['flag']==1 && intval(substr($child['age'],0,1))>=6) {
        $this->ajaxReturn(array("status" =>'1006',"msg"=>'年龄段参数不符合要求！'));
      }
      if ($child['flag']==2 && intval(substr($child['age'],0,1))<6) {
        $this->ajaxReturn(array("status" =>'1006',"msg"=>'年龄段参数不符合要求！'));
      }
      $model = D('Admin/Child');
      $res = $model->addRepotr($user_info, $answer, $child);
      if (!$res) {
        $this->ajaxReturn(array("status" =>'1004',"msg"=>$model->getError()));
      }
      $this->ajaxReturn(array("status" =>'1000',"msg"=>'提交成功！',"data"=>array("child_id"=>$res)));
    }
}
