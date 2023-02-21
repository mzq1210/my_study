#### AR操作

```php
try{
    $res = $model->save(false);
    if($res){
        return $this->asJson(['code' => 200, 'msg' => '登记成功,请等待审核!']);
    }else{;
        return $this->asJson(['code' => 201, 'msg' => reset($model->getErrors())]);
    }
}catch (Exception $e){
    $post = Yii::$app->request->post();
    $post['errorInfo'] = $e->errorInfo;
    Log::WriteLog($post, 'vote_apply', 1);
    return $this->asJson(['code' => 201, 'msg' => '保存失败,请联系管理员']);
}    
```

#### Model中场景的使用

```php
const SCENARIO_LOGIN = 'login';
const SCENARIO_REGISTER = 'register';
public function scenarios()
{
    $scenarios = parent::scenarios();
    $scenarios[self::SCENARIO_LOGIN] = ['loginEmail', 'password'];
    $scenarios[self::SCENARIO_REGISTER] = ['signupEmail', 'password', 'repassword'];
    return $scenarios;
}

//控制器使用
$model = new UserForm();
$model->setScenario(UserForm::SCENARIO_REGISTER);
if ($model->load(Yii::$app->request->post())) {
    // ......
}
return $this->render('register', [
    'model' => $model,
]);
```

#### 事务

```php
/*
 * 示例1：
 * Transaction::READ_COMMITTED  事务级别
 */
try {
    Yii::$app->db->transaction(function() use ($userId, $params){
        $user = User::findOne($userId);
        $user->load($params);
        if (!$user->save()) {
            $error = reset($user->getErrors());
            throw new \Exception();
        }
    }, Transaction::READ_COMMITTED);
} catch (\Exception $e) {
    $transaction->rollBack();
}

/*
 * 示例2：
 */
public function saveWithTrans()
{
    $transaction = Yii::$app->db->beginTransaction();
    $error = '';
    try {
        //1.事件一todo~~~
        if(!$this->save()){
            $error = firstError($this);
            throw new \Exception();
        }
        //2.事件二todo~~~
        $arr = explode(',', $this->tag);
        Tag::deleteAll(['in', 'name', $arr]);
        //3.事件三todo~~~
        foreach ($arr as $val){
            $model = new Tag();
            $model->id = CommonUtil::uuid();
            $model->name = $val;
            if(!$model->save()){
                $error = firstError($model);
                throw new \Exception();
            }
        }
        $transaction->commit();
    } catch (\Exception $e) {
        $transaction->rollBack();
    }

    return $error;
}

// 调用
if (!empty($error = $model->saveWithTrans())) {
    jsonFail($error);
}
jsonSuccess();
```
### yii2的each()和batch()

> each和batch都是调用BatchQueryResult类，所以都要用foreach来调用，each和batch的区别就是foreach每次取出的数据量，而这个数据量受限于batchSize，默认是100，each和batch都是一次性取出上限batchSize的数据，区别只不过是foreach遍历单次输出的数据each是每次一条，batch每次batchSize条。

```php
//batch第一个参数即为batchSize
foreach(Banner::find()->batch(10) as $key => $users){
    echo '第'.$key.'次取出';
    echo "<br/>";
    foreach($users as $user){
        echo $user['b_name'];
        echo "<br/>";
    }
}
```

### 特殊排序

```php
(new \yii\db\Query)
->from('orders')
->orderBy(new \yii\db\Expression('CASE WHEN status != "ORDER_DONE" THEN 1 ELSE 2 END, status'))
->all();
```

