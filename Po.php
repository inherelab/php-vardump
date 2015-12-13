<?php
/**
 * @link https://github.com/inhere/simple-print-tool.git
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 14-11-26
 * Time: 10:35
 * Des :  General Print output test -- Po 常用打印输出测试
 * ********************** 常用输出测试方法 **********************
 * @param mixed $inputData
 * Use : 是 √ 否 X
 * +--------------------------------------------------------------------------------------+
 * |     -----        | 是否可打印   | 打印时是否  | 打印后是否   |      补充说明            |
 * |  (方法)函数使用   |  多个参数    |  类型输出   | 会退出程序   |                         |
 * |------------------+-------------+------------+-------------+-------------------------|
 * | d() / Po::d()    |       √     |     √      |      X      |                         |
 * |------------------|-------- ----|------------|-------------|                         |
 * | de() / Po::de()  |       √     |     √      |      √      |                         |
 * |------------------|-------- ----|------------|-------------|                         |
 * | p() / Po::p()    |       √     |     X      |      X      |                         |
 * |------------------|-------- ----|------------|-------------|                         |
 * | pe() / Po::pe()  |       √     |     X      |      √      |                         |
 * |------------------+-------- ----+------------+-------------+-------------------------|
 * | pr() / Po::pr()  |       √     |     X      |      X      |  pr()等同于print_r(),    |
 * |------------------|-------- ----|------------|-------------|  但可以传入多个参数       |
 * | vd() / Po::vd()  |       √     |     √      |      X      |  vd()等同于var_dump()    |
 * +--------------------------------------------------------------------------------------+
 * 若使用了命名空间 类方法调用 需在最前加上'\'。 @example \Po::d($arg1,$arg2,$arg3,...);
 **/

include __DIR__.'/helpers/PrintHelper.php';
include __DIR__.'/helpers/StaticInvokeHelper.php';

class Po extends StaticInvokeHelper
{
    static private $instance      = null;

    static private $hasStyle      = false; # 标记样式是否已经输出

    /**
     * 控制数组内容显示隐藏的  class name
     * @var string
     */
    static private $controlClass   = 'js-control-showOrHide';

    static private $jqueryLoc   = '/static/dep/jquery.js';

    static private $jqueryCdn   = 'http://libs.useso.com/js/jquery/2.1.0/jquery.min.js';

    /**
     * $disabled 禁用输出，设置后将不会打印数据。
     * use: 在打印前调用 Po::disabled(); 页面将不会有任何打印数据输出
     * @var boolean
     */
    static private $disabled      = false;

    /**
     * $hidden 是否展开打印数据，默认展开
     * use: 在打印前调用 @see Po::hidden() 可默认收缩隐藏打印数据
     */
    static private $hidden        = false;

    /**
     * $detectAjax 开启侦测Ajax请求 @todo 未完善
     * use: 在打印前调用@see Po::detectAjax()
     * @var boolean
     */
    static private $detectAjax    = false;

    /**
     * @see Po::stripTags()
     * 是否去除html标签。当 ajax请求 或是 cli(命令环境) 时，无需设置也会自动去除
     * @var bool
     */
    static private $stripTags     = false;


    // 标记打印调用后是否退出程序
    private $exit    = false;

    // 退出关键字
    // 当打印函数默认不退出，想让其打印后退出时设置 最后一个参数 === $exitKey
    public $exitKey = -4;

    // 不退出，跳出关键字
    // 当打印函数默认退出，想让其打印后不退出(继续执行后续程序)时设置 最后一个参数 === $exitKey
    public $skipKey = -5;

    public $inputData; // 输入数据 TODO unused

    /**
     * 打印位置信息数据
     * @var string
     */
    public $positionData;

    /**
     * 输出数据
     * @var string
     */
    public $outputData;

//    public $numberArg; // 参数个数
//    public $lastArg;   // 传入的最后一个参数

