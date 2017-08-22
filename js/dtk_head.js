/**
 * Created by xckj055 on 2016/12/14.
 */
/*$(function () {
    $.get('/header', function (ret) {
        $('#header_login').html(ret);
    });
});*/

//点击统计
function umDataStatistics(cate, action, label, value, eleId) {
	_czc.push(["_trackEvent", cate, action, label, value, eleId]);
}

// 顶部菜单鼠标移入移出
$('.nav-main li.cur').prev('li').addClass('no-border');
$('.nav-main li').on('mouseenter mouseleave', function(e) {
	if(!$(this).hasClass('cur')) {
		if(e.type == "mouseenter") {
			$(this).prev('li').addClass('no-border');
		}
		if(e.type == "mouseleave") {
			$(this).prev('li').removeClass('no-border')
		}
	}
});

//$(window).resize(function() {
//	sideBarfloat();
//});
//
////side-bar定位
//var sideBarfloat = function() {
//		$('.side-bar-item').css('right', '0');
//	}
//	//var sideBarfloat = function(){
//	//  $('.side-bar').css('left',$('.head-search').offset().left+1260);
//	//}
//sideBarfloat();

// fiter复选框
/*$('.goods-filter-main label').on('click',function(){
     if($(this).hasClass('checked')){
         $(this).removeClass('checked');
     }else{
         $(this).addClass('checked');
     }
 })*/

// 已选（清除选中）
/*$('.choice-item i').click(function(){
     $(this).parent('span').remove();
     if($('.choice-item i').length<=0){
     $('.goods-choice').remove()
     }
 })
 $('.clear-choice').click(function(){
     $('.goods-choice').remove()
 })*/

//顶部搜索栏
function FSubmit(e) {
	if(e == 13) {
		//var xuan = $('select#xuan').val();
		var keywords = $("input[name='keywords']").val();
		if(keywords != null || keywords != undefined || keywords != '') { //alert(1);
			//$('a#search_a_tag').trigger('click');
			document.getElementById('search_a_tag').click();
			//window.location.href = "<?=$this->createUrl('ticket/search')?>" + '?xuan='+xuan+'&keywords=' + keywords;
		}
	}
}

/* 搜索 */
$('#xuan span').click(function() {
	var selectStr = $(this).html() + '<i></i>';
	$('.sear-select').html(selectStr);
	if(!$(this).hasClass('cur')) {
		$('#xuan span').removeClass('cur');
		$(this).addClass('cur');
		$('.sear-select').html(selectStr);
	}
	$('.search-input').css('padding-left', ($('.sear-select').width() + 10) + "px");
	$('#xuan').addClass('hide');
})

$('.sear-select').click(function() {
	if($('#xuan').hasClass('hide')) {
		$('#xuan').removeClass('hide');
	} else {
		$('#xuan').addClass('hide');
	}
})
$('.select-area').mouseleave(function() {
	if(!$('#xuan').hasClass('hide')) {
		$('#xuan').addClass('hide');
	}
})

$('.search-input').css('padding-left', ($('.sear-select').width() + 10) + "px");

//剩余券百分比
sliderEm = function() {
	$('.goods-slider em').each(function() {
		$(this).width($(this).attr('data-width'));
	})
}
sliderEm();

// 返回顶部
function gotoTop(acceleration, stime) {
	acceleration = acceleration || 0.1;
	stime = stime || 10;
	var x1 = 0;
	var y1 = 0;
	var x2 = 0;
	var y2 = 0;
	var x3 = 0;
	var y3 = 0;
	if(document.documentElement) {
		x1 = document.documentElement.scrollLeft || 0;
		y1 = document.documentElement.scrollTop || 0;
	}
	if(document.body) {
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

$('.scrollTop').click(function() {
	gotoTop()
});

$(function() {
	//微信二维码显示
	$('.WeChat-code').click(function() {
		if($('#WeChat-code-img').attr('src')==''){
			$('#WeChat-code-img').attr('src','images/WeChat-code1.gif');
		}
		$('body').css('overflow', 'hidden');
		$('#WeChat-code-bg').css('display', 'block');
		$('#WeChat-code-img').css('display', 'inline-block');
		$('#WeChat-code-close').css('display', 'inline-block');
	});
	//微信二维码消失
	$('#WeChat-code-close').click(function() {
		$('body').css('overflow', 'visible');
		$('#WeChat-code-bg').css('display', 'none');
		$('#WeChat-code-img').css('display', 'none');
		$('#WeChat-code-close').css('display', 'none');
	})
})