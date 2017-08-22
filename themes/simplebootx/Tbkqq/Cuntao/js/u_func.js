$(function(){
 
    $.ajaxSetup({ cache: false });

    $('#my_sys').hover(function(){
        layer.tips('点击去设置我的资料>', '#my_sys', {
            tips: [1, '#3595CC'],
            time: 4000
        });
    },function() {});

    $('#anniu').on('click',function(){
        layer.alert('调试中！即将上线！', {
            skin: 'layui-layer-lan' //样式类名
            ,closeBtn: 0
        } );
    });

    $('.quan_add_u').on('click',function(){
        var quan_id=this.id;
       // alert(quan_id);
        if(!$(this).parents('.goods-item').hasClass('onClick')){
            $(this).parents('.goods-item').addClass('onClick');
        }
	    if($(this).attr('data-page') === 'quanPage')
		{
			document.getElementById(this.id).innerHTML='<p>执行中···</p><span></span>';
		}
		else  if($(this).attr('data-page') === 'quanPage2')
		{
			document.getElementById(this.id).innerHTML='<div class="tg-status add-tg"><p>执行中···</p><span></span></div>';
		}
		else
		{
			document.getElementById(this.id).innerHTML="执行中···";
		}
        quan_id=quan_id.replace("quan_add_u_","");
        if($(this).attr('data-page') === 'quanPage' || $(this).attr('data-page') === 'quanPage2')
        {
            showHintNew("add_quan","quan_add_u",quan_id);
        }
        else
        {
            showHint("add_quan","quan_add_u",quan_id);
        }
    });

    $('.gy_add_u').on('click',function(){
        var quan_id=this.id;
        if(!$(this).parents('.goods-item').hasClass('onClick')){
            $(this).parents('.goods-item').addClass('onClick');
        }
        document.getElementById(this.id).innerHTML="执行中···";
        quan_id=quan_id.replace("gy_add_u_","");
        showHint("gy_add_u","gy_add_u",quan_id);
    });

    $('.ju_add_u').on('click',function(){
        var ju_id=this.id;
        if(!$(this).parents('.goods-item').hasClass('onClick')){
            $(this).parents('.goods-item').addClass('onClick');
        }
        document.getElementById(this.id).innerHTML="执行中···";
        ju_id=ju_id.replace("ju_add_u_","");
        showHint("add_ju","ju_add_u",ju_id);
    });

//$('.julist_add_u').on('click',function(){
//	var ju_id=this.id;
//	document.getElementById(this.id).innerHTML="执行中";
//	ju_id=ju_id.replace("ju_add_u_","");
//    showHint("add_ju","ju_add_u",ju_id);
//    });

    $('.show_line').on('click',function(){
        var gid=this.id;
        gid=gid.replace("show_line_","");
        showline("line_show","show_line",gid);
    });

    $('.hide_line').on('click',function(){
        var gid=this.id;
        gid=gid.replace("hide_line_","");
        showline("line_hide","hide_line",gid);
    });

    $('.fav_sendtime').on('click',function(){  //设置推广时间
        var gid=this.id;
        gid=gid.replace("fav_sendtime_","");
        get_my_mb("setup_fav_sendtime","fav_sendtime_",gid);
    });

    $('.add_qing').on('click',function(){
        var url = $('#save_qing').val();
        var gid=this.id;
        gid=gid.replace("add_qing_","");
        document.getElementById("add_qing_"+gid).innerHTML="ok";
        //直接调用接口
        $.get(url+'?id='+gid, function (response) {
            //console.log(response);
            if (response == 200 || response == '200') {
                document.getElementById("add_qing_"+gid).innerHTML="ok";
            } else {
                document.getElementById("add_qing_"+gid).innerHTML="ok";
            }
        });
        //get_my_mb("add_qing","add_qing_",gid);
    });

    $('.goods_sendtime').on('click',function(){  //设置我的推广时间
        var gid=this.id;
        //alert(gid);
        gid=gid.replace("goods_sendtime_","");
        get_my_mb("setup_fav_sendtime","goods_sendtime_",gid);
    });

    $('.huan_bq').show(function(){           //替换表情
        var neirong=this.innerHTML;
        // neirong=neirong.replace(//mg/g,"<img src='http://www.dataoke.com/img/qqimg/mg.png'>");
        neirong=neirong.replace(new RegExp("/mg","gm"),"<img src='/img/qqimg/mg.png' style='margin-bottom:-5px;'>");
        neirong=neirong.replace(new RegExp("/ka","gm"),"<img src='/img/qqimg/ka.png' style='margin-bottom:-5px;'>");
        neirong=neirong.replace(new RegExp("/yb","gm"),"<img src='/img/qqimg/yb.png' style='margin-bottom:-5px;'>");
        neirong=neirong.replace(new RegExp("/gz","gm"),"<img src='/img/qqimg/gz.png' style='margin-bottom:-5px;'>");
        neirong=neirong.replace(new RegExp("/lw","gm"),"<img src='/img/qqimg/lw.png' style='margin-bottom:-5px;'>");
        neirong=neirong.replace(new RegExp("/qiang","gm"),"<img src='/img/qqimg/qiang.png' style='margin-bottom:-5px;'>");
        this.innerHTML=neirong;
        //document.getElementById("cid_u_num_"+cid).innerHTML="<img src='http://www.dataoke.com/img/loading_type.gif'>";
        //ju_id=ju_id.replace("ju_add_u_","");
    });

    $('.copy_mb').on('click',function(){
        var gid=this.id;
        coid="#"+gid;

        if(coid.indexOf('copy_mb') >= 0)
        {  gid=gid.replace("copy_mb_","");
            if(gid.indexOf('gy') >= 0)
            {
                gid=gid.replace("gy_","");
                obi_id='gy_diy_show_'+gid;}
            else
            {obi_id='my_diy_show_'+gid;}
        }
        else
        {  gid=gid.replace("copy_ut_mb_","");
            obi_id='quan_goods_show_'+gid;
        }
        if ((navigator.userAgent.indexOf('MSIE') >= 0) || (navigator.userAgent.indexOf('Trident') >= 0) || (navigator.userAgent.indexOf('trident') >= 0))
        {
            var obj_mb=document.getElementById(obi_id);
            layer.tips('复制成功', coid);
            copyText(obj_mb);
        }
        else
        {

            layer.alert('请将浏览器切换到【IE模式】再复制！', {
                skin: 'layui-layer-lan'
                ,closeBtn: 0
                ,shift: 4 //动画类型
            });
        }
    });

    $('.quan_mb_my_content').show(function(){
        var quan_id=this.id;
        quan_id=quan_id.replace("my_diy_show_","");
        document.getElementById("my_diy_show_"+quan_id).innerHTML="<div style='padding:40px;' align='center'><img src='/img/loading.gif'></div>";
        //ju_id=ju_id.replace("ju_add_u_","");
        get_my_mb("get_my_mb_show","my_diy_show_",quan_id);
    });

    // $('.gy_mb_my_content').show(function(){
    //     var quan_id=this.id;
    //     quan_id=quan_id.replace("gy_diy_show_","");
    //     document.getElementById("gy_diy_show_"+quan_id).innerHTML="<div style='padding:40px;' align='center'><img src='/img/loading.gif'></div>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("get_gy_mb_show","gy_diy_show_",quan_id);
    // });
    //
    // $('.Arial_num').show(function(){           //显示分类数量
    //     var cid=this.id;
    //     cid=cid.replace("cid_num_","");
    //     document.getElementById("cid_num_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("show_cid_num","cid_num_",cid);
    // });
    //
    // $('.Arial_num1').show(function(){           //显示分类数量
    //     var cid=this.id;
    //     cid=cid.replace("rq_top_num_","");
    //     document.getElementById("rq_top_num_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("show_rq_top_cid_num","rq_top_num_",cid);
    // });
    //
    // $('.Arial_num2').show(function(){           //显示分类数量
    //     var cid=this.id;
    //     cid=cid.replace("xl_top_num_","");
    //     document.getElementById("xl_top_num_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("show_xl_top_cid_num","xl_top_num_",cid);
    // });
    //
    // $('.Arial_num_gy').show(function(){           //显示高佣分类数量
    //     var cid=this.id;
    //     cid=cid.replace("gy_cid_num_","");
    //     document.getElementById("gy_cid_num_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("gy_cid_num","gy_cid_num_",cid);
    // });
    //
    // $('.Arial_num_u').show(function(){           //显示推广中分类数量
    //     var cid=this.id;
    //     cid=cid.replace("cid_u_num_","");
    //     document.getElementById("cid_u_num_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("show_fav_type_num","cid_u_num_",cid);
    // });
    //
    // $('.fav_sendnum').show(function(){           //显示推广中数量
    //     var cid=this.id;
    //     cid=cid.replace("fav_sendnum_","");
    //     document.getElementById("fav_sendnum_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("show_fav_sendnum","fav_sendnum_",cid);
    // });
    //
    // $('.fav_sendtime').show(function(){           //显示推广标识时间
    //     var cid=this.id;
    //     cid=cid.replace("fav_sendtime_","");
    //     document.getElementById("fav_sendtime_"+cid).innerHTML="<img src='/web/img/loading_type.gif'>";
    //     //ju_id=ju_id.replace("ju_add_u_","");
    //     get_my_mb("show_fav_sendtime","fav_sendtime_",cid);
    // });

    
    
    
    
    
//这里请求全部：
//     $('.quan_add_u_1').show(function(){    //判断领券商品是否加入推广
//         var quan_id=this.id;
//         alert(quan_id);
//         quan_id=quan_id.replace("quan_add_u_","");
//         get_myfav_show("get_fav_show","quan_add_u_", quan_id,1);
//     });

//$('.ju_add_u').show(function(){    //判断聚划算商品是否加入推广
//	 var ju_id=this.id;
//	 ju_id=ju_id.replace("ju_add_u_","");
//	 //document.getElementById("my_diy_show_"+quan_id).innerHTML="<div style='padding:40px;' align='center'><img src='http://www.dataoke.com/img/loading.gif'></div>";
//	//ju_id=ju_id.replace("ju_add_u_","");
//     get_myfav_show("get_fav_show","ju_add_u_",ju_id,2);
//    });

    $('.gy_add_u').show(function(){    //判断聚划算商品是否加入推广
        var ju_id=this.id;
        ju_id=ju_id.replace("gy_add_u_","");
        //document.getElementById("my_diy_show_"+quan_id).innerHTML="<div style='padding:40px;' align='center'><img src='http://www.dataoke.com/img/loading.gif'></div>";
        //ju_id=ju_id.replace("ju_add_u_","");
        get_myfav_show("get_fav_show","gy_add_u_",ju_id,3);
    });


    $('.goods_quan_bz').show(function(){    //判断是否进行备注
        var quan_id=this.id;
        quan_id=quan_id.replace("goods_quan_bz_","");
        //document.getElementById("my_diy_show_"+quan_id).innerHTML="<div style='padding:40px;' align='center'><img src='http://www.dataoke.com/img/loading.gif'></div>";
        //ju_id=ju_id.replace("ju_add_u_","");
        get_my_mb("get_goods_quan_bz","goods_quan_bz_",quan_id);
    });


    $('.del_my_quan').on('click',function()
    {
        var quan_id=this.id;
        quan_id=quan_id.replace("del_my_quan_","");

        layer.confirm('你确定删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            delgoods("del_my_quan","quan_goods",quan_id);
        }, function(){});

    });

    $('.del_my_gy').on('click',function()
    {
        var quan_id=this.id;
        quan_id=quan_id.replace("del_my_gy_","");

        layer.confirm('你确定删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            delgoods("del_my_gy","gy_fav_goods",quan_id);
        }, function(){});

    });

    $('.del_rizhi_goods').on('click',function()     //删除日志
    {
        var quan_id=this.id;
        quan_id=quan_id.replace("del_rizhi_goods_","");

        layer.confirm('你确定删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            delgoods("del_my_rizhi","rizhi_goods",quan_id);
        }, function(){});

    });

    $('#delete_all_rizhi').on('click',function()     //清空日志商品
    {

        layer.confirm('你确定清空日志商品吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            del_all_rizhi("del_all_rizhi",1);
        }, function(){});

    });

    $('#get_qzone_html').on('click',function()
    {
        layer.open({
            type: 2,
            title: '生成QQ空间日志HTML',
            shadeClose: true,
            shade: 0.8,
            area: ['680px', '90%'],
            content: 'result.asp' //iframe的url
        });
    });

    $('#zh_que_click').on('click',function()
    {
        layer.open({
            type: 2,
            title: '批量转换鹊桥链接',
            shade: 0.8,
            area: ['550px', '400px'],
            content: 'zh_que_click.asp' //iframe的url
        });
    });
    $('.create_my_sea_bq').on('click',function()
    {
        //判断我是否登录：
        $.get($('#jude_login_url').val(), function (response) {
            if (response.status === 1) {
                layer.open({
                    type: 2,
                    title: '设置我的搜索标签',
                    shade: 0.8,
                    area: ['550px', '400px'],
                    content: '/qlist/s_my_bq.asp' //iframe的url
                });
            } else {
                layer.alert('请先登录再进行操作！', {
                    skin: 'layui-layer-lan' //样式类名
                    ,closeBtn: 0
                }, function(){
                    location.href="/login";
                });
            }
        });
    });

    $('.goods_quan_bz').on('click',function()         //备注信息
    {
        var goodsid=this.id;
        goodsid=goodsid.replace("goods_quan_bz_","");
        layer.open({
            type: 2,
            title: '我的推广商品信息备注',
            shade: 0.8,
            area: ['550px', '450px'],
            content: 'beizhu.asp?goodsid='+goodsid //iframe的url
        });
    });


    $('.zy_quan').on('click',function()
    {
        var goodsid=this.id;
        goodsid=goodsid.replace("zy_quan_","");
        layer.open({
            type: 2,
            title: '看看大家有什么看法',
            shade: 0.8,
            area: ['700px', '600px'],
            content: 'disc_quan.asp?id='+goodsid //iframe的url
        });
    });

    $('#zh_que_bt').on('click',function()
    {
        if($('#pid').val()==0)
            alert("你未设置PID，无法转换！");
        else
        {
            var fangshi=$("input[name='fangshi']:checked").val();
            var mypid=$('#pid').val();
            document.getElementById("zh_content").innerHTML="<img src='/web/img/loading_type.gif'>";
            get_que_click(mypid,fangshi);





        }
    });

    $('.del_my_ju').on('click',function()
    {
        var ju_id=this.id;
        ju_id=ju_id.replace("del_my_ju_","");

        layer.confirm('你确定删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            delgoods("del_my_ju","ju_fav_goods",ju_id);
        }, function(){});



    });

});

