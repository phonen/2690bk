
//鼠标划过时更新放单人,以及获取登录的个性推广设置
$('div.goods-item').on('mouseenter',function () {
    var goods_team_name = $(this).find('.goods_team_name');
    var tag=goods_team_name.attr('showtag');
    if(tag==undefined){
        var data_tk_zs_id=goods_team_name.attr('data_tk_zs_id');
        var url="/get_team_name";
        $.ajax({
            type: "POST",
            url: url,
            data:{'data_tk_zs_id':data_tk_zs_id},
            success: function (data) {
                goods_team_name.attr('showtag',1);//设置标记，已经获取数据不再获取
                data = JSON.parse(data);
                if(data.status==1){
                    goods_team_name.text(data.name);
                }else {
                    goods_team_name.text('');
                }

            }
        })
    }
}) ;
