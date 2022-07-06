### 开启和禁用hyper-v（这么麻烦，鉴于也需要其他环境，不如直接安装vm，在vm里运行docker）

Hyper-V会和Vmware，VirtualBox产生冲突，hyper-v和Vmware等只能开启一个。使用vmware产品时必须禁用hyper-v，使用hyper-v时必须开启hyper-v，否则在bios里已经启用了虚拟硬件并开启了hyper-v组件启动Docker时也会提示：Hardware assisted virtualization and data execution protection must be enabled in the BIOS



管理员模式运行 CMD:

```bash
#禁掉 Hyper-V
bcdedit /set hypervisorlaunchtype off
```

```bash
#开启Hyper-V
bcdedit /set hypervisorlaunchtype auto
```

```bash
#附赠一条开启hyper-v组件的命令：
dism.exe /Online /Enable-Feature:Microsoft-Hyper-V /All
```



其他问题参考：[](https://blog.csdn.net/m0_43438893/article/details/110260427)