<?php
/**
 * Created by PhpStorm.
 * @author Inhere
 * @Date: 14-11-15
 * @Time: 下午3:16
 * 能够使用静态方式访问 类的动态方法
 * @USE: extends ulue\core\utils\StaticInvokeHelper;
 * StaticInvokeHelper.php
 */

abstract class StaticInvokeHelper
{
    static private $objContainer  = array();

    // 记录调用的方法名
    public $calledMethod;

    /**
     * 设置 类方法命名方式
     * @param string | number $defMethodType 1 2 3
     * 1 方法无前缀和后缀:
     *     但要将方法定义为 受保护的(protected) 或 私有(private)
     * 2 使用前缀:
     * @example
     * class Demo {
     *      static public $methodPrefix = 'prefix';
     *      public function prefixTest(){ ... }
     * }
     * invoke : Demo::test()
     *
     * 3 使用后缀:
     * @example
     * class Demo {
     *      static public $methodSuffix = 'Suffix';
     *      public function testSuffix(){ ... }
     * }
     * invoke : Demo::test()
     */
    static public $defMethodType = '1';
    static public $methodPrefix  = '';
    static public $methodSuffix  = '';

    /**
     * allowInvokerCall
     * @example ['test', 'method1', 'method2'] | 'test, method1, method2'
     * @return string | array
     */
    abstract protected function allowInvokerCall();

    /**
     * __callStatic 能够使用静态方式访问 类的动态方法]
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public static function __callStatic($method, array $args)
    {
        $calledClassName = get_called_class();

        if ( !isset(self::$objContainer[$calledClassName]) )
            self::$objContainer[$calledClassName] = new $calledClassName();

        $self  = self::$objContainer[$calledClassName];

        // return call_user_func_array(array($_this_,$method), $args );
        return call_user_func_array(array($self,'invoking'), array($method, $args) );
    }

    /**
     * 同样允许以常规方式调用
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, array $args)
    {
        $oldMethod  = $method;
        $method     = self::_checkUseType($method);

        if ( $this->_isAllowCall($method) && method_exists($this, $method)  ) {
            $this->calledMethod = $method;

            // return call_user_func_array(array($this,$method), $args);
            return call_user_func_array(array($this,'invoking'), array( $method, $args[0]));
        } else 
            trigger_error('error call! Class method [ '.get_class($this)."::$oldMethod() ] does not exist or not allow access!!",E_USER_ERROR);

        return false;
    }

    private function _isAllowCall($method)
    {
        $allowCall = $this->getAllowCall();

        // if ( $allowCall === '*' ) {
        //     return true;
        // }

        if ( empty($allowCall) || !is_array($allowCall)) 
            return false;

        return in_array( $method,$allowCall ) ? true : false;
    }

    public function getAllowCall()
    {
        $allowCall = $this->allowInvokerCall();

        if ( empty($allowCall) ) 
            return null;

        if ( is_array($allowCall) ) 
            return $allowCall;

        if ( is_string($allowCall) ) {
            $allowCall = str_replace(' ', '', $allowCall);

            return strpos($allowCall,',')===false ? array($allowCall) : explode(',', $allowCall);
        }

        return null;
    }

    private static function _checkUseType($method)
    {
        $defMethodType = trim(static::$defMethodType);
        $method        = trim($method);

        switch ((string)$defMethodType) {
            case '2':{
                $methodPrefix = static::$methodPrefix;

                if (empty($methodPrefix) || !is_string($methodPrefix))
                    trigger_error('请设置方法前缀属性值：$methodPrefix ',E_USER_ERROR);

                $methodName = $methodPrefix.ucfirst($method);
                break;
            }
            # code...
            case '3':{
                $methodSuffix = static::$methodSuffix;

                if (empty($methodSuffix) || !is_string($methodSuffix))
                    trigger_error('请设置方法后缀属性值：$methodSuffix ',E_USER_ERROR);

                $methodName = $method.ucfirst($methodSuffix);
                break;
            }
            default://static::$useType==1
                $methodName = $method;
                break;
        }

        return $methodName;
    }

}