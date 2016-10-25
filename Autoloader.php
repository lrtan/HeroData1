<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/25 0025
 * Time: 10:46
 * 自动加载类，基于命令空间,使用的时候要用addNamespace()方法指明空间地址
 *
 */
if (!defined('ROOT')){
    define('ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);
}
//echo ROOT;
Autoloader::start();
new \Lrtan\HeroData();
class Autoloader
{
    protected $arrayNamespaceDirList=array();
    private static $instance;
   public function register()
   {
       spl_autoload_register(array($this,'autoload'));
   }
    public function autoload($class)
    {
       // echo $class;
        /*$class有空间名和类名
           如：Lrtan\Core\abc
        如何 构成 一个完整的地址？
        $BaseDir.$class
        */
        //找到BaseDir
        //将$class以最后的\进行拆分为两个部分，$prefix=Lrtan\Core $postfix=abc
        $prefix=$class;
        while(false!==$offset=strrpos($prefix,'\\')){
            $prefix=substr($class,0,$offset);
            $postfix=substr($class,$offset+1);
            //$prefix='Lrtan\Core'
            //$postfix=abc
            $postfix=str_replace('\\',DIRECTORY_SEPARATOR,$postfix).'.php';
            $arrayBaseDirs=isset($this->arrayNamespaceDirList[$prefix])?$this->arrayNamespaceDirList[$prefix]:null;
            if (!empty($arrayBaseDirs)){
                foreach ($arrayBaseDirs as $BaseDir) {
                    $file=$BaseDir.$postfix;
                    if ($this->requireFile($file))
                    {
                        //echo '加载'.$file.'成功';
                        return true;
                    }
                }

            }
        }
    }

    /**
     * @param $namespace
     * @param $BaseDir
     */
    public function addNamespace($namespace, $BaseDir)
    {
       // $this->arrayNamespaceDirList[]=array($namespace=>$BaseDir);
        /*
        *不行
        *因为要考虑一个namespace在不同的地方，要以二维数组的形式存，如下
        *['namespace1'=>['aa','bb'],'namespace2'=>['aa2','bb2,'cc''],]
         */
        isset($this->arrayNamespaceDirList[$namespace])?:$this->arrayNamespaceDirList[$namespace]=array();
        //查找$BaseDir最后是否有/or\
        $BaseDir=rtrim($BaseDir,'\\');
        $BaseDir=rtrim($BaseDir,'/');
        $BaseDir.=DIRECTORY_SEPARATOR;
        array_push($this->arrayNamespaceDirList[$namespace],$BaseDir);
        //不检查是否有重复的地址。
    }
    public function requireFile($file)
    {
        //echo $file;
        if (is_file($file)){
            require($file);
            return true;
        }else{
            return false;
        }
    }

    private function __construct()
    {
        $this->register();
        $this->addNamespace('Lrtan\Core',ROOT.'class');
        $this->addNamespace('Lrtan',ROOT);
    }
    public static function start()
    {
        if (!is_object(self::$instance)){
            self::$instance=new self;
        }
        return self::$instance;
    }
}