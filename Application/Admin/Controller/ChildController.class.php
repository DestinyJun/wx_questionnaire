<?php
namespace Admin\Controller;
class ChildController extends CommonController
{
  // 孩子体质报告列表
  public function index() {
    $model = D('Child');
    $list = $model->selectAll();
    if (!$list) {
      $this->error($model->getError());
    }
    $this->assign($list);
    $this->display();
  }
}
