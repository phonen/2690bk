(function($,window){
    // input验证
    $.fn.inputTest = function(options){
        var _this = $(this);
        var settings = jQuery.extend({
            parents : "parent-form",
            tips : "test-tips",
            btn : "submit-btn",
            id : _this.data("id"),
            val : $.trim(_this.val()),
            empty : Boolean(_this.data("empty")) || false,
            empty_tips: true,
            right : "test-right",
            error : "test-error",
            return_info: "",
            done : ""
        },options);
        var this_box = _this.parents("." + settings.parents),
            this_tips = _this.siblings("." + settings.tips);

        // 验证正确
        var test_right = function(type){
            if(type == "password"){
                var _pwd = $("input[data-id='pwd']"),
                    _repwd = $("input[data-id='repwd']"),
                    _pwd_tips = _pwd.siblings("." + settings.tips),
                    _repwd_tips = _repwd.siblings("." + settings.tips);
                if(_pwd_tips.is(":visible")){
                    _pwd_tips.stop().fadeOut(300,function(){
                        _pwd_tips.html("");
                    });
                }
                if(_repwd_tips.is(":visible")){
                    _repwd_tips.stop().fadeOut(300,function(){
                        _repwd_tips.html("");
                    });
                }
                _pwd.add(_repwd).removeClass(settings.error).addClass(settings.right);
            }else{
                if(this_tips.is(":visible")){
                    this_tips.stop().fadeOut(300,function(){
                        this_tips.html("");
                    });
                }
                _this.removeClass(settings.error).addClass(settings.right);
            }
        };
        // 验证错误
        var test_error = function(info){
            this_tips.html(info).stop().fadeIn(300);
            _this.removeClass(settings.right).addClass(settings.error);
        };

        // 正则验证
        var exp_test = function(expressions,info){
            if(settings.val == ""){
                if(settings.empty){
                    test_right();
                }else{
                    if(settings.empty_tips === false){
                        this_tips.html("").stop().fadeOut(300);
                        _this.removeClass(settings.error).addClass(settings.right);
                    }else{
                        test_error("不能为空");
                    }
                }
            }else{
                if(expressions && expressions != ""){
                    expressions = new RegExp(expressions);
                    if(expressions.test(settings.val)){
                        test_right();
                    }else{
                        test_error(info);
                    }
                }else{
                    test_right();
                }
            }
        };

        //验证手机号码或邮箱
        var exp_test2 = function (exp, phone, info) {
            if(settings.val == ""){
                if(settings.empty){
                    test_right();
                }else{
                    if(settings.empty_tips === false){
                        this_tips.html("").stop().fadeOut(300);
                        _this.removeClass(settings.error).addClass(settings.right);
                    }else{
                        test_error("不能为空");
                    }
                }
            }else{
                exp = new RegExp(exp);
                phone = new RegExp(phone);
                if(phone.test(settings.val)) {
                    test_right();
                } else if(exp.test(settings.val)) {
                    test_right();
                } else {
                    test_error(info);
                }
            }
        };

        // 密码验证
        var pwd_test = function(){
            var min = 6,max = 24;
            if(settings.val == ""){
                if(settings.empty){
                    test_right();
                }else{
                    if(settings.empty_tips === false){
                        this_tips.html("").stop().fadeOut(300);
                        _this.removeClass(settings.error).addClass(settings.right);
                    }else{
                        test_error("不能为空");
                    }
                }
            }else{
                if(settings.val.length >= min && settings.val.length <= max){
                    if(this_box.find("input[data-id='repwd']").length > 0){
                        if($.trim(this_box.find("input[data-id='pwd']").val()) == $.trim(this_box.find("input[data-id='repwd']").val())){
                            test_right("password");
                        }else{
                            test_error("两次输入密码不同");
                        }
                    }else{
                        test_right();
                    }
                }else{
                    test_error("密码长度应为" + min + "-" + max + "位");
                }
            }
        };

        $.fn.inputTest.test = function(){
            var expressions,info, phoneReg;
            switch(settings.id){
                case "email":
                    expressions = "^[\\w\\-\\.]+@[\\w\\-\\.]+\\.\\w+$";
                    phoneReg = "^1[3|4|5|7|8][0-9]\\d{8}$";
                    info = "手机号码或邮箱格式不正确";
                    exp_test2(expressions, phoneReg, info);
                    break;
                case "phone":
                    expressions = "^1[3|4|5|7|8][0-9]\\d{8}$";
                    info = "手机号应为13X、14X、15X、17X、18X开头的11位数字";
                    exp_test(expressions,info);
                    break;
                case "qq":
                    expressions = "^[0-9]{5,}$";
                    info = "QQ号码应为大于4位的数字";
                    exp_test(expressions,info);
                    break;
                case "subdomain":
                    expressions = "^[0-9A-z]{6,12}$";
                    info = "自定义域名应为6-12位英文和数字";
                    exp_test(expressions,info);
                    break;
                case "webtitle":
                    expressions = "^[\u4E00-\u9FFF|A-z|\\d]{4,12}$";
                    info = "微网站标题应为4-12位中、英文和数字";
                    exp_test(expressions,info);
                    break;
                case "webdes":
                    expressions = "^[\\d\\D]{0,100}$";
                    info = "微网站介绍应为最大100位字符";
                    exp_test(expressions,info);
                    break;
                case "pid":
                    expressions = "^mm_[0-9]{1,}_[0-9]{1,}_[0-9]{1,}$";
                    info = "PID格式不正确";
                    exp_test(expressions,info);
                    break;
                case "text_len":
                    expressions = "^[\\d\\D]{0,100}$";
                    info = "最大可填写100个字符";
                    exp_test(expressions,info);
                    break;
                case "pwd":
                    pwd_test();
                    break;
                case "repwd":
                    pwd_test();
                    break;
                case "code":
                    exp_test();
                    break;
                case "sms":
                    exp_test();
                    break;
                case "sys":
                    test_error(settings.return_info);
                    break;
                default:;
            }
            if(settings.done != ""){
                settings.done();
            }
        };
        return $.fn.inputTest.test();
    }
}(jQuery,window));