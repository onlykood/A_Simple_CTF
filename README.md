# The Simple CTF

---

* ## Background

2017年为了给社团成员做一个看起来还说得过去的CTF平台，所以就随便写了一下，不过没想到最后还成了一个有意思的小玩意，页面看起来比较简陋，也没有用什么框架之类的，所以代码显得很多，以后会尝试去重构代码。采用 html + ajax 动态刷新页面，大部分js、css采用 BootCDN ，所以访问速度较快。

* ## Install

安装方法很简单，除了 apache2 + php7.1 + mysql5.6以外，还需要安装 php 的几个扩展。

```bash
    apt install apache2 php7.1 mysql
    apt install libapache2-mod-php php-mysql php-xml php-gd
    git clone https://github.com/onlykood/A_Simple_CTF
    mv A_Simple_CTF/* /var/www/html/
```

之后访问index.php即可自动跳转至安装页面，安装完成后即可访问。

* ## Usage

    正常访问即可，更多功能请自行挖掘😂

    演示 Demo: <http://test.onlykood.com>

    2019/08/26
    emmm , 由于服务器到期，已经挂了，以后会更新

* ## License

    该项目签署了 MIT 授权许可，详情请参阅 [LICENSE.md](https://github.com/onlykood/A_Simple_CTF/master/LICENSE)

* ## Other

    使用了 Materialize 前端框架，x-admin 作为后台框架。

    仅测试了 win10+wamp(php7.1.9) 以及 ubuntu16.04LTS + lamp 模式，其他环境如有问题，请先自己分析处理。

    如果在使用过程中出现问题，欢迎联系QQ： 702142058，同时您也可以对本项目加以修改，维护。

* ## QA:
    Q: Not show captcha image?
    A: maybe not install php-gd or not restart apache2, or config.php have a space in front of '<?php'

    Q: cache questions
    A: reset cache, will delete all files in CACHEPATH.