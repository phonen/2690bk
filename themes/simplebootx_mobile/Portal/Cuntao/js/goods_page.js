function click_group_checkbox_label(checkboxLabel)
{
	var checkboxLabelId = checkboxLabel.id;
	if(checkboxLabelId == 'tqg_checkbox_label' || checkboxLabelId == 'jhs_checkbox_label')//tqg jhs group
	{
		var tqg_checkbox = document.getElementById('tqg_checkbox');
		var tqg_checkbox_label = document.getElementById('tqg_checkbox_label');
		var jhs_checkbox = document.getElementById('jhs_checkbox');
		var jhs_checkbox_label = document.getElementById('jhs_checkbox_label');
		if(checkboxLabelId == 'tqg_checkbox_label')//click tqg label
		{
			if($(tqg_checkbox_label).attr('class') == 'checked')
			{
				tqg_checkbox.checked = false;
				$(tqg_checkbox_label).attr('class','');
			}
			else
			{
				tqg_checkbox.checked = true;
				$(tqg_checkbox_label).attr('class','checked');
				jhs_checkbox.checked = false;
				$(jhs_checkbox_label).attr('class','');
			}
		}
		else if(checkboxLabelId == 'jhs_checkbox_label')//click jhs label
		{
			if($(jhs_checkbox_label).attr('class') == 'checked')
			{
				jhs_checkbox.checked = false;
				$(jhs_checkbox_label).attr('class','');
			}
			else
			{
				jhs_checkbox.checked = true;
				$(jhs_checkbox_label).attr('class','checked');
				tqg_checkbox.checked = false;
				$(tqg_checkbox_label).attr('class','');
			}
		}
	}
	else if(checkboxLabelId == 'tm_checkbox_label' || checkboxLabelId == 'jpmj_checkbox_label')//tm jpmj group
	{
		var tm_checkbox = document.getElementById('tm_checkbox');
		var tm_checkbox_label = document.getElementById('tm_checkbox_label');
		var jpmj_checkbox = document.getElementById('jpmj_checkbox');
		var jpmj_checkbox_label = document.getElementById('jpmj_checkbox_label');
		if(checkboxLabelId == 'tm_checkbox_label')//click tm label
		{
			if($(tm_checkbox_label).attr('class') == 'checked')
			{
				tm_checkbox.checked = false;
				$(tm_checkbox_label).attr('class','');
			}
			else
			{
				tm_checkbox.checked = true;
				$(tm_checkbox_label).attr('class','checked');
				jpmj_checkbox.checked = false;
				$(jpmj_checkbox_label).attr('class','');
			}
		}
		else if(checkboxLabelId == 'jpmj_checkbox_label')//click jpmj label
		{
			if($(jpmj_checkbox_label).attr('class') == 'checked')
			{
				jpmj_checkbox.checked = false;
				$(jpmj_checkbox_label).attr('class','');
			}
			else
			{
				jpmj_checkbox.checked = true;
				$(jpmj_checkbox_label).attr('class','checked');
				tm_checkbox.checked = false;
				$(tm_checkbox_label).attr('class','');
			}
		}
	}
	else if(checkboxLabelId == 'yfx_checkbox_label')//yfx
	{
		var yfx_checkbox = document.getElementById('yfx_checkbox');
		var yfx_checkbox_label = document.getElementById(checkboxLabelId);
		if($(yfx_checkbox_label).attr('class') == 'checked')
		{
			yfx_checkbox.checked = false;
			$(yfx_checkbox_label).attr('class','');
		}
		else
		{
			yfx_checkbox.checked = true;
			$(yfx_checkbox_label).attr('class','checked');
		}
	}
	else if(checkboxLabelId == 'haitao_checkbox_label'){
		var haitao_checkbox = document.getElementById('haitao_checkbox');
		var haitao_checkbox_label = document.getElementById(checkboxLabelId);
		if($(haitao_checkbox_label).attr('class') == 'checked')
		{
			haitao_checkbox.checked = false;
			$(haitao_checkbox_label).attr('class','');
		}
		else
		{
			haitao_checkbox.checked = true;
			$(haitao_checkbox_label).attr('class','checked');
		}

	}
	$('#search_goods_form').submit();
}



$(document).ready(function(){
	$("a.a_px").each(function(index,obj){
		var query_str = $.trim(window.location.search);
		query_str = query_str.replace('?','');
		if(query_str != '')
		{
			var query_str2 = '';
			var query_arr = query_str.split('&');
			for(var i=0;i<query_arr.length;i++)
			{
				if(query_arr[i].indexOf('cid=') != -1 || query_arr[i].indexOf('px=') != -1 || query_arr[i].indexOf('page=') != -1)
				{
					continue;
				}
				query_str2 += '&'+query_arr[i];
			}
			obj.href += query_str2;
		}
	});
	
	
	var pageTop = $('.goods-pages').offset().top;
	$(window).scroll(function(){
		// 排序漂浮
		if($(window).scrollTop() > pageTop){
			$('.goods-pages').addClass('fixed');
			$('.goods-pages-height').fadeIn(0);
		}else{
			$('.goods-pages').removeClass('fixed');
			$('.goods-pages-height').fadeOut(0);
		}
	});
	
	
	$("input.need_positive_float").blur
	( 
		function () 
		{ 
			var input_value = $.trim(this.value);
			if(input_value != '')
			{
				if((this.name == 'jgqj1' || this.name == 'jgqj2'))//价格区间
				{
					if(!is_positive_float_val(input_value))
					{
						//alert('价格区间输入值必须是0或正整数或正的浮点数！');
						this.value = '';
						return;
					}
					
					if(input_value.indexOf('.') != -1)
					{
						this.value = parseFloat(input_value).toFixed(2);
					}
					
					var jgqj1_val = $.trim($('#jgqj1').val());
					var jgqj2_val = $.trim($('#jgqj2').val());
					
					if(jgqj1_val != '' && jgqj2_val != '' && parseFloat(jgqj1_val)>parseFloat(jgqj2_val))
					{
						$('#jgqj1').val(jgqj2_val);
						$('#jgqj2').val(jgqj1_val);
					}
				}
				else if(this.name == 'yjqj')//佣金区间
				{
					if(!is_positive_float_val(input_value))
					{
						//alert('佣金区间输入值必须是0或正整数或正的浮点数！');
						this.value = '';
						return;
					}
					if(parseFloat(this.value)>100)
					{
						//alert('佣金区间输入的最大值不能超过100%！');
						this.value = 100;
						return;
					}
					if(input_value.indexOf('.') != -1)
						this.value = parseFloat(input_value).toFixed(2);
				}
				else if(this.name == 'xlqj' && !is_positive_integer_val(input_value))//销量区间
				{
					//alert('销量区间输入值必须是0或正整数！');
					this.value = '';
					return;
				}
			}
		}
	);


});

