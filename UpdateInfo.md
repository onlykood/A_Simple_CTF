# Update Info

## 18/11/25

- 未授权访问问题
- 更改注册时的key生成规则
- 管理员与普通用户权限分隔

## 18/11/26

- 后台答题信息倒序

## 18/12/5

- 修复排行显示bug
- 修复公告隐藏bug
- 优化wp显示

## 18/12/10

- 选手比赛机器确认
- 动态flag
- ip确认
- key确认

## 18/12/18

- 修复登陆、注册无法限制的bug


## 2019/1/31

网站较大版本更新计划


1、个人信息可以上传头像

2、设置ctf手级别，根据题目的累积，设置青铜到王者 X
3、根据积分累积,设置个人的能力趋势，比如擅长web还是pwn X
4、利用积分换取线索 doing
5、如果可以的话，可以给予别人放题的权力，支持原创题提交平台，然后后台审核通过可以放题 , 交给管理处理
6、如果长时间卡在一道题上，比如触发连续几天点开一题之类的条件，给予一定的线索提醒 X 卡在路上翻提示

额外：
1. 增加web题，设置docker下发，让大家能学到更多东西，而不是在虚拟机中有过多的限制
2. 增加好玩的短信验证，方便重置密码跟登陆、注册等，ctf 短信升级/频率限制 X 无意义，浪费
3. 增加视频教程板块，这个内容待定 doing

证书、维护、更新、数据库、后台、docker

动态得分数据库机制修改，改成静态固定、动态不固定、一血随意 考虑中

## 2019/02/14

1. 增加头像自定义上传
2. 增加排行榜头像显示

## 2019/02/18
1. 增加 web 题 docker 下发功能，从此可以任意的玩 web 了
2. 完善 docker 下发规则，修正部分代码
3. 增加后台docker处理

## 2019/2/26 -
1. 修改超级管理密码的登陆bug
2. 增加用户每次登陆记录
3. 修改ip存储规则，经过分析，觉得不是太有必要 x
4. 简便安装 doing
5. 删除冗余文件


## 2019/3/1
1. 一些变量的规整化
2. 优化数据库结构
3. 修复记录用户登陆 ip 的 bug，删除账号密码错误时的ip记录

## 2019/3/7
1. 重构数据库完成，整合资源
2. 计划增加选择题
3. 增加主题颜色

## 2019/3/18
1. 默认图像设置为首字母
2. 修改 challenge 模态框滚动条样式
3. 自定义开启关闭邮箱验证
4. 修改为黑色主题，稍后会增加自定义主题

## 2019/3/20
1. 修复调整部分设置
2. 增加非邮箱验证注册

## 2019/3/25
1. 增加pwn docker下发功能
2. 增加docker销毁功能
3. 修复register页面bug

## 2019/3/26
1. 增加赛题依赖功能，必须将该赛题的前置依赖赛题解答出来才有可能继续做后面的赛题


## 2019/6/2
1. 修复部分bug
2. 增加菜单栏样式赛题
3. 增加简便安装模式
需要做的：
1. 后台配置优化
2. 取消 typeid 字段，支持修改 type  √
3. 菜单栏样式与按钮样式任意切换 √


## 2019/7/22
目标：
1. 将规则配置存入数据库，后台可改写
2. easy_install自动重命名_bak.php文件
3. 本地远程自动切换
4. bug修复，retunInfo,
5. config1函数替换
6. 动态socre切换

## 2019年8月24日
开始处理上面的问题以及下列问题：

1. 排行榜问题，静态积分higchart 有问题，数据库查询问题。 √
2. 后台公告增加板块问题， √
3. flag提交问题 √
4. 历史遗留问题，后台很多  √
5. 增加configs真实性
6. 排行先后提交问题 √
7. 折线图问题， √
8. 更新init.bak √

## 2019年8月24日
1. 增加排行榜缓存，排行榜运行效率提升10倍+，减少mysql查询操作，减轻服务器压力
2. higchart前端调整，查询语句调整，更能正确反应排名情况
3. 规范化 users_action 库中 states 状态码，增加 descrip 字段 （等待实现）
    1xx 信息变更：
        100 创建用户
        101 修改信息
    2xx xxxxx:
        200 
        201
    3xx xxxxx:
        300
        301
4. 修复 flag 提交时封号检测逻辑 √
5. 删除 ctf_challenges 中的 type_id 字段 √
6. 后台可以对赛题的类型进行编辑了 √
7. 修复了后台动态flag的显示 √
8. 修复了后台答题信息的删除错误bug √
9. 统一了配置信息，基本上都写入数据库中 √
10. 在后台增加了配置修改的地方 √
需求：
1. 标题后台可控 √
2. 默认主题配色后台可控（等待实现）
3. 题目按钮式/菜单栏式后台可控（等待实现）
4. 增加后台进入按钮/位置（等待实现）
5. 动态积分曲线设置不合理，建议修改（等待实现）
6. Recent Solves优化（等待实现）

目前已知问题：
    后台配置页面存在sql注入，参数过多，没有美化页面问题，之后更更新。 √
    打开浏览器首次访问网站时，会存在数据异常，刷新之后无问题，原因是并发导致的token被两次生成，导致了错误的结果。 √

## 2019/9/26

需求：
    1. 支持用户、赛题的导入导出
    2. 制作成docker，存放于仓库
    3. 增加开始/结束比赛时间 √
    4. 增加给客户观看的比赛榜单，炫酷的那种

## 2019/10/16

修复：
    1. 后台赛题修改，类型未定义问题
    2. 修复登陆处逻辑错误，允许admin用户直接登录
    3. 修复一血开启时的错误加分逻辑
    4. 修改easyInstall.php 缓存文件存放位置

## 2019/10/17

修复：
    1. docker 下发问题，（代码无关，at配置问题，1. 权限。2.开启atq。3.开启www-data atq.deny）
    2. docker 无法主动销毁问题

## 2020/04/28

修复：
    1. 验证码图片数字无法显示，imagettftext()函数需要的是绝对路径。

增加：
    1. 后台可以导出成绩表格。

已知问题：
    getRank流程优化，优先度：低。
