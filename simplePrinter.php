<?php
if (!headers_sent()) {
    header('content-Type:text/html;charset=utf-8');
}

static $__print_style_out_mark = FALSE;

if (!function_exists('spr')) {
    /**
     * spr === print_r 但支持传入多个参数
     * @internal param string $value [description]
     * @return void [type]        [description]
     */
    function spr()
    {
        $param = func_get_args();
        $outString = '';
        $last = array_pop($param);

        ___getFunctionCalledPosition();

        foreach ($param as $key => $value) {
            $outString .= __dump($value, false);
        }

        $exit = false;

        if ($last === -4)
            $exit = true;
        else
            $outString .= __dump($last, false);

        ___output($outString, $exit);
    }
}

if (!function_exists('svd')) {
    /**
     * 多个打印 === var_dump
     * @return void [type] [description]
     */
    function svd()
    {
        $param = func_get_args();
        $last = array_pop($param);
        $outString = '';

        ___getFunctionCalledPosition();

        foreach ($param as $value) {
            $outString .= __dump($value);
        }

        $exit = false;

        if ($last === -4)
            $exit = true;
        else
            $outString .= __dump($last);

        ___output($outString, $exit);
    }
}

function ___output($string, $exit = false)
{
    if ($exit) {
        exit($string);
    }

    echo $string;
}

function __dump($data, $hasType = true)
{
    # 使用系统函数打印
    $outString = ___getSystemPrintData($data, $hasType);
    $outString = trim($outString);

    $output = '<!-- output print start -->';
    $output .= "\n<div class=\"general-print-box general-print-font general-print-shadow\">\n%s\n</div>\n";
    $output .= '<!-- output print end -->';

    if (preg_match('/^<pre[\s]*/i', $outString) !== 1) {
        $outString = "<pre>$outString</pre>";
    }

    $output = sprintf($output, $outString);

    return $output;
}

/**
 * @param string $var 要查找的变量
 * @param array $scope 要搜寻的范围
 * @return mixed
 */
function get_variable_name(&$var, array $scope = null)
{

    $scope = $scope ?: $GLOBALS; // 如果没有范围则在globals中找寻

    // 因有可能有相同值的变量,因此先将当前变量的值保存到一个临时变量中,
    // 然后再对原变量赋唯一值,以便查找出变量的名称,找到名字后,将临时变量的值重新赋值到原变量
    $tmp = $var;
    $var = 'tmp_value_' . mt_rand();
    $name = array_search($var, $scope, true); // 根据值查找变量名称
    $var = $tmp;

    return $name;
}

function ___getSystemPrintData($data, $hasType = true)
{
    $fun = $hasType ? 'var_dump' : 'print_r';

    ob_start();
    $fun($data);
    $string = ob_get_clean();
    $string = str_replace("=>\n ", '=>', $string);

    return $string;
}

function ___getFunctionCalledPosition($backNum = 2, $separator = '#1', $return = false)
{
    global $__print_style_out_mark;

    ob_start();
    if ($phpGt54 = version_compare(PHP_VERSION, '5.4.0', '>=')) {
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $backNum);
    } else {
        debug_print_backtrace(false);
    }
    $positionInfo = ob_get_clean();
    $positionInfo = strstr($positionInfo, $separator);

    if (!$phpGt54) {
        $positionInfo = strstr($positionInfo, ' called at ');
        $positionInfo = strstr($positionInfo, '#2 ', true);
    }

    $positionInfo = trim(str_replace(array("\n", $separator), '', $positionInfo));
    $positionInfo = str_replace('\\', '/', $positionInfo);
    $root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $positionInfo = str_replace($root, '&lt;-ROOT-&gt;', $positionInfo);

    if ($return) {
        return $positionInfo;
    }

    if (!$__print_style_out_mark) {
        $__print_style_out_mark = true;

        echo ___output_css();//,loadJQuery()
    }

    echo "<div class=\"general-print-pos general-print-font\">本次打印调用位置：$positionInfo</div>\n";
}

function ___output_css()
{
    return <<<EOF
          <style>
    .general-print-shadow {
        box-shadow: 1px 1px 10px #E8E6E3 inset,-1px 1px 2px #BBB9A6;
        -moz-box-shadow: 1px 1px 10px #E8E6E3 inset,-1px 1px 2px #BBB9A6;
        -webkit-box-shadow: 1px 1px 10px #E8E6E3 inset,-1px 1px 2px #BBB9A6;
    }
    .general-print-pos { position: relative; padding: 5px 15px; border: 1px solid #C6C6D5; border-radius: 5px 5px 0 0; font-size: smaller; margin: 1rem 2rem 0; background-color: #3D300D; color: #EEE; }
    .general-print-font {font: 13px/1.5 Menlo, Monaco, Consolas, 'Courier New', monospace; }
    .general-print-box {overflow-x: auto; border: 1px solid #C6C6D5; border-top: none; display: block; line-height: 1.4; margin: 0 2rem; background-color: #FCF9F1;
        color: #316476; text-shadow: 0 1px 0 #F4F4F4; font-size: 100%; white-space: nowrap; text-align: left; }
    .general-print-box pre {margin: 0 !important; padding: 10px 12px; font-family: inherit; }
    </style>
EOF;

}