function DoEmpty(id,words,url)
{
    if (confirm(words))
        location.href=url ;
}

//function get_my_mb1(str,leixing,gid)
//{
//var xmlhttp;
////alert(id+"_"+i);
//
//if (window.XMLHttpRequest)
//  {// code for IE7+, Firefox, Chrome, Opera, Safari
//  xmlhttp=new XMLHttpRequest();
//  }
//else
//  {// code for IE6, IE5
//  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
//  }
//xmlhttp.onreadystatechange=function()
//  {
//  if (xmlhttp.readyState==4 && xmlhttp.status==200)
//    {
//	      document.getElementById(leixing+gid).innerHTML=xmlhttp.responseText;
//    }
//  }
//xmlhttp.open("GET","http://www.dataoke.com/ucenter/save.asp?act="+str+"&gid="+gid,true);
//xmlhttp.send();
//}

function get_my_mb(str,leixing,gid) {
   
    $.ajax({
        type: "get",
        url: "http://www.dataoke.com/ucenter/save.asp?act="+str+"&gid="+gid,
        contentType: "application/json",
        dataType: "text",
        async: true,//同步
        success: function (date1) {
            if(str=="get_goods_quan_bz")
            {
                if(date1=="is_login")
                    document.getElementById(leixing+gid).innerHTML="进行备注";
                else
                    document.getElementById(leixing+gid).innerHTML=date1;

            }
            else if(str=="setup_fav_sendtime")
            {
                if(date1=="is_login") 
                {
                    layer.alert('请先登录再进行操作！', {
                        skin: 'layui-layer-lan' //样式类名
                        ,closeBtn: 0
                    }, function(){
                        location.href="/login";
                    }); //alert("请先登录再操作！");
                }
                else
                    document.getElementById(leixing+gid).innerHTML=date1;
            }
            else
                document.getElementById(leixing+gid).innerHTML=date1;
        }
    })
}


