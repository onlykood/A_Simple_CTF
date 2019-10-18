<?php
include 'init.php';
include 'functions.php';
if (!isset($_GET['id'])) {
    die("~No id");
}
$id = intval($_GET['id']);
$link = Database::getConnection();
$sql = $link->query("SELECT * from ctf_challenges where id='$id'");
if (!$sql) {
    returnInfo(SQL_ERROR);
}
$row = $sql->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="./css/font.css">
    <link rel="stylesheet" href="./css/xadmin.css">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="./lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="./js/xadmin.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
            <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
            <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>

<body>
    <div class="x-body">
        <form class="layui-form">
            <input type="hidden" id="quesid" name="quesid" value="<?=$row['id']?>">
            <div class="layui-form-item">
                <label for="L_title" class="layui-form-label">
                    <span class="x-red">*</span>标题
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_title" name="title" required="" lay-verify="title" autocomplete="off" class="layui-input" value="<?php echo htmlspecialchars($row['title']); ?>">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_score" class="layui-form-label">
                    <span class="x-red">*</span>分数
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_score" name="score" required="" lay-verify="score" autocomplete="off" class="layui-input" value="<?=$row['score'];?>">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_type" class="layui-form-label">
                    <span class="x-red">*</span>类型
                </label>
                <div class="layui-input-inline">
                  <select id="L_type" name="type" lay-verify="type">
                    <option value="<?=$row['type'];?>"></option>
                    <option value="0">Web</option>
                    <option value="1">Reverse</option>
                    <option value="2">Pwn</option>
                    <option value="3">Misc</option>
                    <option value="4">Crypto</option>
                    <option value="5">Stega</option>
                    <option value="6">Ppc</option>
                  </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_dockerid" class="layui-form-label">
                    Dockerid
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_dockerid" name="dockerid" lay-verify="dockerid" class="layui-input" value="<?=$row['docker_id']; ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>无docker id，则不填或填0
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_dockerid" class="layui-form-label">
                    赛题依赖
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_depends" name="depends" lay-verify="depends" class="layui-input" value="<?php echo $row['depends']; ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>如 [1,2,3]
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_content" class="layui-form-label">
                    <span class="x-red">*</span>内容
                </label>
                <textarea name="content" id="L_content" required lay-verify="required" class="layui-textarea" style="outline:none;width:60%"><?php echo htmlspecialchars($row['content']); ?></textarea>
            </div>
            <div class="layui-form-item">
                <label for="L_flag" class="layui-form-label">
                    <span class="x-red">*</span>Flag
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_flag" name="password" lay-verify="password" autocomplete="off" class="layui-input" value="<?php echo $row['flag']; ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>留空则设置为随机flag
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_flag" class="layui-form-label">
                </label>
                <button class="layui-btn" lay-filter="mod" lay-submit="">
                    修改
                </button>
            </div>
        </form>
    </div>
    <script>
        layui.use(['form', 'layer'], function() {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;

            //自定义验证规则
            //form.verify({
            // username:function(value){
            //     if(value.length<2){
            //         return '用户名至少2个字符';
            //     }
            // },
            // nikename: function(value){
            //     if(value.length < 2){
            //         return '昵称至少2个字符';
            //     }
            // },
            // password:function(value){
            //     if(value!=''&&value.length<6){
            //         return '密码必须大于6字符';
            //     }
            // },
            // email:[/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,'邮箱格式不正确'],
            // //ip: [/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/, 'IP格式不正确']
            //});

            //监听提交
            form.on('submit(mod)', function(data) {
                //发异步，把数据提交给php
                $.ajax({
                    url: './ajax.php?m=modQuesInfo',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        "quesid": $("#quesid").val(),
                        "type":$("#L_type").val(),
                        "dockerid": $("#L_dockerid").val(),
                        "depends":$("#L_depends").val(),
                        "title": $('#L_title').val(),
                        "score": $('#L_score').val(),
                        "content": $('#L_content').val(),
                        "flag": $('#L_flag').val()
                    },
                    success: function(data) {
                        if (data[0].code == '0') {
                            layer.alert(data[0].text, {
                                icon: 2
                            }, function() {
                                // 获得frame索引
                                var index = parent.layer.getFrameIndex(window.name);
                                //关闭当前frame
                                parent.layer.close(index);
                            });
                            return false;
                        }
                        layer.alert("修改成功", {
                            icon: 6
                        }, function() {
                            // 获得frame索引
                            var index = parent.layer.getFrameIndex(window.name);
                            //关闭当前frame
                            parent.layer.close(index);
                        });
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
                return false;
            });
        });
    </script>
</body>

</html> 