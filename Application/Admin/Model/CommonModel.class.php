<?php
namespace Admin\Model;
use Think\Model;

class CommonModel extends Model
{
  public function __construct($name = '', $tablePrefix = '', $connection = '')
  {
    parent::__construct($name, $tablePrefix, $connection);
  }
}
