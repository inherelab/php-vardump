<?php
/**
* 打印输出, 函数方式调用
*/
if (!function_exists('p'))
{
    function p()
    {
      Spt::own()->p(func_get_args());
    }
}

if (!function_exists('pe'))
{
    function pe()
    {
      Spt::own()->pe(func_get_args());
    }
}

if (!function_exists('d'))
{
    function d()
    {
      Spt::own()->d(func_get_args());
    }
}

if (!function_exists('de'))
{
    function de()
    {
      Spt::own()->de(func_get_args());
    }
}

if (!function_exists('pr'))
{
    function pr()
    {
      Spt::own()->pr(func_get_args());
    }
}

if (!function_exists('vd'))
{
    function vd()
    {
      Spt::own()->vd(func_get_args());
    }
}

//打印用户定义常量
if (!function_exists('puc'))
{
    function puc() {
        return Spt::own()->puc();
    }
}
