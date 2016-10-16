#PHP 基本函数

### dirname(__FILE__)

* __FILE__ 这个内定常数是当前php程序的就是完整路径（路径+文件名）。

* 即使这个文件被其他文件引用(include或require)，__file__始终是它所在文件的完整路径，而不是引用它的那个文件完整路径。

```
请看下面例子：
/home/data/demo/test/a.php

<?php
$the_full_name=__FILE__;
$the_dir=dirname(__FILE__);
echo $the_full_name; //返回/home/data/demo/test/a.php
echo $the_dir;            //返回/home/data/demo/test
?> 


home/data/demo/b.php
<?php include "test/a.php";
echo $the_full_name; //返回/home/data/demo/
echo $the_dir;            //返回/home/data/demo/test 而不是/home/data/demo/
?>test/a.php 而不是/home/data/demo/b.php 


简单地说：
      __FILE__     返回当前 路径+文件名
      dirname(__FILE__) 返回当前文件路径的 路径部分
      dirname(dirname(__FILE__));得到的是文件上一层目录名（不含最后一个“/”号）

例如，当前文件是 /home/data/demo/test.php ，则
__FILE__ 得到的就是完整路径       即 /home/data/demo/test.php ，而
dirname(__FILE__)得到路径部分   即 /home/data/demo     （后面没有“/”号）

```

###DIRECTORY_SEPARATOR

* 目录分隔符，是定义php的内置常量。在调试机器上，在windows我们习惯性的使用“\”作为文件分隔符，但是在linux上系统不认识这个标识，于是就要引入这个php内置常量了：DIRECTORY_SEPARATOR

* DIRECTORY_SEPARATOR是一个返回跟操作系统相关的路径分隔符的php内置命令，在windows上返回\，而在linux或者类unix上返回/，就是这么个区别，通常在定义包含文件路径或者上传保存目录的时候会用到。

```
define('ROOT',dirname(__FILE__)."\upload") //在linux下出错

define('ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR."upload"); //win and linux 正确


```
###闭包函数

* 如果要用一句话说明白闭包函数，那就是：函数内在包含子函数，并最终return子函数。

* 而闭包函数的最大价值在于：我们可以在函数的外部（即子函数），直接读取该函数的局部变量。

* 再仔细研究，就会发现f1()函数就如同一个“类”，而其定义的局部变量就如同该“类”的全局变量；而子函数f2()函数，则如同这个“类”的方法，可以直接使用这个“类”的全局变量n。神奇吧？

#### 闭包函数用途

* 缓存：最显而易见的好处，就是可以实现数据缓存，我们可以把一个需要长期用到的变量设为闭包函数的局部变量，在子函数里面直接使用它。因此局部变量只定义初始化一次，但我们可以多次调用子函数并使用该变量。这比起我们在子函数中定义初始化变量，多次调用则多次初始化的做法，效率更高。闭包函数常见的一种用途就是，我们可以通过此实现计数功能。在闭包函数定义一个计数变量，而在子函数中对其进行++的操作。这样每次调用闭包函数，计数变量就会加1。

```
function f1( ... )
    local n = 0
    function f2( ... )
        n = n + 1
        return n
    end
    return f2
end

local count = f1()
print(count())        --1
print(count())        --2
print(count())        --3
print(count())        --4
print(count())        --5

```
* 实现封装：如同前面所说，闭包函数就如同一个“类”，只有在该闭包函数里的方法才可以使用其局部变量，闭包函数之外的方法是不能读取其局部变量的。这就实现了面向对象的封装性，更安全更可靠。

### declare(ticks=N)
	
* 例如N=1;Zend引擎每执行1条低级语句就去执行一次 register_tick_function() 注册的函数。
可以粗略的理解为每执行一句php代码（例如:$num=1;）就去执行下已经注册的tick函数。
一个用途就是控制某段代码执行时间，例如下面的代码虽然最后有个死循环，但是执行时间不会超过5秒。
运行 php timeout.php

```
<?php
declare(ticks=1);

// 开始时间
$time_start = time();

// 检查是否已经超时
function check_timeout(){
    // 开始时间
    global $time_start;
    // 5秒超时
    $timeout = 5;
    if(time()-$time_start > $timeout){
        exit("超时{$timeout}秒\n");
    }
}

// Zend引擎每执行一次低级语句就执行一下check_timeout
register_tick_function('check_timeout');

// 模拟一段耗时的业务逻辑
while(1){
   $num = 1;
}

// 模拟一段耗时的业务逻辑，虽然是死循环，但是执行时间不会超过$timeout=5秒
while(1){
   $num = 1;
}

```

