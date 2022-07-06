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

