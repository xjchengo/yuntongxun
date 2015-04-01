<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Demo Page</title>
    <link href="{{ asset('packages/xjchen/yuntongxun/assets/bootstrap-3.3.4/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        header,
        main {
            display: block;
            max-width: 480px;
            min-width: 320px;
            margin: 0 auto;
        }

        header {
            text-align:center;
            margin-bottom: 20px;
        }

        #randCodeImg {
            border: 1px solid #ccc;
        }

        span.error {
            color: red;
        }
    </style>
</head>
<body>
<header>
    <h1>容联云通讯模版短信DEMO</h1>
</header>
<main class="container-fluid">
    <form>
        <div class="form-group">
            <label for="telephone">手机号</label>
            <input type="tel" name="telephone" class="form-control step1 step2" id="telephone" placeholder="请输入手机号">
        </div>
        <div class="form-group">
            <label for="captcha">图形验证码</label>
            <div class="row">
                <div class="col-xs-6">
                    <input type="text" name="captcha" class="form-control step1" id="captcha" placeholder="验证码">
                </div>
                <div class="col-xs-6">
                    <img id="randCodeImg" src="{{ route('xjchen.yuntongxun.captcha.refresh') }}">
                </div>
            </div>
        </div>
        <div class="form-group">
            <button type="button" id="get-sms" class="btn btn-primary">获取手机验证码</button>
        </div>
        <div class="form-group">
            <label for="sms">手机验证码</label>
            <input type="text" name="sms" class="form-control step2" id="sms" placeholder="手机验证码">
        </div>
        <button type="button" id="submit-btn" class="btn btn-default">提交</button>
    </form>
</main>

<script src="{{ asset('packages/xjchen/yuntongxun/assets/js/jquery-2.1.3.min.js') }}"></script>
<script src="{{ asset('packages/xjchen/yuntongxun/assets/bootstrap-3.3.4/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('packages/xjchen/yuntongxun/assets/jquery-validation-1.13.1/dist/jquery.validate.min.js') }}"></script>
<script>
    var smsRule = {
        required: true,
        messages:{
            required: "动态验证码不能为空"
        }
    };
    var $getSMSBtn = $('#get-sms');

    var InterValObj; //timer变量，控制时间
    var count = 60; //间隔函数，1秒执行
    var curCount;//当前剩余秒数
    //timer处理函数
    function setRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $getSMSBtn.html('获取手机验证码');
            $getSMSBtn.removeAttr('disabled');
        }else {
            curCount--;
            $getSMSBtn.html('重新获取('+curCount+')');
            console.log(curCount);
        }
    }

    function refreshRandCode() {
        $('#randCodeImg').hide().attr('src',
                '{{ route('xjchen.yuntongxun.captcha.refresh') }}?' + Math.floor(Math.random() * 100)).fadeIn();
    }

    $('#randCodeImg').click(function(){
        refreshRandCode();
    });

    $.validator.addMethod("isMobile", function(value, element) {
        var length = value.length;
        var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(14[1-9]{1})|(17[1-9]{1})|(18[0-9]{1}))+\d{8})$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "请正确填写手机号码");

    $("form").validate({
        errorElement:"span",
        rules : {
            telephone:{
                required:true,
                isMobile:true
            },
            captcha:{
                required:true,
                remote: {
                    url: "{{ route('xjchen.yuntongxun.captcha.check') }}",
                    type: "POST",
                    cache: false
                }
            },
            sms: {
                required: true
            }
        },
        messages : {
            telephone:{
                required:"请输入手机号码",
                isMobile:"请正确填写您的手机号码"
            },
            captcha:{
                required:"请输入图形验证码",
                remote:"图形验证码输入不正确"
            },
            sms: {
                required:"动态验证码不能为空"
            }
        },
        errorPlacement: function (error, element){
            error.appendTo(element.parent());
        }
    });
    $getSMSBtn.click(function(){
        if ($(".step1").valid()) {
            var data = $("form").serialize();
            $.ajax({
                type: "POST",
                cache: false,
                url: "{{ route('xjchen.yuntongxun.sms.send') }}",
                data: data,
                dataType: "json",
                success: function(data) {
                    if(data.status == 0){
                        $getSMSBtn.attr('disabled', 'disabled');
                        curCount=count;
                        InterValObj = window.setInterval(setRemainTime, 1000); //启动计时器，1秒执行一次
                        alert(data.msg);
                    }else{
                        alert(data.msg);
                        refreshRandCode();
                    }
                }
            });
        }
    });
    $('#submit-btn').click(function(){
        if ($('.step2').valid()) {
            var data = $("form").serialize();
            $.ajax({
                type: "POST",
                cache: false,
                url: "{{ route('xjchen.yuntongxun.sms.check') }}",
                data: data,
                dataType: "json",
                success: function(data) {
                    alert(data.msg);
                }
            });
        }
    });
</script>
</body>
</html>