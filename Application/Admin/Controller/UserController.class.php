<?php
namespace Admin\Controller;
class UserController extends CommonController
{
  // 列表
  public function index() {
    $data = D('User')->selectAll();
    $this->assign($data);
    $this->display();
  }

  // 修改
  public function edit() {
    $model = D('User');
    if (IS_GET) {
      $id = I('get.id');
      if ($id<1) {
        $this->error('参数错误');
      }
      $data = $model->where("id={$id}")->find();
      if(!$data) {
        $this->error($model->getError());
      }
      $this->assign('data',$data);
      $this->display();
    } else {
      $data = $model->create();
      if (!$data) {
        $this->error($model->getError());
      }
      $res = $model->save($data);
      if (!$res) {
        $this->error($model->getError());
      }
      $this->success('修改成功！',U('index'));
    }
  }

  // 删除
  public function del() {
    $id = intval(I('get.id'));
    if($id<0) {
      $this->error('参数错误');
    }
    $res = D('User')->where("id={$id}")->delete();
    if (!$res){
      $this->error(D('User')->getError());
    }
    $this->success('删除成功',U('index'));
  }
}
