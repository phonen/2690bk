var isLogin;

var gid = $('#comment').data('id');

//设置cookie
function setCookie(c_name,value)
{
    var exdate=new Date();
    exdate.setTime(exdate.getTime()+ 99999*24*60*60*1000);
    document.cookie=c_name+ "=" +escape(value)+ (";expires="+exdate.toGMTString());
}

// 获取cookie
function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=")
        if (c_start!=-1)
        {
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        }
    }
    return ""
}
$(function(){
    if($(window).scrollTop()>$('.detail-wrap').offset().top){
        $('.detail-content-l-main').addClass('fixed');
    }else{
        $('.detail-content-l-main').removeClass('fixed');
    }
//获取推广标记
    mark(1);
//点击设置推广标记
    $('.tui-mark').on('click',function () {
        umDataStatistics("商品详情 打开推广标记", "点击",'' ,'' , '');
        mark(2);
    });

//是否加入推广
    var gid = $('#comment').data('id');
    $.ajax({
        type: "POST",
        url: "/cdetail/favorite",
        data:{"gid":gid,"type":1},
        success: function (res) {

            if($('.tui-trans').hasClass('tui-trans-cur')){
                if(res.status == "login")
                {
                    isLogin = 0;
                    $('.J_add_tui').html('<i></i>加入推广');
                }else if(res.status == 1 ){
                    isLogin = 1;
                    if (res.data.is_favorites){
                        $('.J_add_tui').html('已加入推广');
                        $('.tui-trans .tui').addClass('cur');
                        if (res.data.qq) {
                            $('.trans-ehy').addClass("cur");
                        }
                        if (res.data.wx) {
                            $('.trans-tkl').addClass("cur");
                        }
                    }else{
                        $('.J_add_tui').html('<i></i>加入推广');
                    }
                }else{
                    $('.J_add_tui').html('<i></i>加入推广');
                }
            }else{
                if(res.status == "login")
                {
                    isLogin = 0;
                }
            }

        }
    }).then(function(){
//ajax请求完毕，获得推广状态后才可进行推广操作
        $('.tui-trans-cur .tui').on('click',function(){
            $('.J_add_tui').html('操作中…');

            if( $(this).hasClass('cur')){
                umDataStatistics("商品详情 取消推广", "点击",'' ,'' , '');
            }else{
                umDataStatistics("商品详情 加入推广", "点击",'' ,'' , '');
            }
            if( isLogin == 0){
                loginFun('tui');
            }else{
                favorite();
            }
        });
        if(!$('.tui-trans').hasClass('tui-trans-cur')){
            $('#goTowx').click(function(e){
                e.preventDefault();
            })
        }else{

            if( isLogin == 0){
                $('#goTowx').click(function(e){
                    e.preventDefault();
                    loginFun();
                })
            }
        }
        //初始化复制区域内容
        getMod();

        //转链
        $('.tui-trans-cur .trans div').click(function(){
        // favorite
            if( isLogin == 0){
                loginFun();
            }else {
                if($(this).hasClass('trans-wechat')){
                }else{
                    if (!$(this).hasClass('cur')) {
                        layer.msg('正在转换，请稍后…', {
                            time: '0' //样式类名
                        });
                        var id = $(this).attr('id'),
                            type;
                        if (id.split('ehy').length > 1) {
                            type = "qq";
                        } else {
                            type = "wx";
                        }
                        trans($(this), type, gid);
                    }
                }
            }
        });
        //评论
        $('.commentBtn').click(function(){
            umDataStatistics("商品详情 打开评论", "点击",'' ,'' , '');
            var url = 'http://www.dataoke.com/quan/disc_quan.asp?id=' + $('#comment').data('id');
            layer.open({
                type: 2,
                title: '看看大家有什么看法',
                shadeClose: true,
                shade: 0.8,
                area: ['700px', '600px'],
                content: url
            });
        });
    });




//加入推广
    function favorite(){
        $.ajax({
            type: "post",
            url: "/handle_popularize",
            data:{'act':'add_quan','id':gid},
            dataType: "text",
            cache:false,
            async: true,//同步
            success: function (res) {
                if(res== "is_login")//未登录
                {
                    $('.J_add_tui').html('<i></i>加入推广');
                    $('.tui-trans-cur .tui').removeClass('cur');
                    $('.tui-trans-cur .trans div').removeClass('cur');
                }else if(res == "is_in" ){//由'加入成功'====>'已取消'
                    $('.J_add_tui').html('<i></i>加入推广');
                    $('.tui-trans-cur .tui').removeClass('cur');
                    umDataStatistics("商品详情 取消推广", "点击",'' ,'' , '');
                    $('.tui-trans-cur .trans div').removeClass('cur');
                }else if(res == "ok" ){//#由'加入推广'====>'加入成功'
                    $('.J_add_tui').html('已加入推广');
                    $('.tui-trans-cur .tui').addClass('cur');
                }else{
                    $('.J_add_tui').html('操作失败');
                    setTimeout(function(){$('.J_add_tui').html('<i></i>加入推广');},1500);
                }
            }
        })
    }


    //左侧浮动
    $(window).scroll(function (e){
        leftImgfixed();
    });
    var leftImgfixed = function(){

        var y = $(document).scrollTop();
        if(y < $('.detail-wrap').offset().top) {
            $('.detail-content-l-main').removeClass('fixed');
        }else{
            $('.detail-content-l-main').addClass('fixed');
        }
    }


    // 焦点图片
    $('.goods-small-img li').on(
        {
            mouseenter:function(){
                $('.goods-small-img li').removeClass('cur');
                $(this).addClass('cur');
                var bigImg = $(this).find('img').eq(0).attr('src');
                $('.goods-big-img img').attr('src',bigImg);
            },
            click:function(){
                umDataStatistics("商品详情 点击图片"+parseInt($(this).index()+1)+"预览", "点击",'' ,'' , '');
                scrollTotop($('.tui-trans').offset().top);
                var bigImg = $(this).find('img').eq(0).attr('src');
                $('.tui-content img').attr('src',bigImg);
                $('.copy-main img').attr('src',bigImg);
            }
        }
    )



//判断是否支持一键复制 0 不支持 1 支持
    var ClipboardSupport = 0;
    if(typeof Clipboard != "undefined"){
        ClipboardSupport = 1;
    }else{
        ClipboardSupport = 0;
    }

    $('.qq-copy').click(function(e){
        umDataStatistics("商品详情 复制QQ模版", "点击",'' ,'' , '');
        var copy = document.getElementById('qq-copy-main');
        copyFunction(copy,'.qq-copy',"QQ文案复制成功");
    });
    $('.wechat-copy').click(function(e){
        umDataStatistics("商品详情 复制微信模版", "点击",'' ,'' , '');
        var copy = document.getElementById('wechat-copy-main');
        copyFunction(copy,'.wechat-copy',"微信文案复制成功");
    });
//设置一键复制
    var copyFunction = function(copyMain,copyBtn,copyMsg){
        if(ClipboardSupport == 0){
            layer.msg('浏览器版本过低，请升级或更换浏览器后重新复制！',{
                    time: 2000
                }
            );
        }else {
            var clipboard = new Clipboard(copyBtn, {
                target: function () {
                    return copyMain;
                }
            });

            clipboard.on('success', function (e) {
                layer.msg(copyMsg, {
                        time: 2000
                    }
                );
                e.clearSelection();
            });

            clipboard.on('error', function (e) {
                layer.msg('复制失败，请升级或更换浏览器后重新复制！', {
                        time: 2000
                    }
                );
                e.clearSelection();
            });
        }
    }

//自定义模板设置
    $('.set-tui').click(function(){
        umDataStatistics("商品详情 自定义推广文案", "点击",'' ,'' , '');
        if( isLogin == 0){
            loginFun();
        }else{
            layer.open({
                type:1,
                title: '',
                // shadeClose: true,
                closeBtn: 1,
                shade: 0.5,
                area: ['924px', '630px'],
                content:  $('#diy-mod')
            });
        }
    });

// 保存模板
    $('.modSetBtn').click(function(){
        setSelfmod(qqWx());
    });

    $('.mod-bot-btn .reset').click(function(){
        getSysmod(qqWx());
    });
    $('.mod-bot-btn .discard-changes').click(function(){
            layer.closeAll();
    });


    $('.mod-choice span').click(function(){
        var index = $(this).index();
        if(!$(this).hasClass('cur')){
            $('.mod-high ul').addClass('hide');
            $('.mod-high ul').eq(index).removeClass('hide');
            $('.mod-main textarea').addClass('hide');
            $('.mod-main textarea').eq(index).removeClass('hide');
            $('.mod-choice span').removeClass('cur');
            $(this).addClass('cur');
        }
    });
})