function get_myfav_show(str,leixing,gid,typeid) {
    $.ajax({
        type: "get",
        url: "http://www.dataoke.com/ucenter/save.asp?act="+str+"&gid="+gid+"&typeid="+typeid,
        contentType: "application/json",
        dataType: "text",

        async: true,//同步
        success: function (date1) {
          
            //document.getElementById(leixing).innerHTML = date1;
            if(date1=="is_login")
                document.getElementById(leixing+gid).innerHTML="加入推广";
            else
                document.getElementById(leixing+gid).innerHTML=date1;
        }
    })
}

function get_que_click(pid,fangshi) {
    $.ajax({

        type: "get",
        url: "http://www.dataoke.com/ucenter/save.asp?act=get_que_click&fangshi="+fangshi+"&pid="+pid,
        contentType: "application/json",
        dataType: "text",

        async: true,//同步
        success: function (date1) {
            document.getElementById("zh_content").innerHTML=date1;
        }
    })
}

function del_all_rizhi(str,leixing) {
    $.ajax({
        type: "get",
        url: "http://www.dataoke.com/ucenter/save.asp?act="+str+"&type_id="+leixing,
        contentType: "application/json",
        dataType: "text",

        async: true,//同步
        success: function (date1) {
            if(date1=="ok")
            {
                layer.msg('删除成功！', {icon: 1});
                location.href="?type=quan";
            }
            else
            {
                layer.msg('亲，删除失败！', {icon: 2}); }
        }
    })
}

