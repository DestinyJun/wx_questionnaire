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
    //告诉浏览器输出2007本能的Excel文件
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //告诉浏览器输出浏览器名称
    header('Content-Disposition: attachment;filename="01simple.xlsx"');
    //禁止缓存
    header('Cache-Control: max-age=0');
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet(); // 得到一个表格对象

    // 体质问卷表格数据处理
    $user_key = ['ptel','name','sex','age','height','weight','nation','address'];
    foreach ($data as $key=>$value) {
      if (in_array($key,$user_key)) {
        $info[] = $value;
      }
    }
    // 筛选哮喘数据
    if (intval($physique_asthma) == 1) {
      $info[] = '是';
    }else {
      $info[] = '否';
    }
    foreach (explode(',',$physique_asthma_list) as $key=>$value) {
      $arr = explode('-',$value);
      $info[] = $arr;
    }
    foreach (explode(',',$physique_answer_list) as $key=>$value) {
      $info[] = explode('-',$value);
    }
    $info[] = $physique_result_list;
    $info[] = $data['physique_type'];

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
      array('哮喘的治疗方案：','A.吸入激素治疗（如布地奈德）','B.口服孟鲁斯特纳','C.脱敏治疗','D.遵照医嘱规律治疗','E.未规律治疗'),
      array('患过敏性疾病么?','A：是','B：否'),
      array('有过敏性家族史?','A：是','B：否'),
      array('您的孩子体型符合以下哪种情况？【可多选】','A.体型正常或结实','B.体型偏矮','C.体型偏瘦','D.体型偏胖'),
      array('A.精力充沛','B.精力旺盛，喜动或活动多','C.容易劳累、没精神'),
      array('A.肌肉紧实','B.肌肉松软','C.肚子大、软'),
      array('A.脸色红润有光泽','B.脸色偏黄','C.脸色偏白、没有光泽','D.脸部油腻'),
      array('A.颜色淡红','B.颜色偏淡','C.颜色偏红'),
      array('A.皮肤润泽','B.皮肤干燥或瘙痒'),
      array('A.头发细','B.头发颜色黄'),
      array('A.舌淡红苔薄白','B.舌淡','C.舌淡胖，苔白滑','D.舌淡苔白','E.舌红','F.舌红苔黄','G.舌红苔黄腻','H.地图舌或者舌苔少','I.舌胖大，苔腻'),
      array('A.下眼睑浮肿','B.起床眼屎多','C.揉鼻子、揉眼睛或眨眼'),
      array('A.喜欢喝水（不包括饮料）','B.不喜欢喝水'),
      array('A.怕冷','B.怕热'),
      array('A.活动后汗多','B.睡觉出汗多','C.出汗黏'),
      array('A.饮食均衡、不挑食','B.饭量小','C.吃肉食偏多','D.吃凉的食物会不舒服'),
      array('A.入睡快，睡眠比较安稳','B.睡眠时间充足的情况下，白天也会打哈欠、犯困','C.上床后需要较长时间才能入睡','D.夜卧睡眠不踏实，来回翻滚','E. 睡觉时眼睛微张（有缝隙）'),
      array('A.大便不干不稀，能定时排便每日1~2次','B.大便干燥（例如球状、粗条状）','C.大便量多、不成形','D.大便粘便盆，不易冲刷干净','E.大便味臭','F.正常饮水量情况下，仍小便黄（6~12岁儿童每天正常饮水量【包括奶类、水果、蔬菜等中的水分】：1500~2000ml，约3~4个矿泉水瓶）','G.小便次数多或量多（6~12岁儿童小便正常量600~1400ml/天，小便正常次数4~7次/天）'),
      array('A.身体健康、很少生病','B.患病后很快康复','C.起口疮、咽痛','D.流鼻血（外伤导致除外）','E说话声音小','F.口气重','G.手脚心热','H.手脚凉','I.肚子痛','J.干呕','K.头晕','L.心慌','M.尿床','N.觉得嗓子有痰','O.身上起湿疹','P.皮肤搔抓后有隆起刮痕','Q.在以下情况下出现打喷嚏、流鼻涕、鼻塞、咳嗽【换季、温度变化、接触花粉或带毛的小动物、装修等可能存在过敏原的地方】','R.接触或食用某过敏原后易起荨麻疹或皮肤痒','S.不喜欢潮湿的环境或在潮湿环境下身体有不适表现（如起皮疹、胸闷、哮喘发作等）'),
      array('患呼吸道感染（如感冒、支气管炎、肺炎）的频率','A.吸入激素治疗（如布地奈德）','B.口服孟鲁斯特纳','C.脱敏治疗'),
      array('您的孩子尿床的频率？','A.几乎每天都有','B.1～4次/每周','C.1～4次/每月','D.数月一次','E.3岁以后几乎不尿床'),
      array('A.喜欢安静、不喜运动','B.性格开朗、爱笑','C.闷闷不乐、唉声叹气','D.胆子小，不喜欢冒险','E.脾气急躁','F.性格温和，脾气好，不易起急','G.遇事易紧张','H.敏感，在乎别人的说法及做法','I.在搬家或换学校后能较快适应环境（包括日常生活、健康状态、心理状态等）'),
      '各个体质得分：',
      '最终输出结果：'
    );
    // 体质答案跟题目匹配
    foreach ($excel as $key=>$value) {
      if (is_array($info[$key])) {
        if (in_array('asthma',$info[$key])) {
          $excel[$key].= "{$info[$key][0]}岁{$info[$key][1]}月";
          continue;
        };
        if (in_array('flag',$info[$key])) {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            $excel[$key][$key_excel] .= "————>{$info[$key][$key_excel]}";
          }
        }
        else {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            foreach ($info[$key] as $k=>$v) {
              if ($key_excel === (intval($v)+1)) {
                $excel[$key][$key_excel].='  ✔';
              }
            }
          }
        };
      } else {
        $excel[$key].= $info[$key];
      }
    }
    // 数据写入表格
    foreach (arrTwo_to_arrOne($excel) as $key=>$value) {
      $sheet->setCellValue('A'.($key+1), $key+1);
      $sheet->setCellValue('B'.($key+1), $value);
    }
    $writer = new Xlsx($spreadsheet); // 把表格对象写入
    $writer->save('php://output'); // 导出表格对象文件
  }

  // 导出饮食问卷
  public function exportDietExcel() {
    $id = I('get.id');
    $model = D('Child');
    $data = $model->findChildOne($id);
    // 哮喘数据
    $physique_asthma = $data['physique_asthma']; // 是否哮喘
    $physique_asthma_list = $data['physique_asthma_list']; // 哮喘问卷答案
    // 孩子基础信息数据处理
    $user_key = ['ptel','name','sex','age','height','weight','nation','address'];
    foreach ($data as $key=>$value) {
      if (in_array($key,$user_key)) {
        $info[] = $value;
      }
    }
    // 哮喘数据处理
    if (intval($physique_asthma) == 1) {
      $info[] = '是';
    }else {
      $info[] = '否';
    }
    foreach (explode(',',$physique_asthma_list) as $key=>$value) {
      $arr = explode('-',$value);
      $info[] = $arr;
    }
    // 一般家庭调查数据处理
    $family = trim($data['family'],'\'');
    foreach (explode(',',$family) as $key=>$value) {
      if (strpos($value,'-') || strpos($value,'-') === 0) {
        $info[] = explode('-',$value);
      } else {
        $info[] = $value;
      }
    }
    // 饮食答案处理
    $answer = trim($data['answer'],'\'');
    foreach (explode(',',$answer) as $key=>$value) {
      if (strpos($value,'water') || strpos($value,'water') === 0) {
        $info[] = explode('-',$value);
        continue;
      }
      if (strpos($value,'final') || strpos($value,'final') === 0) {
        $info[] = explode('-',$value);
        continue;
      }
      $arr1 = [];
      foreach (explode('-',$value) as $k=>$v){
        if ($v === 'flag' || $v === 'snack') {
          $arr1[] = $v;
        }else {
          $arr1[] = explode('+',$v);
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
      array('哮喘的治疗方案：','A.吸入激素治疗（如布地奈德）','B.口服孟鲁斯特纳','C.脱敏治疗','D.遵照医嘱规律治疗','E.未规律治疗'),
      '孩子平均每日睡眠时间是多少小时？  ',
      '您的家庭居住面积是多少平方米？  ' ,
      '家庭人口数有多少人？   ',
      '您的孩子平均每日完成作业的时间是晚上几点？   ',
      '您的孩子平均一周有几个个课外班？   ',
      array('家庭情况生活情况调查：','□家中饲养宠物（如猫、狗） ','□家中环境潮湿（如墙面返潮，墙面等处有较多霉斑霉点）','□家中有人现在或曾经吸烟','□近2年居住地有改变','□近2年按照相关指导调整饮食习惯','□以上均无'),
      array('谷薯类','大米','面食','小米','红薯'),
      array('蔬菜类（A.果实类）','西红柿','茄子','豆角','丝瓜','青椒','冬瓜'),
      array('B.根茎类','土豆','白萝卜或青萝卜','胡萝卜','洋葱','莲藕'),
      array('C.叶菜类','白菜','生菜','卷心菜','菠菜','茼蒿','空心菜','芹菜','茴香','韭菜','菜花'),
      array('D.菌藻类','平菇','海带','香菇','木耳','其他菌菇类如：','食用各类蔬菜比例(果实类:根茎类:叶菜类:菌菇类)='),
      array('肉类','海鱼肉','河鱼肉','海虾','河虾','羊肉','牛肉','鸡肉','猪肉'),
      array('水果类','苹果','梨','香蕉','橙子','桃','猕猴桃','芒果','榴莲','哈密瓜','西瓜','柚子','蓝莓','葡萄','樱桃','草莓','荔枝','其他水果如：'),
      array('蛋、奶类','鸡蛋','鸭蛋','鹌鹑蛋','牛奶','羊奶','酸奶','其他奶类'),
      array('坚果类','核桃','松子','榛子','巴旦木','开心果','腰果','葵花子或花生','每日坚果'),
      array('零食类','油炸类/膨化类','甜点类','巧克力','奶酪','碳酸饮料类','甜饮料类','果脯蜜饯类','饭菜中应用辣椒，咖喱及孜然调料的'),
      array('饮水量','每日饮水量为：'),
      array('主食比例','主食：蔬菜：肉：蛋奶：水果='),
    );
    // 答案题库数据匹配
    foreach ($excel as $key=>$value) {
      if (is_array($info[$key])) {
        if (in_array('asthma',$info[$key])) {
          $excel[$key].= "{$info[$key][0]}岁{$info[$key][1]}月";
          continue;
        };
        if (in_array('flag',$info[$key])) {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              if (in_array('x',$info[$key][$key_excel])) {
                if (in_array('m',$info[$key][$key_excel])) {
                  $excel[$key][$key_excel] .=" {$info[$key][$key_excel][0]} ";
                  $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][1]}";
                  if ($info[$key][$key_excel][2] == 0) {
                    $excel[$key][$key_excel] .=" （周）";
                  } else {
                    $excel[$key][$key_excel] .=" （月）";
                  }
                  $excel[$key][$key_excel] .= "食用量 {$info[$key][$key_excel][3]} ml";
                  continue;
                }
                $excel[$key][$key_excel] .=" {$info[$key][$key_excel][0]} ";
                $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][1]}";
                if ($info[$key][$key_excel][2] == 0) {
                  $excel[$key][$key_excel] .=" （周）";
                } else {
                  $excel[$key][$key_excel] .=" （月）";
                }
                $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][3]}";
                continue;
              }
              if (in_array('k',$info[$key][$key_excel])) {
                $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
                if ($info[$key][$key_excel][1] == 0) {
                  $excel[$key][$key_excel] .=" （周）";
                } else {
                  $excel[$key][$key_excel] .=" （月）";
                }
                $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][2]} 颗";
                continue;
              }
              if (in_array('m',$info[$key][$key_excel])) {
                $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
                if ($info[$key][$key_excel][1] == 0) {
                  $excel[$key][$key_excel] .=" （周）";
                } else {
                  $excel[$key][$key_excel] .=" （月）";
                }
                $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][2]} ml";
                continue;
              }
              if (in_array('y',$info[$key][$key_excel])) {
                $excel[$key][$key_excel] .="({$info[$key][$key_excel][0]}:{$info[$key][$key_excel][1]}:{$info[$key][$key_excel][2]}:{$info[$key][$key_excel][3]})";
                continue;
              }
              $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
              if ($info[$key][$key_excel][1] == 0) {
                $excel[$key][$key_excel] .=" （周）";
              } else {
                $excel[$key][$key_excel] .=" （月）";
              }
              $excel[$key][$key_excel] .= "食用量等级 {$info[$key][$key_excel][2]}";
            }
          }
          continue;
        }
        if (in_array('snack',$info[$key])) {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              $excel[$key][$key_excel] .= "食用频率 {$info[$key][$key_excel][0]}";
              if ($info[$key][$key_excel][1] == 0) {
                $excel[$key][$key_excel] .=" （周）";
              } else {
                $excel[$key][$key_excel] .=" （月）";
              }
            }
          }
          continue;
        }
        if (in_array('water',$info[$key])) {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              $excel[$key][$key_excel] .= "{$info[$key][$key_excel]} ml";
            }
          }
          continue;
        }
        if (in_array('final',$info[$key])) {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            if ($key_excel === 0) {
              continue;
            } else {
              $excel[$key][$key_excel] .= "{$info[$key][1]}:{$info[$key][2]}:{$info[$key][3]}:{$info[$key][4]}";
            }
          }
          continue;
        }
        else {
          foreach ($excel[$key] as $key_excel=>$excel_value) {
            foreach ($info[$key] as $k=>$v) {
              if ($key_excel === (intval($v)+1)) {
                $excel[$key][$key_excel].='  ✔';
              }
            }
          }
        };
      } else {
        $excel[$key].= $info[$key];
      }
    }
    // 数据写入表格
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet(); // 得到一个表格对象
    foreach (arrTwo_to_arrOne($excel) as $key=>$value) {
      $sheet->setCellValue('A'.($key+1), $key+1);
      $sheet->setCellValue('B'.($key+1), $value);
    }
    $writer = new Xlsx($spreadsheet); // 把表格对象写入
    /*******************导出表格***********************/
    //告诉浏览器输出2007本能的Excel文件
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //告诉浏览器输出浏览器名称
    header('Content-Disposition: attachment;filename="01simple.xlsx"');
    //禁止缓存
    header('Cache-Control: max-age=0');
    $writer->save('php://output'); // 导出表格对象文件
//    print_r($info);
//    dump($answer);
//    dump($excel);
  }
}