var qqWx = function(){
    return $('.mod-choice .cur').index();
}
//如果登录，qq和微信模板写入
var getMod = function(){
    var url='/detailtpl' ;
    $.ajax({
        type:'POST',
        data:{'gid':gid},
        url:url,
        success:function(res){
            $('#qq-copy-main').html(res.data.tpl1);
            $('.qq-tui-main .tui-content').html(res.data.tpl1);

            $('#wechat-copy-main').html(res.data.tpl2);
            $('.wechat-tui-main .tui-content').html(res.data.tpl2);
            $('.wechat-tui-main .tui-content img').remove();
            $('.wechat-tui-main .tui-content br').eq(0).remove();
            $('.wechat-tui-img .tui-content').html('<img src="'+$("#wechat-copy-main img").attr("src")+'"/>');

            if($.trim($('.wechat-tui-main .tui-content').text()) != ''){
                $('.wechat-tui-main').css('display','block');
            }else{
                $('.wechat-tui-main').remove();
            }

            if(res.data.content[0].type ==1){
                $('.mod-qq-main').val(res.data.content[0].content);
            }
            if(res.data.content[1].type == 2){
                $('.mod-wechat-main').val(res.data.content[1].content);
            }
        }
    });
}


//自定义模板
var setSelfmod = function(type){
    var content = $('.mod-main textarea').eq(type).val();
    var setModmsg = type == 0 ? "QQ模板保存成功，请刷新查看效果！" : "微信模板保存成功，请刷新查看效果！";
    $.ajax({
        type: "post",
        url: "/dsavetpl",
        data:{'type':type+1,'content':content},
        success: function (res) {
            if(res.status == 0){
                layer.msg('未修改模板，不需要保存！',{
                    time:1500
                });
            }else if(res.status == 1){
                layer.msg(setModmsg,{
                    time:1500
                });
            }
        }
    })
}
//系统默认模板
var getSysmod = function(type){
    var content = $('.mod-main textarea').eq(type).val();
    $.ajax({
        type: "post",
        url: "/dtplreset",
        data:{'type':type+1},
        success: function (res) {
            if(res.status == 1){
                $('.mod-main textarea').eq(type).val(res.data);
                layer.msg('恢复成功，请点击保存！',{
                    time:1500
                });
            }else{
                layer.msg('已是系统模板！',{
                    time:1500
                });
            }
        }
    })
}