* declare(ticks=1);每执行一次低级语句会检查一次该进程是否有未处理过的信号,测试代码如下：
运行 php signal.php
然后CTL+c 或者 kill -SIGINT PID 会导致运行代码跳出死循环去运行pcntl_signal注册的函数，效果就是脚本exit打印“Get signal SIGINT and exi”退出

```
<?php
declare(ticks=1);
pcntl_signal(SIGINT, function(){
   exit("Get signal SIGINT and exit\n");
});

echo "Ctl + c or run cmd : kill -SIGINT " . posix_getpid(). "\n" ;

while(1){
  $num = 1;
}

```


### 匿名函数(闭包函数)

* 定义：匿名函数（Anonymous functions），也叫闭包函数（closures），允许 临时创建一个没有指定名称的函数。最经常用作回调函数（callback）参数的值。当然，也有其它应用的情况。 
```
<?php
 echo  preg_replace_callback ( '~-([a-z])~' , function ( $match ) {
    return  strtoupper ( $match [ 1 ]);
},  'hello-world' );
 // 输出 helloWorld
 ?> 

```

* 闭包函数也可以作为变量的值来使用。PHP 会自动把此种表达式转换成内置类 Closure 的对象实例。把一个 closure 对象赋值给一个变量的方式与普通变量赋值的语法是一样的，最后也要加上分号： 

```
<?php
$greet  = function( $name )
{
     printf ( "Hello %s\r\n" ,  $name );
};

 $greet ( 'World' );
 $greet ( 'PHP' );
?> 

```

* Closure 对象也会从父作用域中继承类属性。这些变量都必须在函数或类的头部声明。从父作用域中继承变量与使用全局变量是不同的。全局变量存在于一个全局的范围，无论当前在执行的是哪个函数。而 closure 的父作用域则是声明该 closure 的函数（不一定要是它被调用的函数）。示例如下： 

```
<?php
 // 一个基本的购物车，包括一些已经添加的商品和每种商品的数量。
// 其中有一个方法用来计算购物车中所有商品的总价格，该方法使
// 用了一个 closure 作为回调函数。
 class  Cart
 {
    const  PRICE_BUTTER   =  1.00 ;
    const  PRICE_MILK     =  3.00 ;
    const  PRICE_EGGS     =  6.95 ;

    protected    $products  = array();
    
    public function  add ( $product ,  $quantity )
    {
         $this -> products [ $product ] =  $quantity ;
    }
    
    public function  getQuantity ( $product )
    {
        return isset( $this -> products [ $product ]) ?  $this -> products [ $product ] :
                FALSE ;
    }
    
    public function  getTotal ( $tax )
    {
         $total  =  0.00 ;
        
         $callback  =
            function ( $quantity ,  $product ) use ( $tax , & $total )
            {
                 $pricePerItem  =  constant ( __CLASS__  .  "::PRICE_"  .
                     strtoupper ( $product ));
                 $total  += ( $pricePerItem  *  $quantity ) * ( $tax  +  1.0 );
            };
        
         array_walk ( $this -> products ,  $callback );
        return  round ( $total ,  2 );;
    }
}

 $my_cart  = new  Cart ;

 // 往购物车里添加条目
 $my_cart -> add ( 'butter' ,  1 );
 $my_cart -> add ( 'milk' ,  3 );
 $my_cart -> add ( 'eggs' ,  6 );

 // 打出出总价格，其中有 5% 的销售税.
 print  $my_cart -> getTotal ( 0.05 ) .  "\n" ;
 // 最后结果是 54.29
 ?> 

```

> 关于作用域 需要加深理解! 


### PSR标准

>更多是在项目中进行高效合理的自动加载,composer如同一个利器,利用psr规范合理的进行自动加载

* 在Composer中,遵循psr-0目录结构如下

```
vendor/
    vendor_name/
        package_name/
            src/
                vendor_name/
                    package_name/
                        ClassName.php     # vendor_name\package_name\ClassName
            tests/
                vendor_name/
                    package_name/
                        ClassNameTest.php     # vendor_name\package_name\ClassNameTest            

```

> 上面的目录已经很深了,src和tests包含了公共模块

* 在Composer中,遵循psr-4目录结构如下

```
vendor/
    vendor_name/
        package_name/
            scr/
                ClassName.php         # vendor_name\package_name\ClassName
            tests/
                ClassNameTest.php     # vendor_name\package_name\ClassNameTest   

```

> 容易发现psr-4的目录结构明显比psr-0的目录结构要简单.对比PSR-0，除了PSR-4可以更简洁外，需要注意PSR-0中对下划线(_)是有特殊的处理的，下划线会转换成DIRECTORY_SEPARATOR，这是出于对PHP5.3以前版本兼容的考虑，而PSR-4中是没有这个处理的，这也是两者比较大的一个区别。

