<?php

if (!class_exists('Po',false) ) {
    require_once __DIR__.'/Po.php';
}

/**
* 打印输出, 函数方式调用
*/
if (!function_exists('p'))
{
    function p()
    {
      Po::own()->p(func_get_args());
    }
}

if (!function_exists('pe'))
{
    function pe()
    {
      Po::own()->pe(func_get_args());
    }
}

if (!function_exists('d'))
{
    function d()
    {
      Po::own()->d(func_get_args());
    }
}

if (!function_exists('de'))
{
    function de()
    {
      Po::own()->de(func_get_args());
    }
}

if (!function_exists('pr'))
{
    function pr()
    {
      Po::own()->pr(func_get_args());
    }
}

if (!function_exists('vd'))
{
    function vd()
    {
      Po::own()->vd(func_get_args());
    }
}

//打印用户定义常量
if (!function_exists('puc'))
{
    function puc() {
        return Po::own()->puc();
    }
}
