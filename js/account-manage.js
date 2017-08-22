/**
 * Created by Leester on 2017/2/13.
 */

$(function () {

    var counter = downTime(60);
    $(".get-phone-verify-fn").on("click", function () {
        var ths = $(this);
        if(!counter.isReady()){
            return;
        }
        $(".account-main .form-input").find(".test-tips").fadeOut();
        var param = {username:$("#phone").val(),vc:$("#vc").val()};
        $.ajax({
            url:ths.data("action"),
            data : param ,
            dataType:'json',
            timeout : 30000,
            success : function (json) {
                var msg = "页面加载失败,请刷新页面或检查网络。";
                if(json.status == 0){
                    counter.setReady(true);
                    switch (json.data.status){
                        case 1:
                            msg = "账号已注册";
                            $("#phone").nextAll(".test-tips").text(msg).fadeIn(100);
                            break;
                        case 3:
                            msg = "验证码输入错误，请重新输入";
                            $("#vc").val('');
                            $("#vc").nextAll(".test-tips").text(msg).fadeIn(100);
                            $("#vc").nextAll(".verify-btn").click();
                            $('.code-img').attr('src',$('.code-img').attr('src') + "?" + Math.random())
                            break;
                        case 4:
                            msg = "短信验证发送过频繁或已超出限制";
                            $("#code").nextAll(".test-tips").text(msg).fadeIn(100);
                            break;
                        case 6:
                            msg = "手机号码错误";
                            $("#phone").nextAll(".test-tips").text(msg).fadeIn(100);
                            break;
                        case 8:
                            msg = "账号不存在";
                            $("#phone").nextAll(".test-tips").text(msg).fadeIn(100);
                            break;
                        default:
                            layer.msg(msg);
                    }
                }
                if(json.status == 1){
                    if($(".forget-pwd .retrieve-step").length > 0) {
                        $(".forget-pwd .retrieve-step").eq(1).addClass("on").siblings().removeClass("on");
                    }
                    counter.count(ths);
                    layer.tips('发送成功', ths);
                }
            },
            error:function(){
                counter.setReady(true);
            }
        });
    });
    $(".verify-btn").on("click",function(){
        $(this).find("img").eq(0).prop("src",$(this).data("href")+"?t="+Math.random());
    });

    $(".forget-pwd .reset-email input").on("input", function () {
        switchPhoneArea($(this));
    });
    $(".forget-pwd .reset-email input").on("blur", function () {
        switchPhoneArea($(this));
    });

    $("#vc, #code").on("keyup", function () {
        $(this).removeClass("test-error");
        $(this).nextAll(".test-tips").text("").fadeOut();
    });
});

function switchPhoneArea(ths) {
    var phoneArea = $(".forget-pwd .get-phone-area");
    var num = ths.val();
    var tips = ths.parents(".form-input").find(".test-tips");
    tips.text("手机或邮箱输入有误");
    if(phoneReg.test(num)) {
        tips.hide();
        phoneArea.slideDown(300);
    } else {
        phoneArea.slideUp(300);
    }
}