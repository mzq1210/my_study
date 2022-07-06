## CSS记录
[TOC]

### flex布局

采用 Flex 布局的元素，称为"<font color=#1E90FF >容器</font>"。它的所有子元素统称为"<font color=#1E90FF >项目</font>"。<font color=#1E90FF >容器</font>默认存在两根轴：水平的主轴<font color=#1E90FF >（main）</font>和垂直的交叉轴<font color=#1E90FF >(cross)</font>

```css
.box{
	/*任何一个容器都可以指定为 Flex 布局。*/
	display: -webkit-flex; /* Safari */
	display: flex;
    
	/*行内元素也可以使用 Flex 布局。*/
	display: inline-flex;
}
```

> 注意，设为 Flex 布局以后，子元素的`float`、`clear`和`vertical-align`属性将失效。



#### 容器的属性

- flex-direction  决定项目的排列方向（<font color=008000 >一般默认即可</font>）

  ```css
  .box {
  	flex-direction: row | row-reverse | column | column-reverse;
  }
  ```

  > - `row`（默认值）：主轴为水平方向，起点在左端。
  > - `row-reverse`：主轴为水平方向，起点在右端。
  > - `column`：主轴为垂直方向，起点在上沿。
  > - `column-reverse`：主轴为垂直方向，起点在下沿。



- flex-wrap  定义如果一条轴线排不下，如何换行

  ```css
  .box{
  	flex-wrap: nowrap | wrap | wrap-reverse;
  }
  ```

  - `nowrap`（默认）：不换行，<font color=008000 >所以项目宽度自然失效。</font>
  - `wrap`：换行，第一行在上方。
  - `wrap-reverse`：换行，第一行在下方。

  

- flex-flow  flex-direction`属性和`flex-wrap`属性的简写形式，默认值为`row nowrap

  ```css
  .box {
  	flex-flow: <flex-direction> || <flex-wrap>;
  }
  ```

  

- justify-content  项目在主轴上的对齐方式。<font color=008000 >该属性常用</font>

  ```css
  .box {
    justify-content: flex-start | flex-end | center | space-between | space-around;
  }
  ```

  > - `flex-start`（默认值）：左对齐
  > - `flex-end`：右对齐
  > - `center`： 居中
  > - `space-between`：两端对齐，项目之间的间隔都相等。
  > - `space-around`：每个项目两侧的间隔相等。所以，项目之间的间隔比项目与边框的间隔大一倍。

  

- align-items  项目在交叉轴上如何对齐。

  ```css
  .box {
  	align-items: flex-start | flex-end | center | baseline | stretch;
  }
  ```

  > - `flex-start`：交叉轴的起点对齐，即项目都在容器最上面。
  > - `flex-end`：交叉轴的终点对齐，即项目都在容器最下面。
  > - `center`：交叉轴的中点对齐，相当于项目全部垂直居中。
  > - `baseline`: 所有项目的第一行文字的最底部对齐。
  > - `stretch`（默认值）：如果项目未设置高度或设为auto，将占满整个容器的高度。

  

- align-content  多根轴线的对齐方式。如果项目只有一根轴线，该属性不起作用<font color=008000 >（不常用）</font>。

  ```css
  .box {
  	align-content: flex-start | flex-end | center | space-between | space-around | stretch;
  }
  ```
  
  > - `flex-start`：与交叉轴的起点对齐。
  > - `flex-end`：与交叉轴的终点对齐。
  > - `center`：与交叉轴的中点对齐。
  > - `space-between`：与交叉轴两端对齐，轴线之间的间隔平均分布。
  > - `space-around`：每根轴线两侧的间隔都相等。所以，轴线之间的间隔比轴线与边框的间隔大一倍。
  > - `stretch`（默认值）：轴线占满整个交叉轴。



#### 项目的属性

- `order`  项目的排列顺序。数值越小，排列越靠前，默认为0。

  ```css
  .item {
    order: <integer>;
  }
  ```

  

- `flex-grow`  项目的放大比例，默认为`0`，即如果存在剩余空间，也不放大。如果所有项目的属性都为1，则它们将等分剩余空间（如果有的话）。如果一个项目的`flex-grow`属性为2，其他项目都为1，则前者占据的剩余空间将比其他项多一倍。

  ```css
  .item {
    flex-grow: <number>; /* default 0 */
  }
  ```

  

- `flex-shrink `  项目的缩小比例，默认为1，即如果空间不足，该项目将缩小。如果所有项目的`flex-shrink`属性都为1，当空间不足时，都将等比例缩小。如果一个项目的`flex-shrink`属性为0，其他项目都为1，则空间不足时，前者不缩小。

  ```css
  .item {
    flex-shrink: <number>; /* default 1 */
  }
  ```

  

