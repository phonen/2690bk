
//领券直播点击统计
$('#jgqj1').click(function(){
    umDataStatistics("领券直播 点击最低价输入", "点击",'' ,'' , '');
});
$('#jgqj2').click(function(){
    umDataStatistics("领券直播 点击最高价输入", "点击",'' ,'' , '');
});
$('#yjqj').click(function(){
    umDataStatistics("领券直播 点击佣金输入", "点击",'' ,'' , '');
});
$('#xlqj').click(function(){
    umDataStatistics("领券直播 点击销量输入", "点击",'' ,'' , '');
});
$('.filter-submit').click(function(){
    umDataStatistics("领券直播 点击筛选", "点击",'' ,'' , '');
});

$('.copy_text').click(function(){
    var s = $(this).parents('.goods-item').index()+1;
    umDataStatistics("领券直播 商品点击复制", "点击",'' ,'' , '');
});
$('.quan_add_u').click(function(){
    var s = $(this).parents('.goods-item').index()+1;
    umDataStatistics("领券直播 商品加入推广", "点击",'' ,'' , '');
});

//商品分类
$('.goods-type-main a').click(function(){
    var t = $(this).html().split('<b>')[0];
    umDataStatistics("领券直播 " + t + "分类", "点击",'' ,'' , '');
});

//商品筛选选项
$('.filter-tag label').click(function(){
    var g = $(this).text();
    umDataStatistics("领券直播 " + g + "选项", "点击",'' ,'' , '');
});

//商品排序
$('.goods-pages-fil a').click(function(){
    var t = $(this).text();
    umDataStatistics("领券直播 " + t + "排序", "点击",'' ,'' , '');
});
//分页
$('.prev-page a').click(function(){
    umDataStatistics("领券直播 小分页上一页", "点击",'' ,'' , '');
});
$('.next-page a').click(function(){
    umDataStatistics("领券直播 小分页下一页", "点击",'' ,'' , '');
});

$('.goods-img img').click(function(){
    var s = $(this).parents('.goods-item').index()+1;
    umDataStatistics("领券直播 商品列表图片", "点击",'' ,'' , '');
});

$('.goods-tit').click(function(){
    var s = $(this).parents('.goods-item').index()+1;
    umDataStatistics("领券直播 商品列表标题", "点击",'' ,'' , '');
});

$('.quan_page_main a').click(function(){
    var s = $(this).html().split('<b>');
    umDataStatistics("领券直播 分页" + s + "", "点击",'' ,'' , '');
});
$('.quan_page_main select').click(function(){
    umDataStatistics("领券直播 更多分页", "点击",'' ,'' , '');
})