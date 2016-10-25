
<p align="center"><a href="http://ztbcms.com" target="_blank"><img width="100" src="favicon.ico"></a></p>
 
# 文档

[在线文档](http://ztbcms.com)

----
# 环境要求
* PHP版本需要5.4+ 推荐5.6(未支持PHP 7)
    * php5-curl 
    * php5-gd
* Mysql 5.5+
    * mysql-client
* Apache 2.2(推荐2.4) 

# 下载安装

下载最新**稳定版**
```shell
$ git clone --branch master https://github.com/Zhutibang/ZtbCMS.git
```

下载最新**开发版**(慎用)
```shell
$ git clone https://github.com/Zhutibang/ZtbCMS.git
```

初始化环境,详情请看[Makefile](Makefile)

```shell
#修改权限
$ make setup-env
```

删除多余的`dev.gitignore`
```shell
$ rm dev.ignore
```

## 版本描述

版本含有4部分, 如`1.2.3.4`, 采用`MAJOR.MINOR.FEATURE.PATCH`来描述版本

- MAJOR 大版本号,代码被大量重写,有大部分不兼容的更新
- MINOR 有重要的核心结构改变,可能会导致部分第三方不兼容
- FEATURE  有新特性/扩展加入或更新
- PATCH bug修复

