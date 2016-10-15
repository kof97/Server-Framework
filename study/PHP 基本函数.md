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

#####psr-0目录结构

```
vendor/



```