//弹出登录提示
var loginFun = function(type){
    if(type == 'tui'){
        $('.J_add_tui').html('<i></i>加入推广');
    }

    layer.alert('请先登录再进行操作！', {
        skin: 'layui-layer-lan' //样式类名
        ,closeBtn: 1
    }, function(){
        layer.closeAll();
        location.href="/login";
    });
}

//转链
var trans = function(obj,type,gid){


    if(type == "qq"){
        umDataStatistics("商品详情 转二合一", "点击",'' ,'' , '');
        var data = {'gid':gid,'type':1},
            tMsg = "转二合一成功";
    }else {
            umDataStatistics("商品详情 转淘口令", "点击",'' ,'' , '');
        var data = {'gid':gid,'type':2},
            tMsg = "转淘口令成功";
    }
    $.ajax({
        type: "post",
        url: '/dtpwd',
        data:data,
        success: function (res) {
            if(res.status == "login")
            {
            }else if(res.status == 1 ){
                layer.closeAll();
                getMod(gid);
                if(getCookie('not-any-more') !=1 && $('.yong-trans-attention').length > 0){
                    layer.open({
                        type:1,
                        title: '',
                        closeBtn: 0,
                        shade: 0.5,
                        area: ['500px', '360px'],
                        content:  $('#yong-attention')
                    });
                }else{
                    layer.msg(tMsg,{
                        time:2000
                    });
                }
                if(!$('.tui').hasClass('cur')) {
                    $('.tui').addClass('cur');
                    $('.J_add_tui').html('已加入推广');
                }
                obj.addClass('cur');
                scrollTotop($('.tui-trans').offset().top);

            }else if(res.status == "nopid"){
                layer.closeAll();
                layer.open({
                    type:1,
                    title: '',
                    closeBtn: 1,
                    shade: 0.5,
                    area: ['360px', '300px'],
                    content:  $('#pid-set')
                });
            }else{
                layer.close();
                layer.msg('转链失败',{
                    time:1500
                });
            }
        }
    });
}
/**
 * 标记推广
 * @param t
 */
