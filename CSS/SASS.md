```scss
//http://course.tpframe.com/course/index.html

$baseColor: #333;
// 变量
p{
    color: $baseColor;
    font-size: 16px;
    a{
        color: #fff;
        // 属性嵌套
        font: {
            size: 20px;
            weight: 700;
        }
        &:hover{
            color: #333;
        }
    }
    .top{
        color: #fff;
        // 父选择器 &
        &-left{
            color: #333;

        }
    }
}

// 占位符
.button%base {
    display: inline-block;
    margin-bottom: 0;
    font-weight: normal;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.btn-default {
    @extend %base;
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

.btn-success {
    @extend %base;
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}

.btn-danger {
    @extend %base;
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}

//注释  单行不会编译进源码，多行会编译进去

// 变量类型
$layer-index:10;
$border-width:3px;
$font-base-family:'Open Sans', Helvetica, Sans-Serif;
$top-bg-color:rgba(255,147,29,0.6);
$block-base-padding:6px 10px 6px 10px;
$blank-mode:true;
$var:null; // 值null是其类型的唯一值。它表示缺少值，通常由函数返回以指示缺少结果。
$color-map: (color1: #fa0000, color2: #fbe200, color3: #95d7eb);
$fonts: (serif: "Helvetica Neue",monospace: "Consolas");

.container {
    $font-size: 16px !global;
    font-size: $font-size;
    @if $blank-mode {
        background-color: #000;
    }
    @else {
        background-color: #fff;
    }
    content: type-of($var);
    content:length($var);
    color: map-get($color-map, color2);
}

.footer {
    font-size: $font-size;
}

// 如果列表中包含空值，则生成的CSS中将忽略该空值。
.wrap {
    font: 18px bold map-get($fonts, "sans");
}

//默认值
$color:#333;
// 如果$color之前没定义就使用如下的默认值
$color:#666 !default;
.container {
    border-color: $color;
}

// ************************    @import **********************
// import导入别使用url或带.css，否则不会编译
@import 'public';
div {
    font-size: $ffff;
}

// ************************    @mixin混入 **********************
@mixin block {
    width: 96%;
    margin-left: 2%;
    border-radius: 8px;
    border: 1px #f6f6f6 solid;
}

// 使用
.container {
    .block {
        @include block;
    }
}

// ************************   @extend（继承） **********************
// 这里也可以用混入实现，但混入的话等于同样的代码复制了多次，代码量就变大了
%alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-size: 12px;
}

.alert-info {
    @extend %alert;
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bce8f1;
}

.alert-success {
    @extend %alert;
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

.alert-warning {
    @extend %alert;
    color: #8a6d3b;
    background-color: #fcf8e3;
    border-color: #faebcc;
}

.alert-danger {
    @extend %alert;
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

// ************************   运算符 **********************
$theme:1;
.container {
    @if $theme == 1 {
        background-color: red;
    }
    @else {
        background-color: blue;
    }
}

.container {
    @if $theme >= 1 {
        background-color: red;
    }
    @else {
        background-color: blue;
    }
}

$width:100;
$height:200;
$last:false;
div {
    @if $width>50 and $height<300 {
        font-size: 16px;
    }
    @else {
        font-size: 14px;
    }
    @if not $last {
        border-color: red;
    }
    @else {
        border-color: blue;
    }
}

// ************************   if条件控制语句 **********************
@mixin triangle($direction:top, $size:30px, $border-color:black) {
    width: 0px;
    height: 0px;
    display: inline-block;
    border-width: $size;
    border-#{$direction}-width: 0;
    @if ($direction==top) {
        border-color: transparent transparent $border-color transparent;
        border-style: dashed dashed solid dashed;
    }
    @else if($direction==right) {
        border-color: transparent transparent transparent $border-color;
        border-style: dashed dashed dashed solid;
    }
    @else if($direction==bottom) {
        border-color: $border-color transparent transparent transparent;
        border-style: solid dashed dashed dashed;
    }
    @else if($direction==left) {
        border-color: transparent $border-color transparent transparent;
        border-style: dashed solid dashed dashed;
    }
}

.p1 {
    @include triangle(right, 50px, red);
}

.p2 {
    @include triangle(bottom, 50px, blue);
}

// ************************   for语句 **********************
@for $i from 1 to 4 {
    .p#{$i} {
        width: 10px * $i;
        height: 30px;
        background-color: red;
    }
}
// 与上面的无区别，类似于 <4 和 <=3 的概念
@for $i from 1 through 3 {
    .p#{$i} {
        width: 10px * $i;
        height: 30px;
        background-color: red;
    }
}

@for $i from 1 to 5 {
    #loading span:nth-child(#{$i}) {
        left: 20 * ($i - 1) + px;
        /* animation-delay: 20 * ($i - 1) / 100 + s; */
        // 按照上面的注释是可以实现动画效果的，但算数太复杂，精简之后把 0. 用字符串拼接，但这样输出的是一个带双引号的字符串，而unquote可以去掉双引号
        animation-delay: unquote($string: "0.") + ($i - 1) * 2 + s;
    }
}

// ************************   each语句 **********************
$color-list:red green blue turquoise darkmagenta;
@each $color in $color-list {
    $index: index($color-list, $color);
    .p#{$index - 1} {
        background-color: $color;
    }
}

// ************************   while语句 **********************
$column:12;
@while $column>0 {
    .col-sm-#{$column} {
        width: $column / 12 * 100%;
        // width: $column / 12 * 100 + %; 会标红
        width: $column / 12 * 100#{"%"};
        width: unquote($string: $column / 12 * 100 + "%");
    }

    // 重点，不能没有，否则死循环
    $column:$column - 1;
}

// ************************   function语句 **********************
@function row-cols-width($column) {
    @return percentage(1 / $column);
}

@for $i from 1 through 6 {
    .row-cols-#{$i}>* {
        width: row-cols-width($i);
    }
}


// ************************   三元表达式 **********************

$theme:'light';
.container {
    color: if($theme=='light', #000, #FFF);
}


// 模块化概念，是import的升级版，同样的文件多次use只加载一次，为了避免这个限制可以为每个模块文件设置别名，使用的时候也要带上别名
@use 'use/common';
@use 'use/global' as g1;
@use 'use/global' as g2;
body {
    font-size: common.$font-size;
    @include g1.base('#FFF');
    @include g2.base('#000');
    width: common.column-width(3, 12);
    @include common.bgColor('#F00');
}

// ************************   @use 模块定义私有成员 **********************
//如果变量只想在模块内使用，可使用-或_定义在变量头即可
$-font-size:14px;
* {
    margin: 0;
    padding: 0;
    font-size: $-font-size;
    color: #333;
}

// ************************   @use 模块默认值及修改 **********************
$font-size:14px !default;
@use 'use/common' with ( $font-size:16px);


/*
@use使用总结
@use引入同一个文件多次，不会重复引入，而@import会重复引入
@use引入的文件都是一个模块，默认以文件名作为模块名，可通过as alias取别名
@use引入多个文件时，每个文件都是单独的模块，相同变量名不会覆盖，通过模块名访问，而@import变量会被覆盖
@use方式可通过 @use 'xxx' as *来取消命名空间，建议不要这么做
@use模块内可通过$- 或$来定义私有成员，也就是说或者-开头的Variables mixins functions 不会被引入
@use模块内变量可通过！default 定义默认值，引入时可通用with（...）的方式修改
可定义-index.scss或_index.scss来合并多个scss文件，它@use默认加载文件(意思是在某个目录下建立一个index文件，index文件中把这个目录下的所有文件都加载进去，这样其他文件要引入整个目录模块的时候只引入该目录下的index就把所有的引入了)
*/

// ************************   @forward转发 **********************
// 外部文件通过index引入某个目录下全部文件的时候，变量、混入、函数都是无法使用的，这时候就需要转发一下
// 转发也可以给模块设置别名（前缀 + *），hide的意思是某个变量\函数不转发
@forward 'uses/common' as com-* hide com-bgColor,$com-font-size;
// 也可以覆盖模块变量
@forward 'uses/common' as com-* with ( $font-size:30px !default);
@forward 'uses/global' as glob-* show base;

@use 'bootstrap';
.body {
    font-size: bootstrap.$com-font-size;
    width: bootstrap.com-column-width(3, 12);
    @include bootstrap.com-bgColor('#000');
    @include bootstrap.glob-base('#000');
}

// 当一个模块里面须要同时使用@use与@forward时，建议先使用@forwar后再使用@use
@use 'uses/code';
@forward 'uses/common' as com-*;
@forward 'uses/global' as glob-* show glob-base;
@use 'use/common' as c1;
.test {
    font-size: c1.$font-size;
    color: code.$color;
}
```

