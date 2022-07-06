#### 自定义log错误文件日志

```php
$error = Yii::$app->errorHandler->exception;
if($error){
    $file = $error->getFile();
    $line = $error->getLine();
    $message = $error->getMessage();
    $code = $error->getCode();

    $log = new FileTarget();
    $log->logFile = Yii::$app->getRuntimePath()."/logs/test_err.log";
    $err_msg = $message. " [file:{$file}][line:{$line}][code:{$code}][url:{$_SERVER['REQUEST_URI']}][POST_DATE:".http_build_query($_POST)."]";
    $log->messages[] = [
        $err_msg,
        1,
        'application',
        microtime(true)
    ];
    $log->export();die;
}
```

