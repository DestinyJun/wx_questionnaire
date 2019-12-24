# 微信问卷调查体质检测小系统
### 1、目录结构说明
### 2、项目相关说明
### 3、接口状态说明
* （1）1000：请求成功
* （2）1001：参数错误
* （3）1002：没有数据
* （4）1003：此账户没有进行任何问卷调查
* （5）1004：提交失败，请重新提交
* （6）1005：该账户在此年龄段下没有进行任何问卷调查
* （7）1006：年龄段参数不符合要求
* （8）1007：查询的城市不存在
* （9）1008：当前城市未开通问卷调查
* （9）1009：当前用户已经做了家庭问卷调查，请不要重复提交！
### 4、接口地址
* 添加问卷调查报告
  > 接口地址：http://192.168.28.245/Home/Report/addReport  
  > 请求参数说明：
  
#### user_info参数说明：
|字段说明       |所属属性         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|openid|user_info|string|必填|-|
|nikename|user_info|string|必填|-|
|sex| user_info|string|必填|在“男”跟“女”之间任选其一|
|tel| user_info|string|必填| 11位的正确格式电话号码|

#### answer参数说明：
|字段说明       |所属属性         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|physique_type|answer |string|必填|各个体质之间用英文,号隔开|
|physique_type_enable| answer|string|必填|各个体质之间用英文,号隔开|

#### child参数说明： 
  |字段说明       |所属属性         |类型            |必填            |备注     |
      | -------------|:--------------:|:--------------:|:--------------:| ------:|
      |ptel|child |string|必填|11位的正确格式电话号码|
      |name| child|string|必填|各个体质之间用英文,号隔开|
      |sex| child|string|必填|在“男”跟“女”之间任选其一|
      |age| child|string|必填| |
      |height| child|number|必填| |
      |weight| child|number|必填| |
      |flag| child|number|必填| 1或者2，1表示3-6岁，2表示6-12岁 |
      |nation| child|string|必填| |
      |address| child|string|必填| |
   ``` json
   {
   	"user_info":{
   		"openid":"1234567891",
   		"nikename":"小明",
   		"sex":"男",
   		"tel":"13888888888"
   	},
   	"answer":{
   		"physique_type":"痰湿质,平和质,气虚质",
   		"physique_type_enable":"痰湿质,平和质,气虚质"
   	},
   	"child":{
   		"ptel":"13888888888",
   		"name":"小小明",
   		"sex":"女",
   		"age":"5岁6个月",
   		"height":100.0,
   		"flag":2,
   		"weight":20,
   		"nation":"汉",
   		"address":"贵州省贵阳市白云区"
   	}
   }
   ```
  > 请求类型：POST   
  > Content-Type：application/javascript  
  > 返回数据：
  ``` json
    {
        "status": "1000",
        "msg": "提交成功！"
    }
  ```
* 获取已提交的问卷调查
  > 接口地址：http://192.168.28.245/Home/Report/getReport  
  > 请求参数说明： 
  
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|openid| |string|Y|用户的openID|
|flag| |string|Y|年龄段标识|

``` json
{"openid":"123","flag":"2"}
```

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
|openid| |string|Y|用户的openID|
|report_id| |string|Y|问卷调查ID|
    
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
