# PHP 开发注意事项

By Arno

---

## 工具类

* 对于工具类，构造函数必须是私有的

## 遍历数组

* 尽量使用 `foreach`，尽量不要使用 `for` 和 `while`

* 提前计算好循环条件，不要在运行时计算

* 避免在循环中修改 `key` 的值

```
    /* 错误 */
    for ($i = 0; $i < count($array); ++$i) {
        // code
    }

    /* 正确 */
    $total = count($array);
    for ($i = 0; $i < $total; ++$i) {
        // code
    }

    foreach ($array as $k => $v) {
        // code
    }
```

## 大小写转换

* 如果可能出现汉字的情况，不允许使用 `strtolower()` 这类函数

使用以下示例转换

```
    strtr($string, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
```

## 目录权限

* 创建、修改目录，需要设置正确的权限，必须先使用 `umask(0000)`，然后再 `mkdir()` 或 `chmod()`，例：

```
    if (!is_dir($dir)) {
        $default = umask(0000);
        mkdir($dir, 0777);
        umask($default);
    }

    if (!is_writeable($dir)) {
        $default = umask(0000);
        chmod($dir, 0777);
        umask($default);
    }
```

## 对于整数的判断

* 不允许直接使用 `intval()` 来判断

* 函数 `intval()` 会对数字开头的字符串截取开头的数字，并且不区分 16 进制数字，因此无法通过 `intval($a)` 来判断 `$a` 一定是整数

```
    if (isset($a) && $a == intval($a)) {
        $a = intval($a);
    }
```

==其他的对于浮点数，或是其他类型数据的判断同理==

## 初始化检测

* 不允许使用未定义的变量或是未初始化的变量，使用 `isset()` 来检测

```
    if (isset($_POST['forum'])) {
        // code
    }
```

## 变量/数组是否为空的检测

* `empty()` 会先检测是否初始化，然后再检测是否为空

* 不允许使用 `count()` 检测是否数组是否为空

```
    /* 错误 */
    if (isset($_POST['forum']) && !empty($_POST['forum'])) {
        // code
    }

    /* 正确 */
    if (!empty($_POST['forum'])) {
        // code
    }
```

## 类型比较

* 尽量使用 `===` 来比较

## 尽可能的让代码更加的优雅
