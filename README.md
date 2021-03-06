# Server-Framework

高性能 Api 服务框架

idl 规范定义，自动化工具

内置 PHP 服务器，不需要依赖其他的服务环境

独立守护进程，支持平滑重启，异常死亡自动拉起

支持协程调用，实现非阻塞 IO

多种模式支持

---

环境：`Unix`、`Libevent`

PHP 版本：`>= PHP 5.3`

---


使用流程：

进入 `bin` 目录下

初始化： `php init.php`

启动服务： `php run.php start`

查看当前服务状态： `php run.php status`

![](https://raw.githubusercontent.com/kof97/Server-Framework/master/images/status.png)

重启服务： `php run.php restart`

平滑重启： `php run.php reload`

停止服务： `php run.php stop`

![](https://raw.githubusercontent.com/kof97/Server-Framework/master/images/stop.png)

帮助： `php run.php help`

![](https://raw.githubusercontent.com/kof97/Server-Framework/master/images/help.png)

---

Developing . . . . .