- `flex-basis `  在分配多余空间之前，项目占据的主轴空间（main size）。浏览器根据这个属性，计算主轴是否有多余空间。它的默认值为`auto`，即项目的本来大小。它可以设为跟`width`或`height`属性一样的值（比如350px），则项目将占据固定空间。

  ```css
  .item {
    flex-basis: <length> | auto; /* default auto */
  }
  ```

  

- `flex `   是`flex-grow`, `flex-shrink` 和 `flex-basis`的简写，默认值为`0 1 auto`

  ```css
  .item {
    flex: none | [ <'flex-grow'> <'flex-shrink'>? || <'flex-basis'> ]
  }
  ```

  

- `align-self`   允许单个项目有与其他项目不一样的对齐方式（举例：其他项目都漂浮在最顶端，特定项目可以下沉到最底端），可覆盖`align-items`属性。默认值为`auto`，表示继承父元素的`align-items`属性，如果没有父元素，则等同于`stretch`。

  ```css
  .item {
    align-self: auto | flex-start | flex-end | center | baseline | stretch;
  }
  ```
  
  

[具体可参考]: http://static.vgee.cn/static/index.html



### 常用属性

box-sizing 为元素指定的padding和border将在设定的宽度和高度内进行绘制。



### BEM 命名规范

#### 常见class关键词

> -  布局类：header, footer, container, main, content, aside, page, section
> -  包裹类：wrap, inner
> -  区块类：region, block, box
> -  结构类：hd, bd, ft, top, bottom, left, right, middle, col, row, grid, span
> -  列表类：list, item, field
> -  主次类：primary, secondary, sub, minor
> -  大小类：s, m, l, xl, large, small
> -  状态类：active, current, checked, hover, fail, success, warn, error, on, off
> -  导航类：nav, prev, next, breadcrumb, forward, back, indicator, paging, first, last
> -  交互类：tips, alert, modal, pop, panel, tabs, accordion, slide, scroll, overlay,
> -  星级类：rate, star
> -  分割类：group, seperate, divider
> -  等分类：full, half, third, quarter
> -  表格类：table, tr, td, cell, row
> -  图片类：img, thumbnail, original, album, gallery
> -  语言类：cn, en
> -  论坛类：forum, bbs, topic, post
> -  方向类：up, down, left, right
> -  其他语义类：btn, close, ok, cancel, switch; link, title, info, intro, more, icon; form, label, search, contact, phone, date, email, user; view, loading...



#### 制定简单规则

> -  以中划线连接，如`.item-img`
> -  使用两个中划线表示特殊化，如`.item-img--small`表示在`.item-img`的基础上特殊化
> -  状态类直接使用单词，参考上面的关键词，如`.active, .checked`
> -  图标以`icon-`为前缀（字体图标采用`.icon-font-name`方式命名）。
> -  js操作的类统一加上`js-`前缀
> -  不要超过四个class组合使用，如`.a.b.c.d`



#### 规则搭配常用关键词使用

>```css
>常规化:
>.header>.inner-center
>.section-feature>.inner-center 
>.section-main>.inner-center
>.section-postscript>.inner-center
>.footer>.inner-center
>
>模块化:
>区块头部.block-hd(hd为header简写)，modal头部.modal-hd，文章头部.article-hd。
>标题也可以分为，页面标题.page-tt(title的简写)，区块标题.block-tt等。
>
>特殊化:
>1：直接修改class，将.page-tt修改成.page-user-tt。
>2：追加class特殊化，根据上面定义的规则，在.page-tt上追加一个class成为.page-tt .page-tt--user。
>3：使用父类，给一个范围，于是形成.page-user .page-tt。
>一般使用的是第二种和第三种办法，因为这两种都有共同的.page-tt，可以比较方便控制一些基础共有的样式。
>
>层级:
>// 继承式
>ul.card-list
>    li.list-item
>        a.item-img-link>img.item-img
>        h3.item-tt>a.item-tt-link
>        p.item-text
> 
>// 关键词式
>ul.card-list
>    li.item
>        a.field-img-link>img.field-img
>        h3.field-tt>a.field-tt-link
>        p.field-text
>
>由上可以看出继承式一般子元素接着父元素的最后一个单词如li接着ul的list，而li的子元素接着li的item；至于关键词式则完全由关键词来表示层级，list>item>filed正好构成三层等级。
>
>小总结：
>.header, .content, .footer
>	.article-hd, .article-content, .article-footer, .active
>		.article-list
>			.list-item
>				.item-img, .item-tt, .item-text
>					.item-tt--link, .item-tt--pos
>				
>```