    /**
     * 设置项目根路径，用于打印时安全替换
     * 网络请求时，可以不用设置 会默认设置为 $_SERVER['DOCUMENT_ROOT']
     * 当在 命令行 环境时，需要定义 PROJECT_PATH 来设置 $rootPath
     *
     * @example
     * $rootPath = null; 为空时输出
     *     p() called at F:\xxx\yyy\test.php:34
     * 设置 $rootPath = 'F:\xxx\yyy'; 后
     *     p() called at <ROOT>\test.php:34
     *
     * @var string
     */
    public $rootPath;

    /**
     * 传入进来的变量的名称
     * @var array
     */
    protected $varNames = [];

    /**
     * 在 $GLOBALS 中搜索变量名时要先排除的信息
     * @var array
     */
    private $exceptVars = array('GLOBALS'=>0,'_ENV'=>0,'HTTP_ENV_VARS'=>0,'ALLUSERSPROFILE'=>0,
        'CommonProgramFiles'=>0,'COMPUTERNAME'=>0,'ComSpec'=>0,'FP_NO_HOST_CHECK'=>0,'NUMBER_OF_PROCESSORS'=>0,
        'OS'=>0,'Path'=>0,'PATHEXT'=>0,'PROCESSOR_ARCHITECTURE'=>0,'PROCESSOR_IDENTIFIER'=>0,
        'PROCESSOR_LEVEL'=>0,'PROCESSOR_REVISION'=>0,'ProgramFiles'=>0,'SystemDrive'=>0,'SystemRoot'=>0,
        'TEMP'=>0,'TMP'=>0,'USERPROFILE'=>0,'VBOX_INSTALL_PATH'=>0,'windir'=>0,'AP_PARENT_PID'=>0,
        'uchome_loginuser'=>0,'supe_cookietime'=>0,'supe_auth'=>0,'Mwp6_lastvisit'=>0,
        'Mwp6_home_readfeed'=>0,'Mwp6_smile'=>0,'Mwp6_onlineindex'=>0,'Mwp6_sid'=>0,'Mwp6_lastact'=>0,
        'PHPSESSID'=>0,'HTTP_ACCEPT'=>0,'HTTP_REFERER'=>0,'HTTP_ACCEPT_LANGUAGE'=>0,'HTTP_USER_AGENT'=>0,
        'HTTP_ACCEPT_ENCODING'=>0,'HTTP_HOST'=>0,'HTTP_CONNECTION'=>0,'HTTP_COOKIE'=>0,
        'PATH'=>0,'COMSPEC'=>0,'WINDIR'=>0,'SERVER_SIGNATURE'=>0,'SERVER_SOFTWARE'=>0,'SERVER_NAME'=>0,
        'SERVER_ADDR'=>0,'SERVER_PORT'=>0,'REMOTE_ADDR'=>0,'DOCUMENT_ROOT'=>0,'SERVER_ADMIN'=>0,
        'SCRIPT_FILENAME'=>0,'REMOTE_PORT'=>0,'GATEWAY_INTERFACE'=>0,'SERVER_PROTOCOL'=>0,
        'REQUEST_METHOD'=>0,'QUERY_STRING'=>0,'REQUEST_URI'=>0,'SCRIPT_NAME'=>0,'PHP_SELF'=>0,
        'REQUEST_TIME'=>0,'argv'=>0,'argc'=>0,'_POST'=>0,'HTTP_POST_VARS'=>0,'_GET'=>0,'HTTP_GET_VARS'=>0,
        '_COOKIE'=>0,'HTTP_COOKIE_VARS'=>0,'_SERVER'=>0,'HTTP_SERVER_VARS'=>0,
        '_FILES'=>0,'HTTP_POST_FILES'=>0,'_REQUEST'=>0
    );

    public function __construct()
    {
        if ( defined('PROJECT_PATH')) {
            $this->rootPath = str_replace('\\', '/', PROJECT_PATH);
        } else {
            $this->rootPath = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        }
    }

    static public function config(array $config)
    {
        # code...
    }

    static public function owner()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @extends
     * 允许静态调用的方法设置 @example Po::p() --> protected function p()
     * @return array|string [type] [description]
     */
    protected function allowInvokerCall()
    {
        return array('d','p','de','pe','vd','pr','uc');
    }

