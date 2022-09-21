1.Your local changes to the following files would be overwritten by merge

出现上述报错的原因是因为其他人修改了xxx仓库文件并提交到了版本库，而我们本地也修改了xxx文件，这时进行pull自然就会产生冲突。

```bash
#修改了同一个文件，需要合并
git stash
git pull
git stash pop
```

2.The following untracked working tree files would be overwritten by merge

与上一个错误的区别是，冲突的文件自己本地的还未提交到仓库（未追踪），而别人先一步提交到了仓库，自己本地的就有删除。操作记得慎重，以免改动文件的丢失。本质上就是操作仓库中没有被追踪的本地文件

```bash
git clean -d -fx 文件名
```