function remove_choice_item(flag)
{
	if(page_type == 'search_index' && (flag == 'k' || flag == 'all'))
	{
		window.location.href = '/qlist';
		return;
	}
	
	if(flag == 'cid' || flag == 'all')
	{
		if(document.getElementById('cid_hidden_field'))
			document.getElementById('cid_hidden_field').value = 0;
	}
	
	if(flag == 'yugao' || flag == 'all')
	{
		if(document.getElementById('yugao_hidden_field'))
			document.getElementById('yugao_hidden_field').value = 0;
	}
	
	if(flag == 'tqgjhs' || flag == 'all')
	{
		document.getElementById('tqg_checkbox').checked = false;
		document.getElementById('jhs_checkbox').checked = false;
	}
	
	if(flag == 'tmjpmj' || flag == 'all')
	{
		document.getElementById('tm_checkbox').checked = false;
		document.getElementById('jpmj_checkbox').checked = false;
	}
	
	if(flag == 'yfx' || flag == 'all')
	{
		document.getElementById('yfx_checkbox').checked = false;
	}
    if(flag == 'haitao' || flag == 'all')
    {
        document.getElementById('haitao_checkbox').checked = false;
    }
	if(flag == 'jgqj' || flag == 'all')
	{
		document.getElementById('jgqj1').value = '';
		document.getElementById('jgqj2').value = '';
	}
	
	if(flag == 'yjqj' || flag == 'all')
	{
		document.getElementById('yjqj').value = '';
	}
	
	if(flag == 'xlqj' || flag == 'all')
	{
		document.getElementById('xlqj').value = '';
	}
	$('#search_goods_form').submit();
}

function is_positive_float_val(str)
{
	var zero_start_pattern = /^0/;
	var zero_dot_start_pattern = /^0\./;
	if(zero_start_pattern.test(str) && str != '0' && !(zero_dot_start_pattern.test(str)))
	{
		return false;
	}
	
	var pattern =  /^\d+(\.\d+)?$/;
	return pattern.test(str);
}

function is_positive_integer_val(str)
{	
	
	var pattern =  /^[1-9][0-9]*$/;
	if(str == '0' || pattern.test(str))
	{
			return true;
	}
	return false;
}

$('.pxclick').on('click', function () {
    var px = $(this).attr('data-px');
    $('#pxvalue').val(px);
    $('#search_goods_form').submit();
});

//搜索表单提交时候过滤数据
$('#search_goods_form').submit(function () {
    //价格区间
    var jgqj1 = $('#jgqj1');
    var jgqj2 = $('#jgqj2');

    if (!is_positive_float_val(jgqj1.val())) {
        //alert('价格区间输入值必须是0或正整数或正的浮点数！');
		jgqj1.attr('disabled','disabled');
    }
    if (!is_positive_float_val(jgqj2.val())) {
        //alert('价格区间输入值必须是0或正整数或正的浮点数！');
		jgqj2.attr('disabled','disabled');
    }

    if (jgqj1.val().indexOf('.') != -1) {
        jgqj1.val(parseFloat(jgqj1.val()).toFixed(2));
    }
    if (jgqj2.val().indexOf('.') != -1) {
        jgqj2.val(parseFloat(jgqj2.val()).toFixed(2));
    }

    var jgqj1_val = $.trim(jgqj1.val());
    var jgqj2_val = $.trim(jgqj2.val());

    if (jgqj1_val != '' && jgqj2_val != '' && parseFloat(jgqj1_val) > parseFloat(jgqj2_val)) {
        jgqj1.val(jgqj2_val);
        jgqj2.val(jgqj1_val);
    }
    //佣金区间
    var yjqj = $('#yjqj');

    if (!is_positive_float_val(yjqj.val())) {
        //alert('佣金区间输入值必须是0或正整数或正的浮点数！');
		yjqj.attr('disabled','disabled');
    }
    if (parseFloat(yjqj.val()) > 100) {
        //alert('佣金区间输入的最大值不能超过100%！');
        yjqj.val(100);
    }
    if (yjqj.val().indexOf('.') != -1)
        yjqj.val(parseFloat(yjqj.val()).toFixed(2));


    //销量
    var xlqj = $('#xlqj');
    if (!is_positive_float_val(xlqj.val())) {
        //alert('区间输入值必须是0或正整数或正的浮点数！');
		xlqj.attr('disabled','disabled');
    }
    if (xlqj.val().indexOf('.') != -1)
        xlqj.val(parseFloat(xlqj.val()).toFixed(0));
});

