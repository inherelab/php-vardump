<?php

// 既可以函数方式调用也可以 类方法调用
// 如：  d()  de()  p()  pe()  Spt::d() Spt::de() Spt::p() Spt::pe()
include_once dirname(__DIR__).'/functions.php';

//也可只加载 Po.php 这时候仅可以用 类方法调用 如： Spt::d() Spt::de() Spt::p() Spt::pe()
include_once dirname(__DIR__).'/Spt.php';

$d = array(
    false,
    true,
    null,
    234,
    34.67,
    'werfdfdfdf'=>'werfdfdfdf',
    'yyyyyyyy'=>array(
        'ttttttttttttt',
        'yyyyyyyyyyyyy',
        'kkkkkkkkkkkk'=>array('uuuuuuuuuu',1323)
        )
    );
$j = json_encode($d);
$o = json_decode($j);

// \Po::hidden();

d('wwwwwwwwww',23453545,$d,$o);
p('wwwwwwwwww',23453545,$d,$o);
pr('ddddddddd',23453545,$d,$o);
vd('ddddddddd',23453545,$d,$o);
pe('ddddddddd',23453545,$d,$o);// 会退出
de($_SERVER);// 会退出
