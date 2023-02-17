```php
//配置
return [
    //日志使用时需要使用的
    'bootstrap' => ['log'],
    'components' => [
         //日志配置
         'log' => [
            'targets' => [
                /*
                 *使用文件存储日志
                 */
                'file' => [
                     //文件方式存储日志操作对应操作对象
                    'class' => 'yii\log\FileTarget',
                     /* 定义存储日志信息的级别，只有在这个数组的数据才能会使用当前方式存储起来
                      有trace（用于开发调试时记录日志，需要把YII_DEBUG设置为true），
                        error（用于记录不可恢复的错误信息)，
                        warning（用于记录一些警告信息)
                        info(用于记录一些系统行为如管理员操作提示)
                        这些常用的。
                    */
                    'levels' => ['info','error'],
                    /**
                     * 按类别分类
                     * 默认为空，即所有。yii\* 指所有以 yii\ 开头的类别.
                     */
                    'categories' => ['yii\*'],
                ],
                /*
                 *使用数据库存储日志
                 */
                'db' => [
                     //数据库存储日志对象
                    'class' => 'yii\log\DbTarget',
                     //同上
                    'levels' => ['error', 'warning'],
                ]
            ],
        ]
    ]
];
```

1.使用数据库存储日志需要在common/config/main.php配置好数据库db配置，再到项目根目录执行命令创建日志表：

```bash
#–migrationPath指定存储所有迁移类文件的目录，这里的位置是：advanced\vendor\yiisoft\yii2\log\migrations
php yii migrate --migrationPath=@yii/log/migrations/
```

2.使用文件存储日志，默认位置是当前项目的runtime/log/app.log。在file配置里增加如下代码可以更改默认日志位置。

```php
'logFile' => '@runtime/logssss/appsss.log'
```

3.直接使用Yii自带的日志记方法

```php
Yii::trace()：记录一条消息去跟踪一段代码是怎样运行的。这主要在开发的时候使用。
Yii::info()：记录一条消息来传达一些有用的信息。
Yii::warning()：记录一个警告消息用来指示一些已经发生的意外。
Yii::error()：记录一个致命的错误，这个错误应该尽快被检查。
```