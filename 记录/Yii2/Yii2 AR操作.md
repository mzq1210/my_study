#### Yii2 AR操作

```php
try{
    $res = $model->save(false);
    if($res){
        return $this->asJson(['code' => 200, 'msg' => '登记成功,请等待审核!']);
    }else{
        $err =  $model->getErrorSummary(false)[0];
        return $this->asJson(['code' => 201, 'msg' => $err]);
    }
}catch (Exception $e){
    $post = Yii::$app->request->post();
    $post['errorInfo'] = $e->errorInfo;
    Log::WriteLog($post, 'vote_apply', 1);
    return $this->asJson(['code' => 201, 'msg' => '保存失败,请联系管理员']);
}    
```

#### 事务

```php
/*
 * 事务级别
 * Transaction::READ_COMMITTED 
 */
Yii::$app->db->transaction(function() use ($userId, $params){
    $user = User::findOne($userId);
    $user->load($params);
    if (!$user->save()) {
        $errors = $user->getFirstErrors();
        $error = reset($errors);
    }
}, Transaction::READ_COMMITTED);
```

#### where条件中两字段相加或相减

```php
$query->andWhere(['<', '`updated_at` + `duration`', time()])->all();
```