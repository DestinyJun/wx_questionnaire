<?php
return array(
  /*** 说明：'配置项'=>'配置值' ***/
	// 配置模板替换
 'TMPL_PARSE_STRING' => array(
   '__PUBLIC_ADMIN__' => '/Public/Admin'  // 后台静态资源目录
 ),
  // 配置url访问模式
  'URL_MODEL' => 2,  // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
  // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  重写模式); 3 (兼容模式)  默认为PATHINFO 模式
  'DEFAULT_MODULE'        =>  'Admin',  // 默认模块
  'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
  'DEFAULT_ACTION'        =>  'index', // 默认操作名称
  'MODULE_ALLOW_LIST'     =>  array('Admin','Home'), // 设置容许访问的模块（好像主配置文件里面没有,但是配置有效）

  // 配置数据库
  'DB_TYPE' => 'mysql',     // 数据库类型
  'DB_HOST' => '127.0.0.1', // 服务器地址
  'DB_NAME' => 'wx_questionnaire',          // 数据库名
  'DB_USER' => 'root',      // 用户名
  'DB_PWD' => 'root',          // 密码
  'DB_PORT' => '3306',        // 端口
  'DB_PREFIX' => 'wx_',    // 数据库表前缀

  // TP开发者工具
  'SHOW_PAGE_TRACE' => false,    // 开启TP的开发者工具
);
