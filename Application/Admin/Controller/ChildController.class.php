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
  public function exportExcel()
  {
    //告诉浏览器输出2007本能的Excel文件
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //告诉浏览器输出浏览器名称
    header('Content-Disposition: attachment;filename="01simple.xlsx"');
    //禁止缓存
    header('Cache-Control: max-age=0');
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Hello World !');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
  }
}