> 此外，PSR-4要求在autoloader中不允许抛出exceptions以及引发任何级别的errors，也不应该有返回值。这是因为可能注册了多个autoloaders，如果一个autoloader没有找到对应的class，应该交给下一个来处理，而不是去阻断这个通道。

> PSR-4更简洁更灵活了，但这使得它相对更复杂了。例如通过完全符合PSR-0标准的class name，通常可以明确的知道这个class的路径，而PSR-4可能就不是这样了。

```
    Given a foo-bar package of classes in the file system at the following paths ...  
      
        /path/to/packages/foo-bar/  
            src/  
                Baz.php             # Foo\Bar\Baz  
                Qux/  
                    Quux.php        # Foo\Bar\Qux\Quux  
            tests/  
                BazTest.php         # Foo\Bar\BazTest  
                Qux/  
                    QuuxTest.php    # Foo\Bar\Qux\QuuxTest  
      
    ... add the path to the class files for the \Foo\Bar\ namespace prefix as follows:  
        <?php  
         // instantiate the loader  
         $loader = new \Example\Psr4AutoloaderClass;  
           
         // register the autoloader  
         $loader->register();  
           
         // register the base directories for the namespace prefix  
         $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');  
         $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');  
      
         //此时一个namespace prefix对应到了多个"base directory"  
      
         //autoloader会去加载/path/to/packages/foo-bar/src/Qux/Quux.php  
         new \Foo\Bar\Qux\Quux;  
      
         //autoloader会去加载/path/to/packages/foo-bar/tests/Qux/QuuxTest.php  
         new \Foo\Bar\Qux\QuuxTest;  

```

> PSR-4 autoloader的实现：

```
<?php  
namespace Example;  
  
class Psr4AutoloaderClass  
{  
    /** 
     * An associative array where the key is a namespace prefix and the value 
     * is an array of base directories for classes in that namespace. 
     * 
     * @var array 
     */  
    protected $prefixes = array();  
  
    /** 
     * Register loader with SPL autoloader stack. 
     *  
     * @return void 
     */  
    public function register()  
    {  
        spl_autoload_register(array($this, 'loadClass'));  
    }  
  
    /** 
     * Adds a base directory for a namespace prefix. 
     * 
     * @param string $prefix The namespace prefix. 
     * @param string $base_dir A base directory for class files in the 
     * namespace. 
     * @param bool $prepend If true, prepend the base directory to the stack 
     * instead of appending it; this causes it to be searched first rather 
     * than last. 
     * @return void 
     */  
    public function addNamespace($prefix, $base_dir, $prepend = false)  
    {  
        // normalize namespace prefix  
        $prefix = trim($prefix, '\\') . '\\';  
  
        // normalize the base directory with a trailing separator  
        $base_dir = rtrim($base_dir, '/') . DIRECTORY_SEPARATOR;  
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';  
  
        // initialize the namespace prefix array  
        if (isset($this->prefixes[$prefix]) === false) {  
            $this->prefixes[$prefix] = array();  
        }  
  
        // retain the base directory for the namespace prefix  
        if ($prepend) {  
            array_unshift($this->prefixes[$prefix], $base_dir);  
        } else {  
            array_push($this->prefixes[$prefix], $base_dir);  
        }  
    }  
  
    /** 
     * Loads the class file for a given class name. 
     * 
     * @param string $class The fully-qualified class name. 
     * @return mixed The mapped file name on success, or boolean false on 
     * failure. 
     */  
    public function loadClass($class)  
    {  
        // the current namespace prefix  
        $prefix = $class;  
  
        // work backwards through the namespace names of the fully-qualified  
        // class name to find a mapped file name  
        while (false !== $pos = strrpos($prefix, '\\')) {  
  
            // retain the trailing namespace separator in the prefix  
            $prefix = substr($class, 0, $pos + 1);  
  
            // the rest is the relative class name  
            $relative_class = substr($class, $pos + 1);  
  
            // try to load a mapped file for the prefix and relative class  
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);  
            if ($mapped_file) {  
                return $mapped_file;  
            }  
  
            // remove the trailing namespace separator for the next iteration  
            // of strrpos()  
            $prefix = rtrim($prefix, '\\');     
        }  
  
        // never found a mapped file  
        return false;  
    }  
  
    /** 
     * Load the mapped file for a namespace prefix and relative class. 
     *  
     * @param string $prefix The namespace prefix. 
     * @param string $relative_class The relative class name. 
     * @return mixed Boolean false if no mapped file can be loaded, or the 
     * name of the mapped file that was loaded. 
     */  
    protected function loadMappedFile($prefix, $relative_class)  
    {  
        // are there any base directories for this namespace prefix?  
        if (isset($this->prefixes[$prefix]) === false) {  
            return false;  
        }  
  
        // look through base directories for this namespace prefix  
        foreach ($this->prefixes[$prefix] as $base_dir) {  
  
            // replace the namespace prefix with the base directory,  
            // replace namespace separators with directory separators  
            // in the relative class name, append with .php  
            $file = $base_dir  
                  . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class)  
                  . '.php';  
            $file = $base_dir  
                  . str_replace('\\', '/', $relative_class)  
                  . '.php';  
  
            // if the mapped file exists, require it  
            if ($this->requireFile($file)) {  
                // yes, we're done  
                return $file;  
            }  
        }  
  
        // never found it  
        return false;  
    }  
  
    /** 
     * If a file exists, require it from the file system. 
     *  
     * @param string $file The file to require. 
     * @return bool True if the file exists, false if not. 
     */  
    protected function requireFile($file)  
    {  
        if (file_exists($file)) {  
            require $file;  
            return true;  
        }  
        return false;  
    }  
} 

```
> 测试用例

