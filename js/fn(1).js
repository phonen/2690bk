/**
 * Created by Leester on 2017/2/13.
 */

//倒计时
//当前元素， num：时间
function downTime(num) {
    var option = this;
    var old = num;
    option.ready = true;
    option.count = function(ths){
        setTimeout(function () {
            option.change(ths);
        },1000);
    };
    option.change = function(ths){
        if(num == 0) {
            option.ready = true;
            ths.attr("disabled", false);
            ths.context.innerText = "点击获取验证码";
        } else {
            option.ready = false;
            ths.attr("disabled", true);
            ths.context.innerText = num + " 秒后重新发送";
            num--;
        }
        option.count(ths);
    };

    return {
        isReady:function (){
            return option.ready;
        },
        setReady:function(ready){
            option.ready = ready;
            num = old;
        },
        count:option.count
    }
}


var phoneReg = /(^1[3|4|5|7|8][0-9]{9}$)/;

/*
$(".input-border-color").on("focus", function () {
    var ths = $(this);
});
$(".input-border-color").on("blur", function () {
    var ths = $(this);
});*/
