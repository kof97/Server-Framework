#PHP 命名空间 namespace / 类别名use / 框架自动载入 机理

---

###PHP5.3新增三大特性

```
命名空间

延迟静态绑定

lambda匿名函数

```
> 命名空间的出现也使PHP可以更加合理的组织项目结构，同时通过命名空间和自动载入机制一大批 PHP 的 MVC 框架也随之出现，明了的项目结构的同时也按需载入，进一步减轻内存压力，加快执行效率。

###因为命名空间是对目录结构友好的

```
	namespace Home\Controller;

	class IndexController {
	}
```

###而 PHP5.2 之前是按造类的下划线去做类似 命名空间 的定义的
```
	class Home_Controller_IndexController {

	}

```

###命名空间 及 USE 的本质

* php 的 ```use 关键字并不是立刻导入所use的类，它只是声明某类的完整类名（命名空间::类标示符）```,而后你在上下文中使用此类时系统才会根据 use 声明获取此类的完整类名 *然后利用自动加载机制*进行载入
```
	namespace Home\Controller;

	use Home\Model\User;
	use Home\Model\Order as OrderList;

	class IndexController {

	    public function index() {
	        //只有当你调用此类时，系统才会根据 use 声明获取此类的完整类名 然后利用自动加载机制进行载入
	        $user = new User();
	        $order = new OrderList();
	    }
	}

```
* 就像如下的代码 自动载入函数是在 use 两个类之后方才实现的 因为 use 并不会立即使用此类 只有在你调用此类时系统才会在找不到此类的情况下通过 autoload 函数动态延迟加载，若仍加载不到，则报错
```
	<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	require 'vendor/autoload.php';

	$app = new \Slim\App;
	$app->get('/hello/{name}', function (Request $request, Response $response) {
	    $name = $request->getAttribute('name');
	    $response->getBody()->write("Hello, $name");
	    return $response;
	});
	$app->run();

```