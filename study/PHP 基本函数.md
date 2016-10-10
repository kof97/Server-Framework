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