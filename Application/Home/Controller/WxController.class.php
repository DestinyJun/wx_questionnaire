<?php
namespace Home\Controller;
class WxController extends CommonController
{
  private $data = array(
    'appid'=>'wxeb179cf05615d750',
    'secret'=>'0805d17ef8c764aab02b8604c1d3855c',
    'js_code'=>'',
    'grant_type'=>'authorization_code'
  );
  private $url = 'https://api.weixin.qq.com/sns/jscode2session';
  public function getOpenid() {
    $this->data['js_code'] = I('get.js_code');
    $data = http_curl($this->url,$this->data,'get',true);
    $this->ajaxReturn($data);
  }
}
