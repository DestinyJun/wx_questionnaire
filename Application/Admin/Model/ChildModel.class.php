<?php

namespace Admin\Model;

use Think\Page;

class ChildModel extends CommonModel
{
  // 字段静态化
  protected $fields = array('id', 'user_id', 'report_id', 'ptel', 'name', 'sex', 'age', 'height', 'weight', 'flag', 'nation', 'address', 'signature', 'family', 'answer', 'addtime', 'is_do', 'is_diet');

  protected $_auto = array(
    array('addtime', 'time', 1, 'function')
  );

  // 添加孩子
  public function addRepotr($user_info, $answer, $child)
  {
    $answerArr = $answer['physique_result_list'];
    foreach ($answerArr as $key => $value) {
      $pArr[] = $value[1];
    };
    // 1是第一种情况，2是第二种情况，3是第三种情况
    // 第一种情况：只要有一个体质>=40
    foreach ($pArr as $key => $value) {
      if ($answerArr[$key][0] !== '平和质') {
        if ($value >= 40) {
          $pJust = 1;
          break;
        }
      } else {
        if ($value >= 60) {
          $pJust = 2;
        }
        if ($value < 60) {
          $pJust = 3;
        }
      }
    }
    // 找出对应条件的分数
    foreach ($pArr as $key => $value) {
      if ($answerArr[$key][0] !== '平和质') {
        $pointArr[] = $value;
      }
    }
    switch ($pJust) {
      case 1:
        foreach ($pointArr as $key=>$value) {
          if ($value >= 40) {
            $pointFour[] = $value;
            $pointFourKey[] = $key;
          }
          if ($value >= 34 && $value < 40){
            $pointCenter[] = $value;
            $pointCenterKey[] = $key;
          }
        }
        if(count($pointFourKey) == 1) {
          $pAnswerArr[] = $answerArr[$pointFourKey];
        }
        break;
    }
    var_dump($pointCenter);
    die();
    $this->startTrans();
    // 登记用户
    $userModel = D('Admin/User');
    $user_info['addtime'] = time();
    $user_res = $userModel->where("openid='{$user_info['openid']}'")->find();
    if (!$user_res) {
      $user_info['sex'] = $user_info['sex'] == '0' ? '未知' : ($user_info['sex'] == '2' ? '女' : '男');
      $user_res = array('id' => $userModel->add($user_info));
      if (!$user_res) {
        $this->rollback();
        $this->error = '用户登记失败，请重新提交';
        return false;
      }
    }

    // 添加报告
    $reportModel = M('physique_report');
    $answer['addtime'] = time();
    $answer['physique_type'] = array_to_str($answer['physique_type']);
    $answer['physique_asthma_list'] = array_to_str($answer['physique_asthma_list']);
    $answer['physique_result_list'] = array_to_str($answer['physique_result_list']);
    $answer['physique_answer_list'] = array_to_str($answer['physique_answer_list']);
    $report_res = $reportModel->add($answer);
    if (!$report_res) {
      $this->rollback();
      $this->error = '问卷报告登记失败，请重新提交';
      return false;
    }

    // 登记孩子
    $child['user_id'] = $user_res['id'];
    $child['answer'] = '';
    $child['addtime'] = time();
    $child['report_id'] = $report_res;
    $child['sex'] = $child['sex'] == '0' ? '未知' : ($child['sex'] == '2' ? '女' : '男');
    $child_res = $this->add($child);
    if (!$child_res) {
      $this->rollback();
      $this->error = '孩子登记失败，请重新提交';
      return false;
    }
    $this->commit();
    return $child_res;
  }

  // 查询孩子体质报告列表
  public function selectAll()
  {
    // 查询总条数
    $count = $this->count();
    // 定义每页查询的条数
    $pageSize = 10;
    // new一个分页类
    $page = new Page($count, $pageSize);
    // 自定义分页按钮内容
    $page->setConfig('prev', '上一页');
    $page->setConfig('next', '下一页');
    $page->setConfig('first', '首页');
    $page->setConfig('last', '尾页');
    $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    // 得到分页的HTML
    $str = $page->show();
    // 接收当前分页
    $p = I('get.p');
    // 查询数据列表
    $data = $this->alias('a')->field('a.*,b.physique_type,c.nikename')
      ->join('left join wx_physique_report b on a.report_id=b.id')
      ->join('left join wx_user c on a.user_id=c.id')
      ->page($p, $pageSize)->select();
    if (!$data) {
      $this->error = '暂无数据!';
    }
//    dump($data);exit();
    return array(
      'data' => $data,
      'page' => $str
    );
  }

  // 查询单个孩子的体质数据
  public function findPhysiqueOne($id)
  {
    $data = $this->alias('a')
      ->join('left join wx_physique_report b on a.report_id=b.id')
      ->where("a.id=$id")->find();
    if (!$data) {
      $this->error = '暂无数据!';
    }
    return $data;
  }

  // 查询单个孩子基本信息
  public function findChildOne($id)
  {
    $data = $this->alias('a')
      ->field('a.*,b.physique_asthma,b.physique_asthma_list')
      ->join('left join wx_physique_report b on a.report_id=b.id')
      ->where("a.id=$id")->find();
    if (!$data) {
      $this->error = '暂无数据!';
    }
    return $data;
  }
}