//function get_myfav_show1(str,leixing,gid,typeid)
//{
//var xmlhttp;
////alert(id+"_"+i);
//
//if (window.XMLHttpRequest)
//  {// code for IE7+, Firefox, Chrome, Opera, Safari
//  xmlhttp=new XMLHttpRequest();
//  }
//else
//  {// code for IE6, IE5
//  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
//  }
//xmlhttp.onreadystatechange=function()
//  {
//  if (xmlhttp.readyState==4 && xmlhttp.status==200)
//    {
//	   if(xmlhttp.responseText=="is_login")
//	      document.getElementById(leixing+gid).innerHTML="加入推广";
//	   else
//	      document.getElementById(leixing+gid).innerHTML=xmlhttp.responseText;
//    }
//  }
//xmlhttp.open("GET","http://www.dataoke.com/ucenter/save.asp?act="+str+"&gid="+gid+"&typeid="+typeid,true);
//xmlhttp.send();
//}


function showHint(str,leixing,id) {
    $.ajax({
        type: "get",
        url: "http://www.dataoke.com/ucenter/save.asp?act="+str+"&id="+id,
        contentType: "application/json",
        dataType: "text",
        async: true,//同步
        success: function (date1) {
            //alert(date1);
            //layer.msg('nihaooaoaoa', {icon: 1, time: 2000});
            //alert(date1);
            if(date1=="ok")
			{	
                //document.getElementById(leixing+"_"+id).innerHTML="加入成功";//old logic code
				
				//new logic code begin
				var obj1 = document.getElementById(leixing+"_"+id);
				// if($(obj1).attr('data-page') === 'quanPage')
				// {
				//
				// 	obj1.innerHTML='<p>加入成功</p><span></span>';
                //
				// }
				// else  if($(obj1).attr('data-page') === 'quanPage2')
				// {
				// 	obj1.innerHTML='<p>加入成功</p><span></span>';
				// }
				// else
				// {
				// 	obj1.innerHTML="加入成功";
				// }
				//new logic code end
                // obj1.innerHTML='<p><i></i>已加入推广</p><span></span>';
                $('#'+leixing+'_'+id).html('<p>取消推广</p><span></span>');
                $('#'+leixing+'_'+id).siblings('a').append('<div class="add_tg_status had-tg"><b><i></i><w>已加入</w></b><span></span></div>');
                if(!$('#'+leixing+'_'+id).parents('.goods-item').hasClass('goods-tg')){
                    $('#'+leixing+'_'+id).parents('.goods-item').addClass('goods-tg');
                }


            }
			else
            {
                if(date1=="is_in")
				{
                    var obj2 = document.getElementById(leixing+"_"+id);
                    //document.getElementById(leixing+"_"+id).innerHTML="已取消";//old logic code
					
					//new logic code begin
					// if($(obj2).attr('data-page') === 'quanPage')
					// {
					//
					// 	obj2.innerHTML='<p>已取消</p><span></span>';
					// }
					// else  if($(obj2).attr('data-page') === 'quanPage2')
					// {
					// 	obj2.innerHTML='<p>已取消</p><span></span>';
					// }
					// else
					// {
					// 	obj2.innerHTML="<p>已取消</p><span></span>";
					// }
                    $('#'+leixing+'_'+id).innerHTML='<p>加入推广</p><span></span>';
                    if($('#'+leixing+'_'+id).parents('.goods-item').hasClass('goods-tg')){
                        $('#'+leixing+'_'+id).parents('.goods-item').removeClass('goods-tg');
                    }
                    $('#'+leixing+'_'+id).parents('.goods-item').find('.had-tg').remove();


					//new logic code end
                }
				else if(date1=="is_login" || date1=="error")
                {
                    //document.getElementById(leixing+"_"+id).innerHTML="加入失败";
                    layer.alert('请先登录再进行操作！', {
                        skin: 'layui-layer-lan' //样式类名
                        ,closeBtn: 0
                    }, function(){
                        location.href="/login";
                    });
                }
                else{
                    document.getElementById(leixing+"_"+id).innerHTML="加入失败";
                }

            }
            if($('#'+leixing+'_'+id).parents('.goods-item').hasClass('onClick')){
                $('#'+leixing+'_'+id).parents('.goods-item').removeClass('onClick');
            }

        }
    })
}


