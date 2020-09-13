<?php

namespace Admin\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ChildController extends CommonController
{
  // 孩子体质报告列表
  public function index()
  {
    $model = D('Child');
    $list = $model->selectAll();
    if (!$list) {
      $this->error($model->getError());
    }
    $this->assign($list);
    $this->display();
  }

  // 导出体质excel
  public function exportPhysiqueExcel()
  {
    $id = I('get.id');
    $model = D('Child');
    $data = $model->findPhysiqueOne($id);
    $physique_asthma = $data['physique_asthma']; // 是否哮喘
    $physique_asthma_list = $data['physique_asthma_list']; // 哮喘问卷答案
    $physique_answer_list = $data['physique_answer_list']; // 体质问卷答案
    $physique_result_list = $data['physique_result_list']; // 体质检测结果
    // 体质问卷表格数据处理
    $user_key = ['ptel', 'name', 'sex', 'age', 'height', 'weight', 'nation', 'address'];
    foreach ($data as $key => $value) {
      if (in_array($key, $user_key)) {
        $info[] = $value;
      }
    }
    // 筛选哮喘数据
    if (intval($physique_asthma) == 1) {
      $info[] = '是';
    } else {
      $info[] = '否';
    }
    $asthma = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    if ($physique_asthma_list) {
      foreach (explode(',', $physique_asthma_list) as $key => $value) {
        $arr = explode('-', $value);
        if (in_array('asthma', $arr)) {
          $info[] = $arr;
        } else {
          foreach ($arr as $k => $v) {
            $arr1[] = $asthma[$v];
          }
          $info[] = $arr1;
        }
      }
    } else {
      $info[] = '患哮喘的年龄：无';
      $info[] = '哮喘的治疗方案：无';
    }

    foreach (explode(',', $physique_answer_list) as $key => $value) {
      $info[] = explode('-', $value);
    }
    foreach (explode(',', $physique_result_list) as $key => $value) {
      $info[] = explode('-', $value);
    }
    $info[] = $data['physique_type'];
    foreach ($info as $key => $value) {
      if (is_array($info[$key])) {
        if (in_array('asthma', $info[$key])) {
          $info[$key] = "{$info[$key][0]}岁{$info[$key][1]}月";
          continue;
        };
        if (in_array('flag', $info[$key])) {
          unset($info[$key][array_search('flag', $info[$key])]);
        }
      }
    }
    // 体质答案跟题目匹配
    $name = "{$data['name']}-体质报告";
    // 数据写入表格
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet(); // 得到一个表格对象
    $sheet->getColumnDimension('B')->setWidth(50);// 设置列宽
    $styleArray = [
      'borders' => [
        'outline' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => '0C0C0C'],
        ],
      ],
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
      ],
    ];
    $num = 1;
    foreach ($info as $key => $value) {
      if (is_array($value)) {
        $length = count($value);
        foreach ($value as $k => $v) {
          $sheet->setCellValue('B' . ($num), $v);
          $sheet->getStyle('B' . ($num))->applyFromArray($styleArray);// 设置单元格样式
          $num++;
        }
        $sheet->setCellValue('A' . ($num - $length), $num - $length);
        $sheet->mergeCells('A' . ($num - $length) . ':A' . ($num - 1));
        $sheet->getStyle('A' . ($num - $length))->applyFromArray($styleArray); // 设置单元格样式
      } else {
        $sheet->setCellValue('A' . ($num), $num);
        $sheet->getStyle('A' . ($num))->applyFromArray($styleArray); // 设置单元格样式
        $sheet->setCellValue('B' . ($num), $value);
        $sheet->getStyle('B' . ($num))->applyFromArray($styleArray);// 设置单元格样式
        $num++;
      }
    }
    //告诉浏览器输出2007本能的Excel文件
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //告诉浏览器输出浏览器名称
    header('Content-Disposition: attachment;filename=' . $name . '.xlsx');
    //禁止缓存
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet); // 把表格对象写入
    $writer->save('php://output'); // 导出表格对象文件
  }

  // 导出饮食问卷
  public function exportDietExcel()
  {
    $id = I('get.id');
    $model = D('Child');
    $data = $model->findChildOne($id);
    if (!$data['answer'] || !$data['family']) {
      $this->error('该孩子尚未做饮食问卷调查！');
    }
    $physique_asthma = $data['physique_asthma']; // 是否哮喘
    $physique_asthma_list = $data['physique_asthma_list']; // 哮喘问卷答案
    // 孩子基础信息数据处理
    $user_key = ['ptel', 'name', 'sex', 'age', 'height', 'weight', 'nation', 'address'];
    foreach ($data as $key => $value) {
      if (in_array($key, $user_key)) {
        $info[] = $value;
      }
    }
    // 哮喘数据处理
    if (intval($physique_asthma) == 1) {
      $info[] = '是';
    } else {
      $info[] = '否';
    }
    if ($physique_asthma_list) {
      foreach (explode(',', $physique_asthma_list) as $key => $value) {
        $arr = explode('-', $value);
        $info[] = $arr;
      }
    } else {
      $info[] = [];
      $info[] = [];
    }

    // 一般家庭调查数据处理
    $family = trim($data['family'], '\'');
    foreach (explode(',', $family) as $key => $value) {
      if ($key > 4) {
        if (strpos($value, '-') || strpos($value, '-') === 0) {
          $info[] = explode('-', $value);
        } else {
          $info[] = [$value];
        }
      } else {
        $info[] = $value;
      }
     /* if (strpos($value,'-') || strpos($value,'-') === 0) {
         $info[] = explode('-',$value);
       } else {
         $info[] = $value;
       }*/
    }
//    dump($info);  exit();
    // 饮食答案处理
    $answer = trim($data['answer'], '\'');
    foreach (explode(',', $answer) as $key => $value) {
      if (strpos($value, 'water') || strpos($value, 'water') === 0) {
        $info[] = explode('-', $value);
        continue;
      }
      if (strpos($value, 'final') || strpos($value, 'final') === 0) {
        $info[] = explode('-', $value);
        continue;
      }
      $arr1 = [];
      foreach (explode('-', $value) as $k => $v) {
        if ($v === 'flag' || $v === 'snack') {
          $arr1[] = $v;
        } else {
          $arr1[] = explode('+', $v);
        }
      }
      $info[] = $arr1;
    }
    // 题库数据
    $excel = array(
      '联系电话：',
      '孩子的姓名：',
      '孩子的性别：',
      '孩子的年龄：',
      '孩子的身高：',
      '孩子的体重：',
      '孩子的民族：',
      '近3年较为固定的生活地址：',
      '孩子是否患哮喘：',
      '患哮喘的年龄是：',
      array('哮喘的治疗方案：', 'A.吸入激素治疗（如布地奈德）', 'B.口服孟鲁斯特纳', 'C.脱敏治疗', 'D.遵照医嘱规律治疗', 'E.未规律治疗'),
      '孩子平均每日睡眠时间是多少小时？  ',
      '您的家庭居住面积是多少平方米？  ',
      '家庭人口数有多少人？   ',
      '您的孩子平均每日完成作业的时间是晚上几点？   ',
      '您的孩子平均一周有几个个课外班？   ',
      array('家庭情况生活情况调查：', '□家中饲养宠物（如猫、狗） ', '□家中环境潮湿（如墙面返潮，墙面等处有较多霉斑霉点）', '□家中有人现在或曾经吸烟', '□近2年居住地有改变', '□近2年按照相关指导调整饮食习惯', '□以上均无'),
      array('谷薯类', '大米', '面食', '小米', '红薯'),
      array('蔬菜类（A.果实类）', '西红柿', '茄子', '豆角', '丝瓜', '青椒', '冬瓜'),
      array('B.根茎类', '土豆', '白萝卜或青萝卜', '胡萝卜', '洋葱', '莲藕'),
      array('C.叶菜类', '白菜', '生菜', '卷心菜', '菠菜', '茼蒿', '空心菜', '芹菜', '茴香', '韭菜', '菜花'),
      array('D.菌藻类', '平菇', '海带', '香菇', '木耳', '其他菌菇类如：', '食用各类蔬菜比例(果实类:根茎类:叶菜类:菌菇类)='),
      array('肉类', '海鱼肉', '河鱼肉', '海虾', '河虾', '羊肉', '牛肉', '鸡肉', '猪肉'),
      array('水果类', '苹果', '梨', '香蕉', '橙子', '桃', '猕猴桃', '芒果', '榴莲', '哈密瓜', '西瓜', '柚子', '蓝莓', '葡萄', '樱桃', '草莓', '荔枝', '其他水果如：'),
      array('蛋、奶类', '鸡蛋', '鸭蛋', '鹌鹑蛋', '牛奶', '羊奶', '酸奶', '其他奶类'),
      array('坚果类', '核桃', '松子', '榛子', '巴旦木', '开心果', '腰果', '葵花子或花生', '每日坚果'),
      array('零食类', '油炸类/膨化类', '甜点类', '巧克力', '奶酪', '碳酸饮料类', '甜饮料类', '果脯蜜饯类', '饭菜中应用辣椒，咖喱及孜然调料的'),
      array('饮水量', '每日饮水量为：'),
      array('调料频率', '饭菜中应用辣椒，咖喱及孜然调料的：'),
      array('主食比例', '主食：蔬菜：肉：蛋奶：水果='),
    );
    // 答案题库数据匹配
    foreach ($excel as $key => $value) {
      if (is_array($info[$key])) {
        if (count($info[$key]) === 0) {
          foreach ($excel[$key] as $key_excel => $excel_value) {
            $excel[$key][$key_excel] .= '';
          }
          continue;
        }
        if (in_array('asthma', $info[$key])) {
          $excel[$key] .= "{$info[$key][0]}岁{$info[$key][1]}月";
          continue;
        };
        if (in_array('flag', $info[$key])) {
          foreach ($excel[$key] as $key_excel => $excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              if (in_array('x', $info[$key][$key_excel])) {
                if (in_array('m', $info[$key][$key_excel])) {
                  $excel[$key][$key_excel] .= " {$info[$key][$key_excel][0]} ";
                  $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][1]}";
                  if ($info[$key][$key_excel][2] == 0) {
                    $excel[$key][$key_excel] .= " （周）";
                  } else {
                    $excel[$key][$key_excel] .= " （月）";
                  }
                  $excel[$key][$key_excel] .= "食用量 {$info[$key][$key_excel][3]} ml";
                  continue;
                }
                $excel[$key][$key_excel] .= " {$info[$key][$key_excel][0]} ";
                $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][1]}";
                if ($info[$key][$key_excel][2] == 0) {
                  $excel[$key][$key_excel] .= " （周）";
                } else {
                  $excel[$key][$key_excel] .= " （月）";
                }
                $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][3]}";
                continue;
              }
              if (in_array('k', $info[$key][$key_excel])) {
                $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
                if ($info[$key][$key_excel][1] == 0) {
                  $excel[$key][$key_excel] .= " （周）";
                } else {
                  $excel[$key][$key_excel] .= " （月）";
                }
                $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][2]} 颗";
                continue;
              }
              if (in_array('m', $info[$key][$key_excel])) {
                $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
                if ($info[$key][$key_excel][1] == 0) {
                  $excel[$key][$key_excel] .= " （周）";
                } else {
                  $excel[$key][$key_excel] .= " （月）";
                }
                $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][2]} ml";
                continue;
              }
              if (in_array('y', $info[$key][$key_excel])) {
                $excel[$key][$key_excel] .= "({$info[$key][$key_excel][0]}:{$info[$key][$key_excel][1]}:{$info[$key][$key_excel][2]}:{$info[$key][$key_excel][3]})";
                continue;
              }
              $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
              if ($info[$key][$key_excel][1] == 0) {
                $excel[$key][$key_excel] .= " （周）";
              } else {
                $excel[$key][$key_excel] .= " （月）";
              }
              $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][2]}";
            }
          }
          continue;
        }
        if (in_array('snack', $info[$key])) {
          foreach ($excel[$key] as $key_excel => $excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
              if ($info[$key][$key_excel][1] == 0) {
                $excel[$key][$key_excel] .= " （周）";
              } else {
                $excel[$key][$key_excel] .= " （月）";
              }
            }
          }
          continue;
        }
        if (in_array('water', $info[$key])) {
          foreach ($excel[$key] as $key_excel => $excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              $excel[$key][$key_excel] .= "{$info[$key][$key_excel]} ml";
            }
          }
          continue;
        }
        if (in_array('final', $info[$key])) {
          foreach ($excel[$key] as $key_excel => $excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              $excel[$key][$key_excel] .= "{$info[$key][1]}:{$info[$key][2]}:{$info[$key][3]}:{$info[$key][4]}:{$info[$key][5]}";
            }
          }
          continue;
        }
        else {
          foreach ($excel[$key] as $key_excel => $excel_value) {
            foreach ($info[$key] as $k => $v) {
              if ($key_excel === (intval($v) + 1)) {
                $excel[$key][$key_excel] .= '  ✔';
              }
            }
          }
        };
      } else {
        $excel[$key] .= $info[$key];
      }
    }
    // 数据写入表格
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet(); // 得到一个表格对象
    $sheet->getColumnDimension('B')->setWidth(80);// 设置列宽
    $styleArray = [
      'borders' => [
        'outline' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          'color' => ['argb' => '0C0C0C'],
        ],
      ],
      'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
      ],
    ];
    foreach (arrTwo_to_arrOne($excel) as $key => $value) {
      $sheet->setCellValue('A' . ($key + 1), $key + 1);
      $sheet->getStyle('A' . ($key + 1))->applyFromArray($styleArray); // 设置单元格样式
      $sheet->setCellValue('B' . ($key + 1), $value);
      $sheet->getStyle('B' . ($key + 1))->applyFromArray($styleArray); // 设置单元格样式
    }
    $writer = new Xlsx($spreadsheet); // 把表格对象写入
    /*******************导出表格***********************/
    $name = "{$data['name']}-饮食报告";
    //告诉浏览器输出2007本能的Excel文件
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //告诉浏览器输出浏览器名称
    header('Content-Disposition: attachment;filename=' . $name . '.xlsx');
    //禁止缓存
    header('Cache-Control: max-age=0');
    $writer->save('php://output'); // 导出表格对象文件
  }
}



