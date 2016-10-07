# PHP 开发规范

Author: Arno

---

## 编辑规范

> * 编码格式均设定为 `不带 BOM 头的 UTF-8 编码`

> * 每行末尾不允许有多余的空格

> * 每行字符数建议在 80 以内，尽量不要超过 120

> * 必须使用 `4 个空格` 缩进，若是 tab 缩进，必须将编辑器设置为 4 个空格的 tab

> * 编辑器设置示例：

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