function showHintNew(str,leixing,id) {//this method add by denglei to transfer the logic of changing popularize status feature from asp to php
    $.ajax({
        type: "post",
        url: "/handle_popularize",
        data:{'act':str,'id':id},
        //contentType: "application/json",
        dataType: "text",
        cache:false,
        async: true,//同步
        success: function (date1) {
            //alert(date1);return;
            //layer.msg('nihaooaoaoa', {icon: 1, time: 2000});
            //alert(date1);

            if(date1=="is_login"/* || date1=="error"*/)
            {
                //document.getElementById(leixing+"_"+id).innerHTML="加入失败";
                layer.alert('请先登录再进行操作！', {
                    skin: 'layui-layer-lan' //样式类名
                    ,closeBtn: 1
                }, function(){
                    location.href="/login";
                });

                $('#'+leixing+'_'+id).html('<p>加入推广</p><span></span>');

            }
            else if(date1=="ok")//#由'加入推广'====>'加入成功'
			{	
                //document.getElementById(leixing+"_"+id).innerHTML="加入成功";//old logic code

				//new logic code begin
				var obj1 = document.getElementById(leixing+"_"+id);
				// if($(obj1).attr('data-page') === 'quanPage')
				// {
				//
				// 	obj1.innerHTML='<p>加入成功</p><span></span>';
				// }
				// else  if($(obj1).attr('data-page') === 'quanPage2')
				// {
				// 	obj1.innerHTML='<p>加入成功</p><span></span>';
				// }

                // obj1.innerHTML='<p><i></i>已加入推广</p><span></span>';
                $('#'+leixing+'_'+id).html('<p>取消推广</p><span></span>');
                $('#'+leixing+'_'+id).siblings('a').append('<div class="add_tg_status had-tg"><b><i></i><w>已加入</w></b><span></span></div>');
                if(!$('#'+leixing+'_'+id).parents('.goods-item').hasClass('goods-tg')){
                    $('#'+leixing+'_'+id).parents('.goods-item').addClass('goods-tg');
                };
				/*else
				{
					obj1.innerHTML="加入成功";
				}*/
				//new logic code end
            }
			else if(date1=="is_in")//由'加入成功'====>'已取消'
            {
                    //document.getElementById(leixing+"_"+id).innerHTML="已取消";//old logic code
					
					//new logic code begin
					var obj2 = document.getElementById(leixing+"_"+id);
					// if($(obj2).attr('data-page') === 'quanPage')
					// {
					//
					// 	obj2.innerHTML='<p>已取消</p><span></span>';
					// }
					// else  if($(obj2).attr('data-page') === 'quanPage2')
					// {
					// 	obj2.innerHTML='<p>已取消</p><span></span>';
					// }
					/*else
					{
						obj2.innerHTML="已取消";
					}*/
					//new logic code end

                obj2.innerHTML='<p>加入推广</p><span></span>';
                $('#'+leixing+'_'+id).siblings('a').find('.had-tg').remove();
                if($('#'+leixing+'_'+id).parents('.goods-item').hasClass('goods-tg')){
                    $('#'+leixing+'_'+id).parents('.goods-item').removeClass('goods-tg');
                }
            }
            else
            {
                //document.getElementById(leixing + "_" + id).innerHTML = "加入失败";
                var obj3 = document.getElementById(leixing + "_" + id);
                if($(obj3).attr('data-page') === 'quanPage')
                {

                    obj3.innerHTML='<p>操作失败</p><span></span>';
                }
                else  if($(obj3).attr('data-page') === 'quanPage2')
                {
                    obj3.innerHTML='<p>操作失败</p><span></span>';
                }
                obj3.innerHTML='<p>加入推广</p><span></span>';
                $('#'+leixing+'_'+id).siblings('a').find('.had-tg').remove();
                if($('#'+leixing+'_'+id).parents('.goods-item').hasClass('goods-tg')){
                    $('#'+leixing+'_'+id).parents('.goods-item').removeClass('goods-tg');
                }

            }
            if($('#'+leixing+'_'+id).parents('.goods-item').hasClass('onClick')){
                $('#'+leixing+'_'+id).parents('.goods-item').removeClass('onClick');
            }

        }
    })
}

