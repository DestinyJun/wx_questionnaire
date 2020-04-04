<?php
namespace Home\Controller;
use FangStarNet\PHPValidator\Validator;
class ReportController extends CommonController {

    // 区域开放查询
    public function authReport() {
      $openid = _I('openid');
      $city_name = _I('city_name');
      $data = array(
        'openid'=>$openid,
        'city_name'=>$city_name,
      );
      // $user_info校验
      Validator::make($data, [
        "openid" => "present",
        "city_name" => "present",
      ]);
      if (Validator::has_fails()) {
        $this->ajaxReturn(array("status" =>'1001',"msg"=>Validator::error_msg()));
      }
      $city_info = M('city')->where("city_name like '{$city_name}%'")->find();
  //      $user_info = M('user')->where("openid='{$openid}'")->find();
      if (!$city_info) {
        $this->ajaxReturn(array('status'=>'1007','msg'=>'查询的城市不存在!'));
      }
      if ($city_info['is_open']) {
        $this->ajaxReturn(array('status'=>'1000','msg'=>'当前城市已开通问卷调查!'));
      } else {
        $this->ajaxReturn(array('status'=>'1008','msg'=>'当前城市尚未开通问卷调查!'));
      }
    }

    // 添加体质问卷调查
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
        "physique_asthma" => "in:0,1",
        "physique_result_list" => "present",
        "physique_answer_list" => "present",
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
      // 6-12岁
      if (intval($child['flag'])==1 && intval(substr($child['age'],0,strrpos($child['age'],'岁')))>=6) {
        $this->ajaxReturn(array("status" =>'1006',"msg"=>'年龄段参数不符合要求！'));
      }
      // 3-6岁
      if (intval($child['flag'])==2 && intval(substr($child['age'],0,strrpos($child['age'],'岁')))<6) {
        $this->ajaxReturn(array("status" =>'1006',"msg"=>'年龄段参数不符合要求！'));
      }
//      dump(array_to_str($answer['physique_asthma_list']));exit();
//      $this->ajaxReturn(array("status" =>'1000',"msg"=>'提交成功！',"data"=>implode('-',$answer['physique_result_list'])));
      $model = D('Admin/Child');
      $res = $model->addRepotr($user_info, $answer, $child);
      if (!$res) {
        $this->ajaxReturn(array("status" =>'1004',"msg"=>$model->getError()));
      }
      $this->ajaxReturn(array("status" =>'1000',"msg"=>'提交成功！',"data"=>array("child_id"=>$res)));
    }

    // 添加饮食问卷调查
    public function addReportFamily() {
      $child_id = intval(_I('child_id'));
      $answer = _I('answer');
      $family = _I('family');
      if ($child_id<1 || !$answer) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误！'));
      }
      $model = M('child');
      $info = $model->where("id={$child_id}")->find();
      if (!$info) {
        $this->ajaxReturn(array('status'=>'1003','msg'=>'此账户没有进行任何问卷调查！'));
      }
      if (!($info['answer']=='')) {
        $this->ajaxReturn(array('status'=>'1009','msg'=>'当前用户已经做了家庭问卷调查，请不要重复提交！'));
      }
      $answer = array_to_str_2($answer);
      $family = array_to_str($family);
//      dump($family);exit();
      $res= $model->where("id={$child_id}")->setField("answer","'{$answer}'");
      if (!$res){
        $this->ajaxReturn(array('status'=>'1004','msg'=>'提交失败，请重新提交！'));
      }
      $model->where("id={$child_id}")->setField("is_do",1);
      $model->where("id={$child_id}")->setField("family","'{$family}'");
      $this->ajaxReturn(array('status'=>'1000','msg'=>'提交成功！'));
    }

    // 获取历史填写问卷
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

    // 获取体质报告结果
    public function getReportResult() {
      $report_id = intval(_I('report_id'));
      $openid = _I('openid');
      if (intval($report_id)<1 || !$openid) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      $report = M('physique_report')->alias('a')->
      join("left join wx_child b on a.id=b.report_id")->
      field('a.physique_type,a.physique_type_enable,b.is_do,b.is_diet')->
      where("a.id={$report_id}")->find();
      if (!$report) {
        $this->ajaxReturn(array('status'=>'1001','msg'=>'参数错误'));
      }
      $this->ajaxReturn(array('status'=>'1000','msg'=>'请求成功','data'=>$report));
    }

    // 测试
    public function test() {
      $answer = _I('answer');
      $this->ajaxReturn(array('status'=>'1000','msg'=>'提交成功！','data'=> $answer));
    }

}
