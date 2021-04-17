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
    // 先排序
    for($i=0, $len=count($answerArr)-1; $i<$len; ++$i)
    {
      for($j=$len; $j>$i; --$j)
      {
        if($answerArr[$j][1] < $answerArr[$j-1][1])
        {
          $temp = $answerArr[$j];
          $answerArr[$j] = $answerArr[$j-1];
          $answerArr[$j-1] = $temp;
        }
      }
    }
    $answerArr = array_reverse($answerArr);
    // 3-7岁
    if ($child['flag'] == 1) {
      // 找出平和质
      foreach ($answerArr as $key => $value) {
        if($value[0] === '平和质') {
          $answerIndex = $key;
        }
      };
      // 从数组中剔除平和质
      foreach ($answerArr as $key => $value) {
        if ($answerArr[$key][0] == '平和质') {
          continue;
        }
        $lessAnswer[] = $value;
      };
      // 拿到除平和质外的所有体质分数
      foreach ($lessAnswer as $key => $value) {
        $pArr[] = $value[1];
      };
      // 0什么都不是，1是第一种情况，2是第二种情况，3是第三种情况
      $pJust = 0;
      // 判断第一种情况：只要有一个体质>=40
      foreach ($pArr as $key => $value) {
        if ($value >= 40) {
          $pJust = 1;
          break;
        }
      };
      // 判断第二种情况
      if ($pArr[0] < 40 && $answerArr[$answerIndex][1] >=60) {
        $pJust = 2;
      }
      // 判断第三种情况
      if ($pArr[0] < 40 && $answerArr[$answerIndex][1] < 60) {
        $pJust = 3;
      }
      // 输出第一种体质
      if ($pJust === 1) {
        // 第一种情况：只有一种体质>=40分，只有一个体质在30-40之间
        foreach ($pArr as $key => $value) {
          if ($value<34) {
            $pJustIndex = $key;
            break;
          }
        };
        if ($pJustIndex<3) {
          $physiqueArr = [$lessAnswer[0],['倾向', 0],$lessAnswer[1]];
        } else {
          // 第二种情况：有多个体质在34-40之间或者大于40
          $physiqueArr = [$lessAnswer[0],$lessAnswer[1],$lessAnswer[2]];
        }
      }
      // 输出第二种体质
      if ($pJust === 2) {
        // 第一种情况：除平和质外其他体质都<34
        if ( $pArr[0] < 34) {
          $physiqueArr = [$answerArr[$answerIndex]];
        } else {
          // 第二种情况：有多个体质在34-40之间
          $physiqueArr = [$answerArr[$answerIndex],['倾向', 0],$lessAnswer[0],$lessAnswer[1]];
        }
      }
      //输出第三种体质
      if ($pJust === 3) {
        $physiqueArr = [$answerArr[$answerIndex],$lessAnswer[0],$lessAnswer[1]];
      }
    }
    // 7-14岁
    else {
      // 找出平和质
      foreach ($answerArr as $key => $value) {
        if($value[0] === '平和质') {
          $answerIndex = $key;
        }
      };
      // 从数组中剔除平和质
      foreach ($answerArr as $key => $value) {
        if ($answerArr[$key][0] == '平和质') {
          continue;
        }
        $lessAnswer[] = $value;
      };
      // 拿到除平和质外的所有体质分数
      foreach ($lessAnswer as $key => $value) {
        $pArr[] = $value[1];
      };
      // 0什么都不是，1是第一种情况，2是第二种情况，3是第三种情况
      $pJust = 0;

      // 判断第一种情况：只要有一个体质>=44
      foreach ($pArr as $key => $value) {
        if ($value >= 44) {
          $pJust = 1;
          break;
        }
      };
      // 判断第二种情况
      if ($pArr[0] < 44 && $answerArr[$answerIndex][1] >=53) {
        $pJust = 2;
      }
      // 判断第三种情况
      if ($pArr[0] < 44 && $answerArr[$answerIndex][1] < 53) {
        $pJust = 3;
      }

      // 输出第一种体质
      if ($pJust === 1) {
        // 第一种情况：有多个他体质大于38分
        if ( $pArr[2] >= 38) {
          $physiqueArr = [$lessAnswer[0],['倾向', 0],$lessAnswer[1],$lessAnswer[2]];
        }
        // 第二种情况，只有一种他体质大于等于38
         else if ( $pArr[0] >= 44 && $pArr[1]>=38 && $pArr[1] < 44) {
          $physiqueArr = [$lessAnswer[0],['倾向', 0],$lessAnswer[1]];
        }
      }
      // 输出第二种体质
      if ($pJust === 2) {
        // 第一种情况：除平和质外其他体质都<38
        if ( $pArr[0] < 38) {
          $physiqueArr = [$answerArr[$answerIndex]];
        } else if ( $pArr[1] >= 38) {
          // 第二种情况：有一个人体质大于等38
          $physiqueArr = [$answerArr[$answerIndex],$lessAnswer[0],$lessAnswer[1]];
        } else if ( $pArr[0] >= 38) {
          // 第二种情况：有一个人体质大于等38
          $physiqueArr = [$answerArr[$answerIndex],['倾向', 0],$lessAnswer[0]];
        }
      }
      //输出第三种体质
      if ($pJust === 3) {
        $physiqueArr = [$answerArr[$answerIndex],$lessAnswer[0],$lessAnswer[1]];
      }
    }
    // 数据库操作
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
    $answer['physique_type'] = array_to_str($physiqueArr);
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
    foreach ($physiqueArr as $value) {
      $result_res[] = array('name' => $value[0],'value'=>$value[1]);
    };
    return array('child_id'=>$child_res,'physique_type'=>$result_res);
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
