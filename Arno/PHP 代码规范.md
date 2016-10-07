# PHP 开发规范

Author: Arno

---

## 编辑规范

* 编码格式均设定为 `不带 BOM 头的 UTF-8 编码`

* 每行末尾不允许有多余的空格

* 每行字符数建议在 80 以内，尽量不要超过 120

* 符号后面都 `必须` 跟一个空格，例如 `$a = $b`、`function test() {}`、`$a > $b`、`for($i = 0; $i < 2; ++i) {}`

* 必须使用 `4 个空格` 缩进，若是 tab 缩进，必须将编辑器设置为 4 个空格的 tab

* 编辑器设置示例：

```
    sublime:
        "tab_size": 4,
        "translate_tabs_to_spaces": true
    vim:
        :set tabstop=4    "设定tab宽度为4个字符
        :set shiftwidth=4 "设定自动缩进为4个字符
        :set expandtab    "输入 tab 时自动转换为空格
    emacs:
        (setq tab-width 4)
```

## 概念统一

### 面向过程编程

* 函数/过程 function

* 

### 面向对象编程

* 类 class

* 成员变量

* 方法 method

* 

## 命名规范

### 常量

* 常量命名全部使用大写，以 `_` 为分隔

* 常量的使用范围：配置名，公共库名或者类名等，全局常量建议定义在配置文件里

```
    define('DB_MASTER', 'MASTER');
```

### 变量

* 局部变量采用下划线命名的方式，即全部使用小写，多个单词使用 `_` 分隔

```
    $database_name = 'kof97';
```

### 函数名

* 函数名采用驼峰命名

```
    function testCase($argv1, $argv2)
    {
        // function
    }
```

### 面向对象

* 类名采用 `大驼峰命名`

* 成员变量以及方法采用 `驼峰命名`

```
    class CurlClient
    {

    }
```














