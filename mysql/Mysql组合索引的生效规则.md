组合索引（a,b,c），组合索引的生效原则是，从前往后依次使用生效，如果中间某个索引没有使用，那么断点前面的索引部分起作用，断点后面的索引没有起作用；

```mysql
组合索引使用判断：

(0) select * from mytable where a=3 and b=5 and c=4;

abc三个索引都在where条件里面用到了，而且都发挥了作用

(1) select * from mytable where c=4 and b=6 and a=3;

这条语句列出来只想说明 mysql没有那么笨，where里面的条件顺序在查询之前会被mysql自动优化，效果跟上一句一样

(2) select * from mytable where a=3 and c=7;

a用到索引，b没有用，所以c是没有用到索引效果的

(3) select * from mytable where a=3 and b>7 and c=3;(范围值就算是断点)

a用到了，b也用到了，c没有用到，这个地方b是范围值，也算断点，只不过自身用到了索引

(4) select * from mytable where b=3 and c=4;

因为a索引没有使用，所以这里 bc都没有用上索引效果

(5) select * from mytable where a>4 and b=7 and c=9;

a用到了 b没有使用，c没有使用

(6) select * from mytable where a=3 order by b;

a用到了索引，b在结果排序中也用到了索引的效果，前面说了，a下面任意一段的b是排好序的

(7) select * from mytable where a=3 order by c;

a用到了索引，但是这个地方c没有发挥排序效果，因为中间断点了，使用 explain 可以看到 filesort

(8) select * from mytable where b=3 order by a;

b没有用到索引，排序中a也没有发挥索引效果

————————————————
原文作者：PHPer技术栈
转自链接：https://learnku.com/articles/59303
版权声明：著作权归作者所有。商业转载请联系作者获得授权，非商业转载请保留以上作者信息和原文链接。
```

