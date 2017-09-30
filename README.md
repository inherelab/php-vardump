
# simple-print-tool
a simple php print tool, 简单的php数据打印工具

## 说明 : 是 √ 否 X
  若使用了命名空间 类方法调用 需在最前加上'\'。 @example \Spt::d($arg1,$arg2,$arg3,...);

  (方法)函数使用| 是否可打印多个参数    | 打印时是否类型输出  | 打印后是否会退出程序   |      补充说明
  ------------------|-------------|------------|-------------|----------------------
   d() / Spt::d()    |       √     |     √      |      X      |          --
   de() / Spt::de()  |       √     |     √      |      √      |         --
   p() / Spt::p()    |       √     |     X      |      X      |           --
   pe() / Spt::pe()  |       √     |     X      |      √      |         --
   pr() / Spt::pr()  |       √     |     X      |      X      |  pr()等同于print_r(),   但可以传入多个参数
   vd() / Spt::vd()  |       √     |     √      |      X      |  vd()等同于var_dump()


## Composer

- command

`composer required inhere/simple-print-tool`

- use composer.json

> stable

```
    "require-dev": {
        "inhere/simple-print-tool": "~1.0"
    }
```

> lastest

```
    "require-dev": {
        "inhere/simple-print-tool": "dev-master"
    }
```

run `composer up`


## Manual Load - 手动加载

```php
// 加载functions.php 既可以函数方式调用也可以 类方法调用
// 如：  d()  de()  p()  pe()  Spt::d() Spt::de() Spt::p() Spt::pe() ....
include './../functions.php';


//也可只加载 Po.php 这时候仅可以用 类方法调用 如： Spt::d() Spt::de() Spt::p() Spt::pe() ...
include './../Po.php';

// simplePrinter.php 则更简单，仅这一个文件，只提供 pr() vd() 两种打印方法。不可与functions.php 同时加载！
include_once './../simplePrinter.php';
```


##效果
<a href="https://raw.githubusercontent.com/inhere/simple-print-tool/master/test/test1.jpg" target="_blank">
![alt text](https://raw.githubusercontent.com/inhere/simple-print-tool/master/test/test1.jpg "Title")
</a>

