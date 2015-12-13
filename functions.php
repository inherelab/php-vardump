<?php

!class_exists('Po',false) && include_once __DIR__.'/Po.php';

/**
* 打印输出, 函数方式调用
*/
if (!function_exists('p'))
{
    function p()
    {
      Po::owner()->p(func_get_args());
    }
}
<<<<<<< HEAD

if (!function_exists('pe'))
{

=======

if (!function_exists('pe'))
{
>>>>>>> 9d049e0376d45793f7ca5e7b7a0dea3b9c65f0fb
    function pe()
    {
      Po::owner()->pe(func_get_args());
    }
}

if (!function_exists('d'))
{
    function d()
    {
      Po::owner()->d(func_get_args());
    }
}

if (!function_exists('de'))
{
    function de()
    {
      Po::owner()->de(func_get_args());
    }
}

if (!function_exists('pr'))
{
    function pr()
    {
      Po::owner()->pr(func_get_args());
    }
}

if (!function_exists('vd'))
{
    function vd()
    {
      Po::owner()->vd(func_get_args());
    }
}

//打印用户定义常量
if (!function_exists('puc'))
{
    function puc() {
        return Po::uc();
    }
}
