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

