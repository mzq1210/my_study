```php
<?php
namespace h5\filter;

class MyException extends \Exception
{
    protected $code = 201;
}
```

```php
<?php
/**
 * Created by PhpStorm.
 * User: tan
 * Date: 2018/6/7
 * Time: 10:19
 */

namespace h5\controllers;

use h5\filter\MyException;
use yii\db\Exception;

class ArtController extends BaseController{

    public function actionIndex(){
        echo '我是Index';

    }

    public function runAction($id, $params = [])
    {
        echo '我在beforeAction前';
        try {
            if(1 == 1){
                throw new Exception('34534534534534534');
//                throw new MyException('这就是对的');
            }
            return parent::runAction($id, $params);
        } catch (MyException $myException) {
            return $this->asJson([
                'code' => $myException->getCode(),
                'message' => $myException->getMessage()
            ]);
        } catch (\Exception $ex) {
            return $this->asJson([
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ]);
            //throw $ex;
        }
    }

    public function beforeAction($action)
    {
        echo '我在前';
        return parent::beforeAction($action);
    }


    public function afterAction($action, $result)
    {
        echo '我在后';
        return parent::afterAction($action, $result);
    }

}

```

