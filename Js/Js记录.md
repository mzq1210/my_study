### Js记录

```javascript
//1.两种类型定义方式不同，但都可以使用push()方法
var data = [];
var data = {};

//1.二维数组循环赋值，需要先给一维数组定义一下空数组
//2.改变VV，原数组的数据也会改变
$.each(data, function (i, item) {
    tempArr[i] = [];
    $.each(item, function (ii, vv) {
        var date = new Date(vv[0]);
        var dstr = date.getFullYear() + '年';
        vv[0] = dstr;
        tempArr[i][dstr] = vv;
    });
});

//1.关联数组（即对象）循环
for (let item in tempArr) {  
    mydata.push(tempArr[item])
}
```

