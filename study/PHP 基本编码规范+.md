# PHP 开发规范

By Arno

以标准 PSR 规范为基础

> https://github.com/kof97/PSR/blob/master/PSR1_zh-cn.md

> https://github.com/kof97/PSR/blob/master/PSR2_zh-cn.md

> https://github.com/kof97/PSR/blob/master/PSR4_zh-cn.md

---

## 文件命名

* 类文件直接以类名命名

## 编辑规范

* 编码格式均设定为 `不带 BOM 头的 UTF-8 编码`

* 每行末尾 `不允许` 有多余的空格

* 每行字符数建议在 80 以内，尽量不要超过 120

* `能够使用单引号的时候，就不要使用双引号`
    
    >解释: 单引号串中的内容总被认为是普通字符，因此单引号中的内容不会被转义效率更高,双引号内容会被转义,效率较低.

* 必须采用完整的 PHP 标签 `<?php ?>`

* 若是纯 PHP 代码的文件，不允许出现 PHP 的结束标签 `?>`，文件末尾必须加上 `// end of script`，同时加上一个新的空行，例：

    >解释:旨在让不同的 PHP 项目之间切换， 或审阅别人代码时， 能够无缝开始， 降低熟悉风格的成本。 这个风格上的所有内容都是有一定意义的，
    >比如省略关闭标签主要是文件 include 和 输出内容不受干扰的考量。 否则很有可能在你的关闭标签后面掺杂东西到另一个文件里， 没有经验的人会很难排查错误。
    ---
    >如果使用闭合标签，任何由开发者，用户，或者FTP应用程序插入闭合标签后面的空格都有可能会引起多余的输出、php错误、之后的输出无法显示、空白页。
    >因此，所有的php文件应该省略这个php闭合标签，并插入一段注释来标明这是文件的底部并定位这个文件在这个应用的相对路径。这样有利于你确定这个文件已经结束而不是被删节的。
    ---
    >省略最后的 ?> 后，最后一行代码之后和所有空格、空行都被理解为程序的一部分，而不会被输出
    >这样就可避免在被嵌入时给 html 代码带来大量空白
    >也可防止空格、空行的输出给 header 之类的函数带来的问题

```
<?php



// end of script

```

* 符号后面都 `必须` 跟一个空格，例如 `$a = $b`、`function test() {}`、`$a > $b`、`for ($i = 0; $i < 2; ++$i) {}`

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

* 函数 function

* 变量

### 面向对象编程

* 命名空间 namespace

* 类 class

* 成员变量/属性 property

* 方法 method


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

* 局部变量采用 `下划线命名`

```
<?php

    class CurlClient
    {
        protected $appId;

        protected $appKey;

        protected $curl;

        protected $curlOptions;

        function __construct($app_id, $app_key)
        {
            $this->appId = $app_id;
            $this->appKey = $app_key;
        }

        public function init()
        {

        }

        public function getInstance()
        {
            return $this->curl;
        }
    }

// end of script

```

### 注释

例：

```
/**
 * Class Curl
 *
 * @category PHP
 * @package  pkg
 * @author   Arno
 */
class Curl
{
    /**
     * @var Curl instance.
     */
    protected $curl;

    /**
     * Check the curl extension.
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new Exception('The cURL extension must be loaded to use the "curl".');
        }
    }

    /**
     * Init a new curl instance.
     *
     * @params string $app_id  The app id.
     * @params array  $headers The request headers.
     */
    public function init($app_id, $headers = array())
    {
        // init
    }

    /**
     * Set a curl option.
     *
     * @param string $key
     * @param string $value
     */
    public function setopt($key, $value)
    {
        curl_setopt($this->curl, $key, $value);
    }
}

//end of script

```
