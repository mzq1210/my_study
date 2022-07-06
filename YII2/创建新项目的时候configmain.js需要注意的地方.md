### 创建新项目的时候config/main.js需要注意的地方

1.common/config/bootstrap中需要添加别名

```
Yii::setAlias('@local', dirname(dirname(__DIR__)) . '/local');
```

2.controllerNamespace 命名空间需要调整

3.user组件需要调整