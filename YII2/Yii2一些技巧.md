#### Like 单边查询

```php
['like', 'name', 'tester'] // name LIKE '%tester%'。
['like', 'name', '%tester', false] // name LIKE '%tester'
$query = User::find()->where(['LIKE', 'name', '%' .$keyword, false]);
```


#### Model 随机抽取十名幸运用户

```php
$data = User::find()->select('ID, City,State,StudentName')->orderBy(['rand()' => SORT_DESC])->limit(10);
```


#### where条件中两字段相加或相减

```php
$query->andWhere(['<', '`updated_at` + `duration`', time()])->all();
```

#### MySQL 数据处理
数据表新增字段之后无法保存数据，出现 Getting unknown property……错误
1. 清理掉runtime下的cache缓存之后也可以正常使用这个字段。

2. 修改完表字段后执行以下代码

```php
//清理指定表结构缓存数据
Yii::$app->db->getSchema()->refreshTableSchema($tableName);
//或清理所有表结构缓存数据
Yii::$app->db->getSchema()->refresh();
```

> 建议将以上代码添加到修改数据表结构的migration中。


#### Yii 获取接口传过来的 JSON 数据：

```php
\Yii::$app->request->rawBody;
```


#### 点击下载文件 action

```php
public function actionDownload($id)
{
    $model = $this->findModel($id);
    return \Yii::$app->response->setDownloadHeaders($model->downurl);
}
```


#### 修改登陆状态超时时间（到期后自动退出登陆） config/web.php中的components

```php
'user' => [
    'class' => 'yii\web\User',
    'identityClass' => 'common\models\User',
    'loginUrl' => ['/user/sign-in/login'],
    'authTimeout' => 1800,  //登陆有效时间
    'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
],
```


#### 数据库有user表有个avatar_path字段用来保存用户头像路径
需求: 头像url需要通过域名http://b.com/作为基本url

```php
class User extends \yii\db\ActiveRecord
{
    public function extraFields()
    {
        $fields = parent::extraFields(); 
        $fields['avatar_url'] = function () {
            return empty($this->avatar_path) ? '可以设置一个默认的头像地址' : 'http://b.com/' . $this->avatar_path;
		};
    	return $fields;
	}
}
```

#### YII模块IP白名单设置，增加安全性

```markdown
$config['modules']['gii'] = [
     'class' => 'yii\gii\Module',
     'allowedIPs' => ['127.0.0.1', '::1','10.10.1.*'], 
];
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.33.1'],
];
```

#### 防止 SQL 和 Script 注入

```php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
echo Html::encode($view_hello_str) //可以原样显示<script></script>代码  
echo HtmlPurifier::process($view_hello_str)  //可以过滤掉<script></script>代码
```

#### 关于CSRF验证

方法一：关闭Csrf，除非必要，否则不推荐

```php
public function init(){
    $this->enableCsrfValidation = false;
}
```

方法二：普通提交，form表单中加入隐藏域

```bash
<input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
```

方法三：ajax异步提交，加入_csrf字段

```haskell
var csrfToken = $('meta[name="csrf-token"]').attr("content");
$.ajax({
  type: 'POST',
  url: url,
  data: {_csrf:csrfToken},
  success: success,
  dataType: dataType
});
```

文件操作

```php
// 遍历一个文件夹下文件&子文件夹
FileHelper::findFiles('/path/to/search/');
FileHelper::findFiles('.', ['only' => ['*.php', '*.txt']]); // 只返回php和txt文件
FileHelper::findFiles('.', ['except' => ['*.php', '*.txt']]); // 排除php和txt文件
// 获得指定文件的MIME类型
FileHelper::getMimeType('/path/to/img.jpeg');
// 复制文件夹
FileHelper::copyDirectory($src, $dst, $options = [])
// 删除一个目录及内容
FileHelper::removeDirectory($dir, $options = [])
// 生成一个文件夹（同时设置权限）
FileHelper::createDirectory($path, $mode = 0775, $recursive = true)
```