    /**
     * invoking 函数调用者]
     * @param  string $methodName   要调用的方法名]
     * @param  array  $data         数据]
     * @return void
     */
    public function invoking($methodName,array $data)
    {
        if ( !method_exists($this, $methodName) ) {
            self::quit('The Class <b>'.get_class($this)."</b> don't has method $methodName() !");
        }

        // 提取变量名称
        $allVar = array_diff_key($GLOBALS, $this->exceptVars);
        foreach($allVar as $key => $val){
            $this->varNames[$key] = $val;
        }

        $positionData = $this->calledPosition()->positionData;

        $this->$methodName($data);

        $outputData = $this->outputData;

        /** ajax 请求 且开启 ajax 检测 @TODO 未完善 */
        if (self::$detectAjax && PrintHelper::isAjax()) {
            $output     = array(
                'position' => str_replace(PHP_EOL, '', $positionData),
                'content'  => PrintHelper::clearTagAndFormat($outputData)
            );

            $outputData = json_encode($output).',';

        // 非 web 请求， CLI 命令行环境
        } elseif ( !PrintHelper::isWebRequest() ) {
            $outputData = PrintHelper::clearTagAndFormat( $outputData );
        }

        if ( self::$stripTags ) {
            $outputData = PrintHelper::clearTagAndFormat( $positionData.$outputData );
        } else {
            $outputData = $positionData.$outputData;
        }

        if ( !PrintHelper::isWebRequest() ) {
            $outputData .= "\n<<<<<< $methodName() print out end ......\n";
        }

        self::quit($outputData,$this->exit);
    }

//////////////////////////////// 打印输出设置 ////////////////////////////////

    /**
     * 隐藏输出内容，只剩下工具条
     * @param bool $value
     */
    static public function hidden($value=true)
    {
        self::$hidden = (bool)$value;
    }

    /**
     * 禁用输出
     * @param bool $value
     */
    static public function disabled($value=true)
    {
        self::$disabled = (bool)$value;
    }

    /**
     * 去除html标记
     * @param bool $value
     */
    static public function stripTags($value=true)
    {
        self::$stripTags = (bool)$value;
    }


    /**
     * 开启侦测Ajax请求, 需在加载页面时开启(而不是在Ajax请求时调用开启)
     * @todo 未完善
     */
    static public function detectAjax($value=true)
    {
        if ($value==='end' && PrintHelper::isAjax() ) {
            self::quit();
        } else {
            if (self::$detectAjax = (bool)$value) {
                echo self::_scriptTag();
            }
        }
    }

//////////////////////////////// 可用方法 ////////////////////////////////


