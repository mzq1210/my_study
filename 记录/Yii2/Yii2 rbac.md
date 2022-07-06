### Yii2 rbac

##### assignment表

> 用来记录用户都被赋予了哪些角色（根据Yii2的设计，权限是不能赋予用户的）。

##### item表

> 用来记录创建了哪些角色，哪些权限。

##### item_child表

> 1、一个角色和它的子角色； 
>
> 2、一个角色和属于它的权限； 
>
> 3、一个权限和属于它的子权限。



##### 用户添加权限流程

```php
$auth = Yii::$app->authManager;

//添加权限
$createPost = $auth->createPermission('路由');
$createPost->description = '创建了权限';
$auth->add($createPost);

//添加角色
$role = $auth->createRole ('角色名');
$role->description = '创建了角色';
$auth->add($role);

//给角色分配权限
$auth->addChild($role, $createPost);

//为用户分配角色，第二个参数为用户ID
$auth->assign($role, 2);

//验证用户是否有权限
public function beforeAction($action)
{
    $action = Yii::$app->controller->action->id;
    if(\Yii::$app->user->can($action)){
        return true;
    }else{
        throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
    }
}
```





##### 



