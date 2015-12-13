<?php
/**
 * Created by sublime 3.
 * Auth: Inhere
 * Date: 15-1-20
 * Time: 10:35
 */

class PrintHelper
{
    /**
     * 清除标签并格式化数据
     * @param string $data
     * @return mixed|string
     */
    static public function clearTagAndFormat($data)
    {
        if (!$data || !is_string($data)) {
            return $data;
        }

        $data = strip_tags($data);
        $data = str_replace(
            array('&rArr;', '&gt;'),
            array('=>', '>'),
            $data
        );
        $data = preg_replace(
            array( "/[\n\r]+/i", "/Array[\s]*\(/","/=>[\s]+/i" ),
            array( "\n", 'Array (',"=> " ),
            $data
        );

        return $data;
    }

    static public function simpleFormat($data)
    {
        if (!$data || !is_string($data)) {
            return $data;
        }

        $data = preg_replace(
            array( "/[\n\r]+/i", "/Array[\s]*\(/","/=>[\s]+/i" ),
            array( "\n", 'Array (',"=> " ),
            $data
        );

        return $data;
    }

    /**
     * @param $data
     * @param bool $hasType
     * @return mixed
     */
    public static function getSystemPrintData($data,$hasType=true)
    {
        $fun = $hasType ? 'var_dump' : 'print_r';

        ob_start();
        $fun($data);
        $string     = ob_get_clean();

        if ( self::isWebRequest() && preg_match('/^<pre[\s]*/i', $string)!=1 ) {
            $string  = "<pre>$string</pre>";
        }

        return self::simpleFormat($string);
    }

    /**
     * 清除标签并格式化数据
     * @param string $data
     * @return mixed|string
     */
    public static function clearTagAndFormat($data)
    {
        if (!$data || !is_string($data)) {
            return $data;
        }

        $data = strip_tags($data);
        $data = str_replace(
            array('&rArr;', '&gt;'),
            array('=>', '>'),
            $data
        );
        $data = preg_replace(
            array( "/[\n\r]+/i", "/Array[\s]*\(/","/=>[\s]+/i" ),
            array( "\n", 'Array (',"=> " ),
            $data
        );

        return $data;
    }

    public static function simpleFormat($data)
    {
        if (!$data || !is_string($data)) {
            return $data;
        }

        $data = preg_replace(
            array( "/[\n\r]+/i", "/Array[\s]*\(/","/=>[\s]+/i" ),
            array( "\n", 'Array (',"=> " ),
            $data
        );

        return $data;
    }

    public static function versionCheck()
    {
        # code...
        $re = version_compare(PHP_VERSION, '5.4.0') >= 0;

        if (!$re) {
            exit('你的PHP版本是：'.PHP_VERSION.'；要求PHP>=5.4!');
        }
    }

    // 计算字符长度
    public static function strLength($str)
    {
        if ( $str==='0' || $str === 0 ) 
            return '1';

        if ( empty($str) ) 
            return '0';

        if (function_exists('mb_strlen')) {
            return mb_strlen($str,'utf-8');
        } else {
            preg_match_all("/./u", $str, $arr);
            return count($arr[0]);
        }
    }

    /**
     * getLines 获取文件一定范围内的内容]
     * @param  type ]  $fileName  含完整路径的文件]
     * @param  integer $startLine 开始行数 默认第1行]
     * @param  integer $endLine 结束行数 默认第50行]
     * @param  string $method 打开文件方式]
     * @throws Exception
     * @return array             返回内容
     */
    public static function getLines($fileName, $startLine = 1, $endLine = 50, $method = 'rb')
    {
        $content = array();

        // 判断php版本（因为要用到SplFileObject，PHP>=5.1.0）
        if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
            $count    = $endLine - $startLine;

            try{
                $obj_file = new \SplFileObject($fileName, $method);
                $obj_file->seek($startLine - 1); // 转到第N行, seek方法参数从0开始计数

                for ($i = 0; $i <= $count; ++$i) {
                    $content[] = $obj_file->current(); // current()获取当前行内容
                    $obj_file->next(); // 下一行
               }
            }catch(Exception $e)
            {
              throw new Exception("读取文件--{$fileName} 发生错误！");
            }

        } else { //PHP<5.1
            $openFile   = fopen($fileName, $method);

            if (!$openFile) {
                exit('error:can not read file--'.$fileName);
            }

            // 跳过前$startLine行
            for ($i = 1; $i < $startLine; ++$i) {
                fgets($openFile);
            }

            // 读取文件行内容
            for ($i; $i <= $endLine; ++$i) {
                $content[] = fgets($openFile);
            }

            fclose($openFile);
        }

        return array_filter($content); // array_filter过滤：false,null,''
    }

    // 命令模式
    public static function isCliMode()
    {
<<<<<<< HEAD:helpers/PrintHelper.php
       // return PHP_SAPI === 'cli' ? true : false;
       return php_sapi_name() === 'cli';
=======
      // return PHP_SAPI === 'cli' ? true : false;
      return php_sapi_name() === 'cli' ? true : false;
>>>>>>> 9d049e0376d45793f7ca5e7b7a0dea3b9c65f0fb:helpers/PrintHelper.php
    }

    // ajax 请求
    public static function isAjax()
    {
       return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }

    // flash 请求
    public static function isFlash()
    {
       return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && stripos($_SERVER['HTTP_X_REQUESTED_WITH'],'Shockwave')!==false;
    }

    public static function getIsFlash()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']: null;

        return $userAgent && (stripos($userAgent,'Shockwave')!==false || stripos($userAgent,'Flash')!==false);
    }

    // 是正常的网络请求 get post
    public static function isWebRequest()
    {
        return !self::isCliMode() && !self::isAjax() && !self::isFlash();
    }


    public static function varExport($var, $return=false, $length=200)
    {
        $string = var_export($var,true);

        if (is_object($var)) {
            $string = str_replace(array('::__set_state(',"=> \n"),array('(Object) ',"=>"),$string);
        }

        $string = trim($string);

        if ($return) {
            return strlen($string)>$length ? substr($string, 0,$length).'...' : $string;
        } else {
            echo $string;
        }

        return '';
    }
}