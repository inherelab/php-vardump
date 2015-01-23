<?php
/**
 * Created by PhpStorm.
 * @author Inhere
 * @Date: 14-11-15
 * @Time: 下午3:16
 * 能够使用静态方式访问 类的动态方法
 * @USE: extends ulue\core\utils\StaticInvokHelper;
 * StaticInvokHelper.php
 */

abstract class StaticInvokHelper
{
    static private $instanceContainer  = array();

    // 记录调用的方法名
    public $calledMethod;

    /**
     * 设置 类方法命名方式
     * @param string | number $defMethodType 1 2 3
     * 1 方法无前缀和后缀:
     *     但要将方法定义为 受保护的(protected) 或 私有(private)
     * 2 使用前缀:
     * @example  class Demo
     * setting: $methodPrefix = 'method';
     * function: public function methodTest(){}
     * invoke : Demo::test()
     * 3 使用后缀:
     * @example  class Demo
     * setting: $methodSuffix = 'Method';
     * function: public function testMethod(){}
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
     * @param  string $method [description]
     * @param  array $args [description]
     * @return mixed
     */
    static public function __callStatic($method, array $args)
    {
        $calledClassname = get_called_class();

        if ( !isset(self::$instanceContainer[$calledClassname]) ) {
            self::$instanceContainer[$calledClassname] = new $calledClassname();
        }

        $_this_                = self::$instanceContainer[$calledClassname];

        // return call_user_func_array(array($_this_,$method), $args );
        return call_user_func_array(array($_this_,'invoking'), array($method, $args) );

    }

    /**
     * 同样允许以常规方式调用
     * @param  string $method [description]
     * @param  array  $args [description]
     * @return mixed       [description]
     */
    public function __call($method, array $args)
    {
        $oldMethod  = $method;
        $method     = self::_checkUseType($method);

        if ( $this->_isAllowCall($method) && method_exists($this, $method)  ) {
            $this->calledMethod = $method;

            // return call_user_func_array(array($this,$method), $args);
            return call_user_func_array(array($this,'invoking'), array( $method, $args[0]));
        }
        else {
            \Trigger::error('error call! Class method [ '.get_class($this)."::$oldMethod() ] does not exist or not allow access!!");
        }
    }

    private function _isAllowCall($method)
    {
        $allowCall = $this->getAllowCall();

        // if ( $allowCall === '*' ) {
        //     return true;
        // }

        if ( empty($allowCall) || !is_array($allowCall)) {
            return false;
        }

        return in_array( $method,$allowCall ) ? true : false;
    }

    public function getAllowCall()
    {
        $allowCall = $this->allowInvokerCall();

        if ( empty($allowCall) ) {
            return null;
        }

        if ( is_array($allowCall) ) {
            return $allowCall;
        }

        if ( is_string($allowCall) ) {
            $allowCall = str_replace(' ', '', $allowCall);

            return strpos($allowCall,',')===false ? array($allowCall) : explode(',', $allowCall);
        }

        return null;

    }

    static private function _checkUseType($method)
    {
        $defMethodType = trim(static::$defMethodType);
        $method        = trim($method);

        switch ((string)$defMethodType) {
            case '2':{
                $methodPrefix = static::$methodPrefix;

                if (empty($methodPrefix) || !is_string($methodPrefix)) {
                    \Trigger::error('请设置方法前缀属性值：$methodPrefix ');
                }

                $methodName = $methodPrefix.ucfirst($method);
                break;
            }
            # code...
            case '3':{
                $methodSuffix = static::$methodSuffix;

                if (empty($methodSuffix) || !is_string($methodSuffix)) {
                    \Trigger::error('请设置方法后缀属性值：$methodSuffix ');
                }

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