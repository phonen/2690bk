$(function(){
    //判断是否支持一键复制 0 不支持 1 支持
    var ClipboardSupport = 0;
    if(typeof Clipboard != "undefined"){
        ClipboardSupport = 1;
    }else{
        ClipboardSupport = 0;
    }
	//获取文案
    $('.singleCopyText').click(function(e){
        layer.closeAll();
        if(ClipboardSupport == 0){
            layer.msg('浏览器版本太低，请升级或更换浏览器后重新复制！',{
                    time: 2000
                }
            );
       }else{
            $.ajax({
				type: 'post',
				url: '/?a=ajax_getmes',
				data: {
					iid:$(this).attr('data')
					},
				cache: false,
				dataType:'json',
				success: function(req) {
					$("#textContent").html('【天猫】'
	            		+req.title+'\n【价格】原价'
	            		+req.price+'元  券后'
	            		+req.coupon_price+'元包邮\n【推荐理由】'
	            		+req.intro+'\n---------分割线--------\n长按复制这条信息 '
	            		+req.token+' ,打开【手机淘宝家乡版】即可领券并下单');
					showTextMes();
				},
				error: function(e) {
					console.log(e);
				}
			})
        }
    })
    //获取图片
	$('.copy-img').click(function() {
		var div = $("#wenan");
		//是否ajax图片
		var url = '/?a=gettpl&id=' + $(this).attr('data');
		$.ajax({
			type: 'get',
			url: url,
			cache: false,
			success: function(req) {
				div.html(req);
				div.find('img').css('width','80%');
				show_model();
			},
			error: function(e) {
				console.log(e);
			}
		});
	})
    //设置一键复制
    var copyFunction = function(){
        var clipboard = new Clipboard('#clickCopy');

        clipboard.on('success', function(e) {
            layer.msg('已复制',{time: 2000});
            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            layer.msg('复制失败，请手动复制！',{time: 2000});
            e.clearSelection();
        })
   }
    $('#clickCopy').click(function(){
		copyFunction();
	})
	//hover动态效果
	$('div.a').click(function() {
			$(this).children().addClass('fenlei_check').end().siblings().children().removeClass('fenlei_check');
		})
	
	$("#fenlei").click(function(){
		if($("#item_lei").css('display')=='none'){
			$(".main_lei").show("slow");
		}else{
			$("#item_lei").hide("slow");
		}
		return false;
	})
	$('#top_img').click(function(){
		$(this).attr('src','images/mobile/dingbu.gif');
	})
	function show_model(){
		$("#show_mes").show();
		$("body").css('overflow','hidden');
		$("header").css("position","relative");
		$("body").bind("touchmove",function(event){event.preventDefault()});
	}
	function showTextMes(){
		$("#show_text_mes").show();
		$("body").css('overflow','hidden');
		$("header").css("position","relative");
		$("body").bind("touchmove",function(event){event.preventDefault()});
		$("#textContent").unbind("touchmove");
	}
	$(".close_mes").click(function(){
		$("#show_mes").hide();
		$("body").css('overflow','');
		$("header").css("position","fixed");
		$("#show_text_mes").hide();
		$("body").unbind("touchmove");
	})
	$('.event_box_img').css('height',$('.event_box_img').css('width'));
	$("#cnzz_stat_icon_1261869629 img").attr('class','hidden');
	
})