function setup_fav_sendtime(str,leixing,id) {
    $.ajax({
        type: "get",
        url: "http://www.dataoke.com/ucenter/save.asp?act="+str+"&id="+id,
        contentType: "application/json",
        dataType: "text",
        async: true,//同步
        success: function (date1) {

            if(date1=="ok")
                document.getElementById(leixing+"_"+id).innerHTML="标记成功";
            else if(date1=="is_login")
            {
                //document.getElementById(leixing+"_"+id).innerHTML="标记失败";
                layer.alert('请先登录再进行操作！', {
                    skin: 'layui-layer-lan' //样式类名
                    ,closeBtn: 1
                }, function(){
                    location.href="/login";
                });
            }
            else
                document.getElementById(leixing+"_"+id).innerHTML="标记失败";

        }
    })
}

//function showHint1(str,leixing,id)
//{
//var xmlhttp;
////alert(id+"_"+i);
//if (str.length==0)
//  {
// // document.getElementById("txtHint").innerHTML="";
//  return;
//  }
//if (window.XMLHttpRequest)
//  {// code for IE7+, Firefox, Chrome, Opera, Safari
//  xmlhttp=new XMLHttpRequest();
//  }
//else
//  {// code for IE6, IE5
//  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
//  }
//xmlhttp.onreadystatechange=function()
//  {
//  if (xmlhttp.readyState==4 && xmlhttp.status==200)
//    {
//	//alert(xmlhttp.responseText);
//	if(xmlhttp.responseText=="ok")
//	   document.getElementById(leixing+"_"+id).innerHTML="加入成功";
//	else
//	   {
//	   if(xmlhttp.responseText=="is_in")
//	       document.getElementById(leixing+"_"+id).innerHTML="已存在";
//	   else if(xmlhttp.responseText=="is_login")
//	       {
//			   document.getElementById(leixing+"_"+id).innerHTML="加入失败";
//			   layer.alert('请先登录再进行操作！', {
//					skin: 'layui-layer-lan' //样式类名
//					,closeBtn: 0
//				}, function(){
//					 location.href="/login/";
//				});
//		   }
//	   else
//          document.getElementById(leixing+"_"+id).innerHTML="加入失败";
//	   }
//    }
//  }
//xmlhttp.open("GET","http://www.dataoke.com/ucenter/save.asp?act="+str+"&id="+id,true);
//xmlhttp.send();
//}

function showline(str,leixing,id)
{
    var xmlhttp;
//alert(id+"_"+i);
    if (str.length==0)
    {
        // document.getElementById("txtHint").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {

            document.getElementById(leixing+"_"+id).innerHTML=xmlhttp.responseText;

        }
    }
    xmlhttp.open("GET","http://www.dataoke.com/ucenter/save.asp?act="+str+"&id="+id,true);
    xmlhttp.send();
}

function delgoods(str,leixing,id)
{
    var xmlhttp;
    if (str.length==0)
    {
        // document.getElementById("txtHint").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            //alert(xmlhttp.responseText);
            if(xmlhttp.responseText=="ok")
            { document.getElementById(leixing+"_"+id).style.display="none";
                layer.msg('删除成功！', {icon: 1}); }
            else
            {
                layer.msg('亲，删除失败！', {icon: 2}); }
        }
    }
    xmlhttp.open("GET","/ucenter/save.asp?act="+str+"&id="+id,true);
    xmlhttp.send();
}

function copyText(obj)
{
    var rng = document.body.createTextRange();
    rng.moveToElementText(obj);
    rng.scrollIntoView();
    rng.select();
    rng.execCommand("Copy");
    rng.collapse(false);
} 

