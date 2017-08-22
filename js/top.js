/*$(function(){
    $.ajaxSetup({ cache: false });

    $('.top_msg').show(function(){

        $.ajax({

            type: "get",
            url: "../zs_tk/function.asp?act=top_msg",
            contentType: "application/json",
            dataType: "text",

            async: true,//同步
            success: function (date1) {
                //date1="2,2,0";
                var str= new Array();
                str=date1.split(",");
                // alert(str[0]);
                if(str[0]>0)
                { 
					//document.getElementById("top_msg").innerHTML=" <em style='color:#F1468D;font-family:Arial;width:40px;border-radius: 16px;background: #FFD67A;font-style: normal;padding-left: 3px;padding-right: 5px; margin-left: 2px;'>"+str[0]+"</em> ";
					document.getElementById("top_msg").innerHTML='('+str[0]+')';
				}

            }
        })
    });

});*/


function gotoTop(acceleration,stime) {
    acceleration = acceleration || 0.1;
    stime = stime || 10;
    var x1 = 0;
    var y1 = 0;
    var x2 = 0;
    var y2 = 0;
    var x3 = 0;
    var y3 = 0;
    if (document.documentElement) {
        x1 = document.documentElement.scrollLeft || 0;
        y1 = document.documentElement.scrollTop || 0;
    }
    if (document.body) {
        x2 = document.body.scrollLeft || 0;
        y2 = document.body.scrollTop || 0;
    }
    var x3 = window.scrollX || 0;
    var y3 = window.scrollY || 0;

    // 滚动条到页面顶部的水平距离
    var x = Math.max(x1, Math.max(x2, x3));
    // 滚动条到页面顶部的垂直距离
    var y = Math.max(y1, Math.max(y2, y3));

    // 滚动距离 = 目前距离 / 速度, 因为距离原来越小, 速度是大于 1 的数, 所以滚动距离会越来越小
    var speeding = 1 + acceleration;
    window.scrollTo(Math.floor(x / speeding), Math.floor(y / speeding));

    // 如果距离不为零, 继续调用函数
    if(x > 0 || y > 0) {
        var run = "gotoTop(" + acceleration + ", " + stime + ")";
        window.setTimeout(run, stime);
    }
}