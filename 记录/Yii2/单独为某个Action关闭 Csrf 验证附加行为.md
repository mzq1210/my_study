### 单独为某个Action关闭 Csrf 验证附加行为

新建一个Behavior

```php
use Yii;
use yii\base\Behavior;
use yii\web\Controller;

class NoCsrf extends Behavior
{
    public $actions = [];
    public $controller;
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }
    public function beforeAction($event)
    {
        $action = $event->action->id;
        if(in_array($action, $this->actions)){
            $this->controller->enableCsrfValidation = false;
        }
    }    
}
```

然后在Controller中添加Behavior

```php
public function behaviors()
{
    return [
        'csrf' => [
            'class' => NoCsrf::className(),
            'controller' => $this,
            'actions' => [
                'action-name'
            ]
        ]
    ];
}
```