    /**
     * vd === var_dump
     * @param $param
     * @param string $outString
     */
    protected function vd($param,$outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value,true,true);
        }

        if ($last === $this->exitKey) {
            $this->exit = true;
        } else {
            $outString .= $this->dump($last,true,true);
        }

        $this->outputData = $outString;
    }

    /**
     * pr === print_r 但支持传入多个参数
     * @param $param
     * @param string $outString
     */
    protected function pr($param,$outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value,false,true);
        }

        if ($last === $this->exitKey) {
            $this->exit = true;
        } else {
            $outString .= $this->dump($last,false,true);
        }

        $this->outputData = $outString;
    }

    ## 打印输出，不含类型

    /**
     * 多个打印
     * @param $param
     * @param string $outString
     * @return void
     */
    protected function p($param,$outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value,false);
        }

        if ($last === $this->exitKey) {
            $this->exit   = true;
        } else {
            $outString .= $this->dump($last,false);
        }

        $this->outputData = $outString;
    }

    /**
     * 多个打印,会退出
     * @param $param
     * @param string $outString
     */
    protected function pe($param,$outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value,false);
        }

        $this->exit       = true;

        if ($last === $this->skipKey) {
            $this->exit  = false;
        } else {
            $outString .= $this->dump($last,false);
        }

        $this->outputData = $outString;
    }

    ## 打印输出，含数据类型

    /**
     * 打印一个或者多个参数： 可以传入多个参数；最后一个若为 _4 则退出程序
     * d($arg1,$arg2,$arg3,...)
     * @param $param
     * @param string $outString
     */
    protected function d($param,$outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value);
        }

        if ($last === $this->exitKey) {
            $this->exit   = true;
        } else {
            $outString .= $this->dump($last);
        }

        $this->outputData = $outString;
    }

    /**
     * 同d(),但是打印后会立即退出程序；最后一个若为 _5 则放弃退出
     * @param $param
     * @param string $outString
     */
    protected function de($param,$outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value);
        }

        $this->exit = true;

        if ($last === $this->skipKey) {
            $this->exit   = false;
        } else {
            $outString .= $this->dump($last);
        }

        $this->outputData = $outString;
    }

    /**
     * 默认打印数据保存文件
     * @var string
     */
    private static $tempFile = 'dump.txt';

    /**
     * 设置保存输出数据到文件
     * @var boolean
     */
    private static $saveToFile    = false;

    /**
     * 保存输出数据到文件设置
     * true 追加内容
     * false 覆盖内容
     * @var boolean
     */
    private static $appendData = true;

    protected function log()
    {
        $args = func_get_args();
        $str = PHP_EOL;

        foreach ($args as $arg) {
            $str .= PrintHelper::getSystemPrintData($arg,true);
        }

        error_log($str, 3, self::$tempFile);
    }

    /**
     * 打印用户定义常量 user constant
     * @internal param bool $return description]
     * @return void
     */
    protected function uc()
    {
        $const = get_defined_constants(true);

        if (!isset($const['user'])) {
            $this->outputData = $this->dump( null );
        } else {
            $this->outputData = $this->dump( $const['user'] );
        }
    }

    private function _handle($param, $hasType=true, $useSystemPrint=false, $outString= '')
    {
        $last = array_pop($param);

        foreach ($param as $value) {
            $outString .= $this->dump($value, $hasType, $useSystemPrint);
        }

        if ($last === '_4') {
            $this->exit   = true;
        } else {
            $outString .= $this->dump($last, $hasType, $useSystemPrint);
        }

        $this->outputData = $outString;
    }

