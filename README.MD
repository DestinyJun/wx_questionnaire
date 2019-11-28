# 微信问卷调查体质检测小系统
### 1、目录结构说明
### 2、项目相关说明
### 3、接口状态说明
* （1）1000：请求成功
* （2）1001：参数错误
* （3）1002：没有数据
* （4）1003：此账户没有进行任何问卷调查
* （5）1004：提交失败，请重新提交
### 4、接口地址
* 获取已提交的问卷调查
  > 接口地址：http://192.168.28.245/Home/Report/getReport  
  > 请求参数：``{"openid":"123"}``  
  > 请求类型：POST   
  > Content-Type：application/javascript  
  > 返回数据：
  ``` json
    {
        "status": "1000",
        "msg": "查询成功",
        "data": [
            {
                "name": "小小明1",
                "report_id": "1"
            }
        ]
    }
  ```
* 获取单个问卷测算结果
  > 接口地址：http://192.168.28.245/Home/Report/getReportResult  
  > 请求参数：``{"openid":"123","report_id":"1"}``  
  > 请求类型：POST 

    |字段名称       |字段说明         |类型            |必填            |备注     |
    | -------------|:--------------:|:--------------:|:--------------:| ------:|
    |a| |string|Y|-|
    |b| |string|Y|-|
    
  > Content-Type：application/javascript  
  > 返回数据：
  ``` json
    {
        "status": "1000",
        "msg": "请求成功",
        "data": {
            "id": "1",
            "physique_type": "肾虚质,阴虚质",
            "addtime": "1574772843"
        }
    }
  ```
* 添加家庭调查答案
  > 接口地址：http://192.168.28.245/Home/Report/addReportFamily  
  > 请求参数：``{"child_id":"1","answer":"A,B,C,D,12次"}``  
  > 请求类型：POST   
  > Content-Type：application/javascript  
  > 返回数据：
  ``` json
    {
        "status": "1000",
        "msg": "提交成功！"
    }
  ```
