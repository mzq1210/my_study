#### nodejs

> 运行在服务端的 JavaScript，作用上类似于PHP、Python或Ruby等动态的编程语言。

```bash
#查看node 版本
node -v
```



#### nvm  [安装](https://github.com/coreybutler/nvm-windows/releases)

> 管理node版本

```bash
#查看nvm版本
nvm list
```



#### npm

> nodeJs环境下“安装”开源JS库的工具，安装nodeJs就安装好了npm。

```bash
#查看npm版本
npm -v

#安装最新版本
npm install npm@latest -g

#使用
#本项目安装
npm install <Module Name>
#全局安装
npm install -g <Module Name>

#更新本地安装包：
npm update
#更新全局安装包：
npm update -g

#卸载本地安装包,全局同理
npm uninstall <package>

#更换淘宝镜像（永久）
npm config set registry https://registry.npm.taobao.org

#通过cnpm使用
npm install -g cnpm --registry=https://registry.npm.taobao.org
#成功后会有版本信息返回，不成功有可能是node版本低
cnpm -v

#记录在package.json的“devDependencies”里
--save-dev
```