//////////////////////////////// 输出数据解析 ////////////////////////////////


    /**
     * 格式化打印数组，含类型 长度 ==var_dump
     * @param mixed $data
     * @param bool | int $hasType 输出类型
     * @param bool | int $useSystemPrint 使用系统函数打印
     * @return string
     * @internal param bool|int $exit
     */
    private function dump($data, $hasType=true, $useSystemPrint=false)
    {
        $style = '';

        if ( self::$hidden ) {
            $style = ' style="display:none;"';
        }

        $outString = '%s';

        if ( PrintHelper::isWebRequest() ) {
            $outString = "<!-- output print start -->\n<div class=\"general-print-box general-print-font general-print-shadow\" $style>\n%s</div>\n<!-- output print end -->".PHP_EOL;
        }

        # 使用系统函数打印
        if ( $useSystemPrint ) {
            $result       = PrintHelper::getSystemPrintData($data,$hasType);
            $outString    = sprintf($outString,$result);
        }# 自定义函数
        else {
            $result       = self::_handleTypeOutput($data,$hasType);
            $outString    = sprintf($outString,$result);
        }

        unset($hasType,$data,$result);

        return $outString;
    }

    /**
     * 格式化打印，不含数据类型
     * @param $data
     * @param bool $mark
     * @param string $outString
     * @return string
     */
    static private function _handleNormalOutput($data, $mark=true, $outString='')
    {
        $html         = 'htmlspecialchars';
        $ucfirst      = 'ucfirst';
        $jsClass      = self::$controlClass;//' class="js-print-showOrHide"';
        $usualString  = "<dt><div class=\"array-value\">\n%s</div></dt>\n";
        $outString   .= '<dl>'.PHP_EOL;
        $dataType     = gettype($data);

        if ( is_array($data) ) {

            $count = 'count';
            $mark && $outString .= "<dt>".PHP_EOL."<div class=\"{$jsClass}\" style=\"width:98%\">
            <strong class=\"general-print-color-dg\">{$ucfirst($dataType)}</strong>(size:<strong>{$count($data)}</strong>)<strong>(</strong></div><span class=\"print-icon icon-hide\"></span>".PHP_EOL."</dt>".PHP_EOL;
            $outString .= '<!-- .general-print-ar-content -->'.PHP_EOL.'<dd class="general-print-ar-content">';

            foreach($data as $k => $v)
            {
                $k          = is_int($k) ? $k : "'{$html($k,ENT_QUOTES)}'";
                $vType      = gettype($v);
                $outString .= PHP_EOL."<dl>\n<dt><div class=\"array-key\">\t$k</div>";

                if ( is_array($v) || is_object($v) || is_resource($v))
                {
                    $outString .= "<div class=\"array-value {$jsClass}\"> &rArr; <strong class=\"general-print-color-dg\">{$ucfirst($vType)}</strong>(size:";

                    if (empty($v)) {
                        $outString .= "0)<strong>()</strong> </div><span class=\"print-icon icon-hide\"></span></dt>";
                        continue;
                    }

                    $outString .= "<strong>{$count((array)$v)}</strong>)<strong>(</strong></div><span class=\"print-icon icon-hide\"></span>\n</dt>".PHP_EOL;
                    $outString .= ltrim(self::_handleNormalOutput($v,false),'<dl>');
                } else {

                    if ($v === false) $v = 'bool(false)';
                    if ($v === true ) $v = 'bool(true)';
                    if ($v === null ) $v = 'null(null)';
                    if ($v === ''   ) $v = '""';

                    $outString .= "<div class=\"array-value\"> &rArr; <span class=\"general-print-color-r\">{$v}</span> </div></dt>\n</dl>";
                }

            }//--endforeach--

            $outString .= PHP_EOL.'</dd><!-- /.general-print-ar-content -->'.PHP_EOL.'</dl>'.PHP_EOL.'<dl><dt><strong>)</strong></dt>';
        } else if ( is_object($data) ) {
            $outString .= PrintHelper::getSystemPrintData($data,0);
        } else if (is_resource($data)) {
            if ( ( $type = get_resource_type( $data ) ) === 'stream' and $meta = stream_get_meta_data( $data ) ) {

                if ( isset( $meta['uri'] ) ) {
                    $file = $meta['uri'];
                    $resourceString = "resource ({$type}) {$html( $file, ENT_QUOTES )}";
                } else {
                    $resourceString = "resource ({$type})";
                }

            } else {
                $resourceString = "resource ({$type})";
            }

            $outString .= sprintf($usualString,$resourceString);
        } else {

            if ($data === false) $data = 'bool(false)';
            if ($data === null ) $data = 'null(null)';
            if ($data === true)  $data = 'bool(true)';
            if ($data === ''   ) $data = '""';

            $outString .= '<span class=\"general-print-color-r\">'.sprintf($usualString,$data).'</span>';
        }

        return $outString.'</dl>'.PHP_EOL;
    }

    /**
     * 格式化打印，含数据类型
     * @param $data
     * @param bool $hasType
     * @param bool $mark
     * @internal string $tab 每递归一次，添加一次Tab缩进
     * @param string $outString
     * @internal param $type ] $o description]
     * @return string
     */
    static private function _handleTypeOutput($data,$hasType=true,$mark=true,$outString='')
    {
        # 常规打印，不含有类型
        if (!$hasType) {
            return self::_handleNormalOutput($data);
        }

        $jsClass      = self::$controlClass;
        $html         = 'htmlspecialchars';
        $ucfirst      = 'ucfirst';
        static $i      = 1;
        $usualString  = "<dt><div class=\"array-value\">\n%s</div></dt>\n";
        $outString   .= '<dl>'.PHP_EOL;
        $dataType     = gettype($data);

        // if (is_object($data)) $data = (array)$data;
        # 含类型打印
        if ( is_array($data) ) {
            $count = 'count';
            $mark && $outString .= "<dt>\n<span class=\"print-icon icon-hide\"></span><div class=\"{$jsClass}\" style=\"width:98%\">".
            "<strong class=\"general-print-color-dg\">{$ucfirst($dataType)}</strong>(size:<strong>{$count($data)}</strong>)<strong>(</strong></div>\n</dt>\n";
            $outString .= '<!-- .general-print-ar-content -->'.PHP_EOL.'<dd class="general-print-ar-content">';
            $tab    = self::_getTab($i);

            foreach($data as $k => $v)
            {
                $vType      = gettype($v);
                $k          = is_int($k) ? $k : "'{$html($k,ENT_QUOTES)}'";
                $outString .= "\n<dl>\n<dt><div class=\"array-key\">$tab$k</div>";

                if ( is_array($v) || is_object($v) || is_resource($data) ) {//
                    $outString .= "<div class=\"array-value {$jsClass}\"> &rArr; <strong class=\"general-print-color-dg\">{$ucfirst($vType)}</strong>(size:";

                    if (empty($v)) {
                        $outString .= "0)<strong>()</strong> </div><span class=\"print-icon icon-hide\"></span></dt>";
                        continue;
                    }
                    $i++;

                    $outString .= "<strong>{$count((array)$v)}</strong>)<strong>(</strong></div>\n<span class=\"print-icon icon-hide\"></span></dt>\n";
                    $outString .= ltrim(self::_handleTypeOutput($v,true,false),'<dl>');
                } else {
                    $length_html = '';

                    if ( $v === null )   {
                        $v = '(null)' ;
                    } else if ($v === false) {
                        $v = '(false)';
                    } else if ($v === true ) {
                        $v = '(true)' ;
                    } else {
                        $length       = PrintHelper::strLength($v);
                        $length_html  = "(<span class=\"general-print-color-g\">length:</span>{$length})";
                        $vType == 'string' && $v = "\"{$html($v,ENT_QUOTES)}\"";
                    }

                    $outString .= "<div class=\"array-value\"> &rArr; {$vType} <span class=\"general-print-color-r\">{$v}</span> {$length_html} </div></dt>".PHP_EOL."</dl>";
                }
            }//--endforeach--

            $endTab = substr($tab,0,-($i-1));
            $outString .= "\n</dd><!-- /.general-print-ar-content -->\n</dl>\n<dl><dt><strong>$endTab)</strong></dt>";
        } else if ( is_object($data) ) {
            $outString .= PrintHelper::getSystemPrintData($data);
        } else if (is_resource($data)) {
            if ( ( $dataType = get_resource_type( $data ) ) === 'stream' and $meta = stream_get_meta_data( $data ) ) {

                if ( isset( $meta['uri'] ) ) {
                    $file = $meta['uri'];
                    $resourceString = "Resource ({$dataType}) {$html( $file, ENT_QUOTES)}";
                } else {
                    $resourceString = "Resource ({$dataType})";
                }

            } else {
                $resourceString = "Resource ({$dataType})";
            }

            $outString .= sprintf($usualString,$resourceString);
        } else {
            $length = null;

            if ($data === false) $data = 'false';
            else if ($data === true)  $data = 'true';
            else if ($data === null)  $data = 'null';
            else $length = PrintHelper::strLength($data);// float integer string

            $dataType == 'string' && $data = "\"{$html($data,ENT_QUOTES)}\"";

            if ($length === null)
                $lengthString = '';
            else
                $lengthString = "(<span class=\"general-print-color-g\">length:</span>{$length})";

            $outString .= sprintf($usualString,"{$dataType} <span class=\"general-print-color-r\">{$data}</span> {$lengthString}");
        }

        return $outString.'</dl>'.PHP_EOL;
    }