function mark(t) {
    $.ajax({
        type: "POST",
        url: "/dmakertime",
        data:{"gid":gid,"type":t},
        success: function (res) {
            if(res.status==1){
                if(res.data=='login'){
                    layer.alert('请先登录再进行操作！', {
                        skin: 'layui-layer-lan' //样式类名
                        ,closeBtn: 1
                    }, function(){
                        location.href="/login";
                    });
                    $('.tui-mark').text('未标记');
                }else {
                    $('.tui-mark').text(res.data);
                }
            }
        }
    })
}

// 滚到顶部距离y
var scrollTotop = function gotoTop(y) {
    $('body').animate({scrollTop:y},500);
}

//商品详情点击统计
$('.goods-big-img a').click(function(){
    umDataStatistics("商品详情 图片跳出", "点击",'' ,'' , '');
});
$('.goods-tit h1 a').click(function(){
    umDataStatistics("商品详情 标题跳出", "点击",'' ,'' , '');
});
$('.goods-bug-btn').click(function(){
    umDataStatistics("商品详情 纠错举报", "点击",'' ,'' , '');
});
$('.info-img a').click(function(){
    umDataStatistics("商品详情 打开作者主页(作者图标)", "点击",'' ,'' , '');
});
$('.his-home a').click(function(){
    umDataStatistics("商品详情 打开作者主页(Ta的主页)", "点击",'' ,'' , '');
});
$('.info-name a').click(function(){
    umDataStatistics("商品详情 打开作者主页(作者名称)", "点击",'' ,'' , '');
});
$('.tender-more a').eq(1).click(function(){
    umDataStatistics("商品详情 打开QQ咨询", "点击",'' ,'' , '');
});
$('.yong p a').click(function(){
    umDataStatistics("商品详情 申请计划", "点击",'' ,'' , '');
});
$('.quan p a').click(function(){
    umDataStatistics("商品详情 领券", "点击",'' ,'' , '');
});
$('#goTowx').click(function(){
    umDataStatistics("商品详情 转微信页", "点击",'' ,'' , '');
});
$('.promote-more a').click(function(){
    umDataStatistics("商品详情 查看更多", "点击",'' ,'' , '');
});
$('.promote-list ul a').click(function(){
    if($(this).index()<12){
        umDataStatistics("商品详情 浏览推荐商品1-6", "点击",'' ,'' , '');
    }else{
        umDataStatistics("商品详情 浏览推荐商品7-12", "点击",'' ,'' , '');
    }
});
$('.lingquan a').click(function(){
        umDataStatistics("商品详情 底部查看更多", "点击",'' ,'' , '');
});
$('.marketing-tit a').click(function(){
    umDataStatistics("商品详情 营销模版帮助", "点击",'' ,'' , '');
});
$('.copy-tui .use-help a').click(function(){
    umDataStatistics("商品详情 营销模版文字帮助提示", "点击",'' ,'' , '');
});
$('.iknow span').click(function(){
    layer.closeAll();
})
$('.not-any-more a').click(function(){
    layer.closeAll();
    setCookie('not-any-more','1');
})