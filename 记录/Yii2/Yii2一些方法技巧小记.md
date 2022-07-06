### Yii2一些方法技巧小记

#### LIKE 查询 单边加%

```php
['like', 'name', 'tester'] // name LIKE '%tester%'。
['like', 'name', '%tester', false] // name LIKE '%tester'
$query = User::find()->where(['LIKE', 'name', $id.'%', false]);
```


#### SQL 随机抽取十名幸运用户

```php
$query = new Query;             
$query->select('ID, City,State,StudentName')
      ->from('student')                               
      ->where(['IsActive' => 1])
      ->andWhere(['not', ['State' => null]])
      ->orderBy(['rand()' => SORT_DESC])
      ->limit(10);
```


#### where条件中两字段相加或相减

```php
$query->andWhere(['<', '`updated_at` + `duration`', time()])->all();
```

#### MySQL 数据处理
yii2 给mysql数据库表添加字段后，立即使用这个字段时会出现未定义的情况（Getting unknown property）
原因：yii 对数据表结构进行了缓存。

方法1. 清理掉runtime下的cache缓存之后也可以正常使用这个字段。

方法2. 修改完表字段后执行

```php
//清理指定表结构缓存数据
Yii::$app->db->getSchema()->refreshTableSchema($tableName);

//或清理所有表结构缓存数据
Yii::$app->db->getSchema()->refresh();
```

> 建议将以上代码添加到修改数据表结构的migration中。


#### 查找 auth_times 表 type=1 并且 不存在 auth_item 表里面的数据
```php
// AuthItem.php 关键是 onCondition 方法
public function getAuthTimes()
{
    return $this->hasOne(AuthTimes::className(), ['name' => 'name', ])->onCondition([AuthTimes::tableName() . '.type' => 1]);
}

// AuthTimes.php 文件
// ......
AuthItem::find()->joinWith('authTimes')->where([self::tableName() . '.name' => null])->all();
```

#### 写 log 日志
```php
use yii\log\Logger;
\Yii::getLogger()->log('User has been created', Logger::LEVEL_INFO);
```


#### Yii2 获取接口传过来的 JSON 数据：

```php
\Yii::$app->request->rawBody;
```


#### 点击下载文件 action

```php
public function actionDownload($id)
{
    $model = $this->findModel($id);
    if ($model) {
    	// do something
    }
    return \Yii::$app->response->setDownloadHeaders($model->downurl);
}
```


#### 修改登陆状态超时时间（到期后自动退出登陆） config/web.php中的components

```php
‘user’ => [
    ‘class’=>’yii\web\User’,
    ‘identityClass’ => ‘common\models\User’,
    ‘loginUrl’=>[‘/user/sign-in/login’],
    ‘authTimeout’ => 1800,//登陆有效时间
    ‘as afterLogin’ => ‘common\behaviors\LoginTimestampBehavior’
],
```


#### 数据库有user表有个avatar_path字段用来保存用户头像路径
需求: 头像url需要通过域名http://b.com/作为基本url

User.php



```php
class User extends \yii\db\ActiveRecord
{
    ...
    public function extraFields()
    {
        $fields = parent::extraFields(); $fields['avatar_url'] = function () {
            return empty($this->avatar_path) ? '可以设置一个默认的头像地址' : 'http://b.com/' . $this->avatar_path;
    };

    return $fields;
}
...
```
}

ExampleController.php

```php
class ExampleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $userModel = User::find()->one();
        $userData = $userModel->toArray([], ['avatar_url']);
		echo $userData['avatar_url']; // 输出内容: http://b.com/头像路径
	}
}
```

