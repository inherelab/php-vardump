<?php
/**
* 打印输出, 函数方式调用
*/
if (!function_exists('p'))
{
    function p(...$vars)
    {
      Spt::own()->p(...$vars);
    }
}

if (!function_exists('pe'))
{
    function pe(...$vars)
    {
      Spt::own()->pe(...$vars);
    }
}

if (!function_exists('d'))
{
    function d(...$vars)
    {
      Spt::own()->d(...$vars);
    }
}

if (!function_exists('de'))
{
    function de(...$vars)
    {
      Spt::own()->de(...$vars);
    }
}

if (!function_exists('pr'))
{
    function pr(...$vars)
    {
      Spt::own()->pr(...$vars);
    }
}

if (!function_exists('vd'))
{
    function vd(...$vars)
    {
      Spt::own()->vd(...$vars);
    }
}

//打印用户定义常量
if (!function_exists('puc'))
{
    function puc() {
        Spt::own()->puc();
    }
}