//////////////////////////////// 辅助函数 ////////////////////////////////

    // 得到函数的调用位置，以免调用太多，找不到调用打印的地方
    public function calledPosition($backNum=6,$separator='#5')
    {
        if (!headers_sent())
            @header('Content-Type: text/html; charset=UTF-8');

        if (self::$disabled) {
            return '';
        }

        ob_start();
        if ( $phpGt54 = version_compare(PHP_VERSION, '5.4.0', '>=')) {
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,$backNum);
        } else {
            debug_print_backtrace(false);
        }

        $positionInfo = ob_get_clean();
        $positionInfo = strstr($positionInfo, $separator);

        if ( !$phpGt54 ) {
            $positionInfo = strstr($positionInfo, ' called at ');
            $positionInfo = strstr($positionInfo, "]\n#",true) . ']';
        }

        $positionInfo = trim(str_replace(array("\n",$separator), '', $positionInfo));
        $positionInfo = str_replace('\\', '/', $positionInfo);

        # ajax cli flash
        if (!PrintHelper::isWebRequest() || self::$stripTags ) {
            $positionInfo       = str_replace($this->rootPath, '<ROOT>', $positionInfo);
            $this->positionData = "\n>>>>>> The method $positionInfo\n\n";

            return $this;
        }

        $positionInfo = str_replace($this->rootPath, '&lt;ROOT&gt;', $positionInfo);
        $positionData = '';

        # 加载样式和jQuery。 TODO: 同一个页面只加载一次样式和jQuery
        if (!self::$hasStyle) {
            $positionData      .= self::_styleTag().PHP_EOL.self::_scriptTag();
            self::$hasStyle     =true;
        }

        #
        $tips          = !static::$hidden ? '' : '本次打印数据已隐藏,请点击右侧开关按钮显示数据。';
        $positionData .= <<<EOF
