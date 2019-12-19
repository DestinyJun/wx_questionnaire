<?php
namespace Admin\Controller;
class AreaController extends CommonController
{
  // 列表初始化
  public function index() {
    $province = M('province')->select();
    $city = M('city')->where("province_code=110000")->select();
    if (!$province){
      $this->error('查询出错！');
    }
    $this->assign(array(
      'province'=>$province,
      'city'=>$city,
    ));
    $this->display();
  }

  // 获取市级数据
  public function selectCity() {
    $code = I('get.code');
    $city = M('city')->where("province_code={$code}")->select();
    if (!$city) {
      $this->ajaxReturn(array('status'=>1001,'msg'=>'查询数据出错！'));
    }
    $this->ajaxReturn($city);
  }

  // 修改
  public function edit() {
    $city_code = I('get.city_code');
    $is_open = intval(I('get.is_open'));
    if(!$city_code) {
      $this->ajaxReturn(array('status'=>1002,'msg'=>'参数错误！'));
    }
    $data = array('is_open'=>$is_open);
    $res = M('city')->where("city_code={$city_code}")->save($data);
    if (!$res) {
      $this->ajaxReturn(array('status'=>1001,'msg'=>'操作失败！'));
    }
    $this->ajaxReturn(array('status'=>1000,'msg'=>'操作成功！'));
  }
}
