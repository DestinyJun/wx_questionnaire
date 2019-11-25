<?php
namespace Admin\Controller;
class AdminController extends CommonController
{
  // 管理员列表
  public function index() {
    $data = D('Admin')->select();
    $this->assign('data',$data);
    $this->display();
  }

  // 管理员添加
  public function add() {
    if (IS_GET) {
      $this->display();
    } else {
      $model = D('Admin');
      $data = $model->create();
      if (!$data) {
        $this->error($model->getError());
      }
      $res = $model->add($data);
      if (!$res){
        $this->error($model->getError());
      }
      $this->success('添加成功','/Admin'.U('index'));
    }
  }

  // 管理员删除
  public function del() {
    $id = intval(I('get.id'));
    if($id<=1) {
      $this->error('参数错误');
    }
    $res = D('Admin')->where("id={$id}")->delete();
    if (!$res){
      $this->error(D('Admin')->getError());
    }
    $this->success('删除成功','/Admin'.U('index'));
  }

  // 编辑管理员
  public function edit() {
    $model = D('Admin');
    if (IS_GET) {
      $id = I('get.id');
      if ($id<=1) {
        $this->error('参数错误');
      }
      $data = $model->where("id={$id}")->find();
      if (!$data){
        $this->error($model->getError());
      }
      $this->assign('data',$data);
      $this->display();
    } else {
      $data = $model->create();
      if (!$data){
        $this->error($model->getError());
      }
      $res = $model->save($data);
      if (!$res){
        $this->error($model->getError());
      }
      $this->success('修改成功','/Admin'.U('index'));
    }
  }

}
