分析一：如果单独使用mysql则会产生以下几个问题

1.单表超过千万就要分表

2.全文索引完全无法使用

3.因为innodb索引左侧查询的特性，要根据不同的查询从场景创建超多索引（比如商城根据不同分类筛选商品），导致写性能爆炸



这里就产生了es+mysql的方式，经典架构如下：

<img src="ES+mysql%E6%9E%B6%E6%9E%84.assets/WX20230106-220614@2x.png" alt="WX20230106-220614@2x" style="width: 50%;" align="left"/>

如何保证es和MySQL的数据一致性呢？

musql每5分钟把修改的数据打包发送给mq,通过es订阅mq的方式发送给es



Mysql update的时候是行锁还是表锁还是间隙锁？

示例 test表中只有两行数据 id=10 和 id=50

分为三种情况：

1.如果是id精准匹配，则是行锁 

update test set name='sss' where id=10

2.id不能精准匹配时，是间隙锁 

update test set name='sss' where id=7 （因为不存在数据id=7，所以索引id为1~6行会被锁住，7不会，开区间）

update test set name='sss' where id=12 （因为不存在数据id=12，所以索引id为11~49行会被锁住，10和50不会，开区间）

3.id是范围查找，锁定的是间隙表+行表

update test set name='sss' where id>=8 and id <=11 (锁定范围1~10, 10, 11~49)
