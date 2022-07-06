### GridView

![](C:\Users\mzq\Desktop\1531204914545512.jpg)

#### 表格、字段预处理

```php
try {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
		//自定义需要显示的字段
        'columns'=>[
            'id',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
            //设置label
            [
                'label'=>'会员名',
                'attribute'=>'username',
            ],
            //多字段组合
            [
                'label'=>'省市',
                //按照province排序，但是attribute的本意并不在此，可以不写
                'attribute'=>'province',
                //这个属性控制字段是否排序
                'enableSorting'=>false,
                //组合
                'value'=>function($model){
                    return "{$model->province}-{$model->city}";
                },
                /*因为GridView的table分为headerOptions、contentOptions和footerOptions三部分，所以可以分别设置样					式，如：列的头部变成红色，列的内容变成蓝色。headerOptions和contentOptions直接作用到了th和td标签，为其				  增加类似于style等属性，因此如果th或td标签中还有其他的html标签，直接定义style就无法生效了，此时可以					通过css类解决这个问题。
                */
                'headerOptions' => ['style'=>'color:red'],
                'contentOptions' => ['style'=>'color:blue'],
                //这里footerOptions不能单独使用，需要和下面的showFooter配合使用
                'footerOptions'=>['style'=>'color:yellow'],
                //控制该列显示隐藏
                'visible'=>(Yii::$app->admin->id === 1)
            ]
        ],
        'showFooter'=>true,
        //当数据为空的时候，table框架是否存在，默认不存在。
        'showOnEmpty'=>false,
        //layout内有5个可以使用的值，分别为{summary}、{errors}、{items}、{sorter}和{pager}。我们可以改变这个模板，比			如要去掉summary。
        'layout'=>"{items}\n{pager}",
        
        //针对特殊的类单独指定其class
        'dataColumnClass'=>"yii\grid\DataColumn",
        //修改表头
        'caption'=>"会员列表",
        'captionOptions' => [],
        
        //控制存放table的这个盒子div样式
        'options' => [],
        //控制该table的样式
        'tableOptions' => [],
        //控制td行的展示样式，比如偶数行背景变为红色
        'rowOptions'=>function($model,$key, $index, $grid){
            if($index%2 === 0){
                return ['style'=>'background:red'];
            }
        }
        //要注意的是，匿名函数返回的结果也会作为一行纳入到渲染过程，beforeRow同理
        'afterRow'=>function($model,$key, $index,$grid){
            if($index%2 === 0){
                return "<tr><td colspan='4'>我是偶数</td></tr>";
            }
        }，
        //如果一个单元格为空，默认值。
        'emptyCell' => ''
    ]);
}catch(\Exception $e){
    // todo
}
```



### 下拉搜索

```php
public static function dropDown ($column, $value = null) {
    $dropDownList = [
        'is_delete'=> [
            '0'=>'显示',
            '1'=>'删除',
        ],
        'is_hot'=> [
            '0'=>'否',
            '1'=>'是',
        ],
        //有新的字段要实现下拉规则，可像上面这样进行添加
        // ......
    ];
    //根据具体值显示对应的值
    if ($value !== null) 
        return array_key_exists($column, $dropDownList) ? $dropDownList[$column][$value] : false;
    //返回关联数组，用户下拉的filter实现
    else
        return array_key_exists($column, $dropDownList) ? $dropDownList[$column] : false;
}

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        // ......
        [
            'attribute' => 'is_hot',
            'value' => function ($model) {
                return Article::dropDown('is_hot', $model->is_hot);
            },
            'filter' => Article::dropDown('is_hot'),
        ],
        [
            'attribute' => 'is_delete',
            'value' => function ($model) {
                return Article::dropDown('is_delete', $model->is_delete);
            },
            'filter' => Article::dropDown('is_delete'),
        ],
        // ......
    ],
]); ?>
```



#### 显示图片

```php
[
    'label' => '头像',
    'format' => [
        'image',
        [
            'width'=>'84',
            'height'=>'84'
        ]
    ],
    'value' => function ($model) {
        return $model->image;
    }
],
```



#### html渲染

```php
[
    'attribute' => 'title',
    'value' => function ($model) { 
    return Html::encode($model->title); 
    },
    'format' => 'raw',
],
```



#### 链接可点击跳转案例

```php
[
    'attribute' => 'order_id',
    'value' => function ($model) {
        return Html::a($model->order_id, "/order?id={$model->order_id}", ['target' => '_blank']);
    },
    'format' => 'raw',
],
```

