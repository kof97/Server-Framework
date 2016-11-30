# Server-Framework

高性能 PHP 服务框架，内置 PHP 服务器，不需要依赖其他的服务环境

独立守护进程，支持平滑重启，异常死亡自动拉起

支持协程调用，实现非阻塞 IO

多种模式支持

---



使用流程：

进入 `bin` 目录下

初始化： `php init.php`

启动服务： `php run.php start`

查看当前服务状态： `php run.php status`


重启服务： `php run.php restart`

平滑重启： `php run.php reload`

停止服务： `php run.php stop`

帮助： `php run.php help`

Developing . . . .