<div class="general-print-pos general-print-font">
  <p class="js-general-pos-info general-pos-info" style="display:inline-block;">本次打印调用位置：$positionInfo <span class="general-print-tips">$tips</span></p>
  <span class="general-print-help">?</span>
  <span class="general-print-code">&equiv;</span>
  <span class="general-print-switch js-general-print-switch">&otimes;</span>
</div>
EOF;
        $this->positionData = $positionData;

        return $this;
    }

    static private function _getTab($n)
    {
        $tab = "\t";
        if($n==1){
            return $tab;
        } else {
            for($i=1;$i<$n;$i++) {
                $tab .="\t";
            }

            return $tab;
        }
    }

    static private function _styleTag()
    {
        $css = file_get_contents(__DIR__.'/static/po.css');
        $css = preg_replace('/\s\s+/',' ',$css);
        $styleTag = "<!-- PRINT_OUTPUT_STYLE -->\n<style type='text/css'>%s</style>\n<!-- PRINT_OUTPUT_STYLE -->";

        return sprintf($styleTag,$css);
    }

    static private function _scriptTag()
    {
        $find    = array(
            '__reallyDetectAjax__', '__controlClass__'
        );
        $replace = array(
            'false', static::$controlClass
        );

        $jsCode = file_get_contents(__DIR__.'/static/po.js');

        if (self::$detectAjax == true) {
            $replace = array(
                'true', static::$controlClass
            );
        }

        $jsCode = str_replace($find, $replace, trim($jsCode));

        $jsTag = "%s<script type=\"text/javascript\">\n" .'%s ' ."\n</script>\n<!--PRINT_OUTPUT_SCRIPT-->\n";

        return sprintf($jsTag, self::_jqueryLoad() ,$jsCode);
    }

    public static function _jqueryLoad()
    {
        $jqueryCdn = self::$jqueryCdn;
        $jqueryLoc = self::$jqueryLoc;

        return <<<EOF
<!--PRINT_OUTPUT_SCRIPT-->
<script type="text/javascript">
  !window.jQuery && document.write('<script src="$jqueryCdn"><\/script>');
 </script>
<script type="text/javascript">
  !window.jQuery && document.write('<script src="$jqueryLoc"><\/script>');
 </script>
EOF;
    }

    public static function quit($msg='', $exit=true)
    {
        if ($exit) {
            exit($msg);
        } else {
            echo $msg;
        }
    }

}// class end