```
    <?php  
    namespace Example\Tests;  
      
    class MockPsr4AutoloaderClass extends Psr4AutoloaderClass  
    {  
        protected $files = array();  
      
        public function setFiles(array $files)  
        {  
            $this->files = $files;  
        }  
      
        protected function requireFile($file)  
        {  
            return in_array($file, $this->files);  
        }  
    }  
      
    class Psr4AutoloaderClassTest extends \PHPUnit_Framework_TestCase  
    {  
        protected $loader;  
      
        protected function setUp()  
        {  
            $this->loader = new MockPsr4AutoloaderClass;  
      
            $this->loader->setFiles(array(  
                '/vendor/foo.bar/src/ClassName.php',  
                '/vendor/foo.bar/src/DoomClassName.php',  
                '/vendor/foo.bar/tests/ClassNameTest.php',  
                '/vendor/foo.bardoom/src/ClassName.php',  
                '/vendor/foo.bar.baz.dib/src/ClassName.php',  
                '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php',  
            ));  
      
            $this->loader->addNamespace(  
                'Foo\Bar',  
                '/vendor/foo.bar/src'  
            );  
      
            $this->loader->addNamespace(  
                'Foo\Bar',  
                '/vendor/foo.bar/tests'  
            );  
      
            $this->loader->addNamespace(  
                'Foo\BarDoom',  
                '/vendor/foo.bardoom/src'  
            );  
      
            $this->loader->addNamespace(  
                'Foo\Bar\Baz\Dib',  
                '/vendor/foo.bar.baz.dib/src'  
            );  
      
            $this->loader->addNamespace(  
                'Foo\Bar\Baz\Dib\Zim\Gir',  
                '/vendor/foo.bar.baz.dib.zim.gir/src'  
            );  
        }  
      
        public function testExistingFile()  
        {  
            $actual = $this->loader->loadClass('Foo\Bar\ClassName');  
            $expect = '/vendor/foo.bar/src/ClassName.php';  
            $this->assertSame($expect, $actual);  
      
            $actual = $this->loader->loadClass('Foo\Bar\ClassNameTest');  
            $expect = '/vendor/foo.bar/tests/ClassNameTest.php';  
            $this->assertSame($expect, $actual);  
        }  
      
        public function testMissingFile()  
        {  
            $actual = $this->loader->loadClass('No_Vendor\No_Package\NoClass');  
            $this->assertFalse($actual);  
        }  
      
        public function testDeepFile()  
        {  
            $actual = $this->loader->loadClass('Foo\Bar\Baz\Dib\Zim\Gir\ClassName');  
            $expect = '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';  
            $this->assertSame($expect, $actual);  
        }  
      
        public function testConfusion()  
        {  
            $actual = $this->loader->loadClass('Foo\Bar\DoomClassName');  
            $expect = '/vendor/foo.bar/src/DoomClassName.php';  
            $this->assertSame($expect, $actual);  
      
            $actual = $this->loader->loadClass('Foo\BarDoom\ClassName');  
            $expect = '/vendor/foo.bardoom/src/ClassName.php';  
            $this->assertSame($expect, $actual);  
        }  
    }  



```

> 关于单元测试用例

* 单元通俗的说就是指一个实现简单功能的函数。单元测试就是只用一组特定的输入(测试用例)测试函数是否功能正常，并且返回了正确的输出