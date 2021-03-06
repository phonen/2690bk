<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------

/**
 * 查询文章列表，支持分页或不分页
 * Author: Raifner <81818832@qq.com> 2016-2-22
 * @param string $type 查询类型,可以为'cid'=根据分类文章分类ID 获取该分类下所有文章（包含子分类中文章）,可以为'keyword'=根据关键字 搜索文章（包含子分类中文章）
 * @param string $v 当查询类型为'cid'或'keyword'时,待搜索的值
 * @param string $tag  查询标签，以字符串方式传入,例："cid:1,2;field:post_title,post_content;limit:0,8;order:post_date desc,listorder desc;where:id>0;"<br>
 *  ids:调用指定id的一个或多个数据,如 1,2,3<br>
 * 	cid:数据所在分类,可调出一个或多个分类数据,如 1,2,3 默认值为全部,在当前分类为:'.$cid.'<br>
 * 	field:调用post指定字段,如(id,post_title...) 默认全部<br>
 * 	limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始),使用分页时无效
 * 	order:排序方式，如：post_date desc<br>
 *	where:查询条件，字符串形式，和sql语句一样
 * @param array $where 查询条件，（暂只支持数组），格式和thinkphp where方法一样；
 * @param bool $ispage 是否分页
 * @param int $pagesize 每页条数.
 * @param array $pagesetting 分页设置<br>
 * 	参数形式：<br>
 * 	array(<br>
 * 		&nbsp;&nbsp;"listlong" => "9",<br>
 * 		&nbsp;&nbsp;"first" => "首页",<br>
 * 		&nbsp;&nbsp;"last" => "尾页",<br>
 * 		&nbsp;&nbsp;"prev" => "上一页",<br>
 * 		&nbsp;&nbsp;"next" => "下一页",<br>
 * 		&nbsp;&nbsp;"list" => "*",<br>
 * 		&nbsp;&nbsp;"disabledclass" => ""<br>
 * 	)
 * @param string $pagetpl 以字符串方式传入,例："{first}{prev}{liststart}{list}{listend}{next}{last}"
* @return array 包括分页的文章列表<br>
 * array(<br>
 * 	&nbsp;&nbsp;"posts"=>"",//文章列表，array<br>
 * 	&nbsp;&nbsp;"page"=>""//分页html<br>
* )
 */
function sp_post($type=null,$v=null,$tag,$where=array(),$ispage,$pagesize=20,$pagesetting=array(),$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}'){
    $where=is_array($where)?$where:array();
    $tag=sp_param_lable($tag);
    $field = !empty($tag['field']) ? $tag['field'] : '*';
    $limit = !empty($tag['limit']) ? $tag['limit'] : '';
    $order = !empty($tag['order']) ? $tag['order'] : 'post_date';
    switch($type){
        case 'keyword':
            $where['post_title'] = array('like','%' . $v . '%');//关键字
        case 'cid':
            $cid=intval($v);
            $catids=array();
            $terms=M("Terms")->field("term_id")->where("status=1 and ( term_id=$cid OR path like '%-$cid-%' )")->order('term_id asc')->select();
            foreach($terms as $item){
                $catids[]=$item['term_id'];
            }
            if(!empty($catids)){
                $catids=implode(",", $catids);
                $catids="cid:$catids;";
            }else{
                $catids="";
            }
            $tag['cid']=$catids;//重新生成条件
        default:
    }
    //根据参数生成查询条件
    $where['status'] = array('eq',1);
    $where['post_status'] = array('eq',1);
    if (!empty($tag['cid'])) {
        $where['term_id'] = array('in',$tag['cid']);
    }
    if (!empty($tag['ids'])) {
        $where['object_id'] = array('in',$tag['ids']);
    }
    if (!empty($tag['where'])) {
        $where['_string'] = $tag['where'];
    }
    $join = "".C('DB_PREFIX').'posts as b on a.object_id =b.id';
    $join2= "".C('DB_PREFIX').'users as c on b.post_author = c.id';
    $rs= M("TermRelationships");
    if($ispage){
        //使用分页
		//需要处理重复字段(排除c表id字段)				//$field=($field=='*')?'a.*,b.*,user_login,user_pass,user_nicename,user_email,user_url,avatar,sex,birthday,signature,last_login_ip,last_login_time,create_time,user_activation_key,user_status,score,user_type,coin,mobile':$field;
		//$sub=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->limit($limit)->buildsql();
		//$totalsize=M()->table($sub.' d')->count();
        $totalsize=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->count();
		import('Page');
		if ($pagesize == 0) {
			$pagesize = 20;
		}
		$PageParam = C("VAR_PAGE");
		$page = new \Page($totalsize,$pagesize);
		$page->setLinkWraper("li");
		$page->__set("PageParam", $PageParam);
		$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		//$sub=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->order($order)->limit($limit)->buildsql();
		//$posts=M()->table($sub.' d')->limit($page->firstRow . ',' . $page->listRows)->select();
		$posts=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
		$content['posts']=$posts;
		$content['page']=$page->show('default');
		$content['count']=$totalsize;
        return $content;
    }else{
        //不使用分页
        $posts=$rs->alias("a")->join($join)->join($join2)->field($field)->where($where)->order($order)->limit($limit)->select();
        return $posts;
    }
}

/**
 * 查询文章列表，不做分页
 * @param 见函数sp_post
 * @return  array 返回查询的文章列表(二维数组)
 */
function sp_sql_posts($tag,$where=array()){
    return sp_post(null,null,$tag,$where,false);
}


/**
 * 查询文章列表，支持分页
 * @param 见函数sp_post
 * @return array 包括分页的文章列表<br>
 */

function sp_posts($tag,$where=array(),$pagesize=20,$pagesetting=array(),$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}'){
    return sp_post(null,null,$tag,$where,true,$pagesize,$pagesetting,$pagetpl);
}

/**
 * 功能：根据分类文章分类ID 获取该分类下所有文章（包含子分类中文章），调用方式同sp_sql_posts
 * @param 见函数sp_post
 * @return  array 返回查询的文章列表(二维数组),不含分页
 */

function sp_sql_posts_bycatid($cid,$tag,$where=array()){
    return sp_post('cid',$cid,$tag,$where,false);
}

/**
 * 文章分页查询方法
 * @param 见函数sp_post
 * @return array 带分页数据的文章列表(多维数组)
 */

function sp_sql_posts_paged($tag,$pagesize=20,$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}'){
    return sp_post(null,null,$tag,null,true,$pagesize,null,$pagetpl);
}

/**
 * 功能：根据关键字 搜索文章（包含子分类中文章），已经分页，调用方式同sp_sql_posts_paged<br>
 * @param 见函数sp_post
 * @return array 带分页数据的文章列表(多维数组)
 */
function sp_sql_posts_paged_bykeyword($keyword,$tag,$pagesize=20,$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}'){
    return sp_post('keyword',$keyword,$tag,$where,true,$pagesize,null,$pagetpl);
}

/**
 * 功能：根据分类文章分类ID 获取该分类下所有文章（包含子分类中文章），已经分页，调用方式同sp_sql_posts_paged<br>
 * @param 见函数sp_post
 * @return array 带分页数据的文章列表(多维数组)
 */

function sp_sql_posts_paged_bycatid($cid,$tag,$pagesize=20,$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}'){
    return sp_post('cid',$cid,$tag,$where,true,$pagesize,null,$pagetpl);
}
/**
 * 获取指定id的文章
 * @param int $tid 分类表下的tid.
 * @param string $tag 查询标签，以字符串方式传入,例："field:post_title,post_content;"<br>
 *	field:调用post指定字段,如(id,post_title...) 默认全部<br>
 * @return array 返回指定id的文章
 */
function sp_sql_post($tid,$tag){
	$where=array();
	$tag=sp_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';

	//根据参数生成查询条件
	$where['status'] = array('eq',1);
	$where['tid'] = array('eq',$tid);

	$join = "".C('DB_PREFIX').'posts as b on a.object_id =b.id';
	$join2= "".C('DB_PREFIX').'users as c on b.post_author = c.id';
	$term_relationships_model= M("TermRelationships");

	$post=$term_relationships_model->alias("a")->join($join)->join($join2)->field($field)->where($where)->find();
	return $post;
}

/**
 * 获取指定条件的页面列表
 * @param string $tag 查询标签，以字符串方式传入,例："ids:1,2;field:post_title,post_content;limit:0,8;order:post_date desc,listorder desc;where:id>0;"<br>
 * 	ids:调用指定id的一个或多个数据,如 1,2,3<br>
 * 	field:调用post指定字段,如(id,post_title...) 默认全部<br>
 * 	limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始)<br>
 * 	order:排序方式，如：post_date desc<br>
 *	where:查询条件，字符串形式，和sql语句一样
 * @return array 返回符合条件的所有页面
 */
function sp_sql_pages($tag){
	$where=array();
	$tag=sp_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';
	$limit = !empty($tag['limit']) ? $tag['limit'] : '';
	$order = !empty($tag['order']) ? $tag['order'] : 'post_date';

	//根据参数生成查询条件
	$where['post_status'] = array('eq',1);
	$where['post_type'] = array('eq',2);
	
	if (isset($tag['ids'])) {
		$where['id'] = array('in',$tag['ids']);
	}
	
	if (isset($tag['where'])) {
		$where['_string'] = $tag['where'];
	}

	$posts_model= M("Posts");

	$pages=$posts_model->field($field)->where($where)->order($order)->limit($limit)->select();
	return $pages;
}

/**
 * 获取指定id的页面
 * @param int $id 页面的id
 * @return array 返回符合条件的页面
 */
function sp_sql_page($id){
	$where=array();
	$where['id'] = array('eq',$id);

	$rs= M("Posts");
	$post=$rs->where($where)->find();
	return $post;
}


/**
 * 返回指定分类
 * @param int $term_id 分类id
 * @return array 返回符合条件的分类
 */
function sp_get_term($term_id){
	
	$terms=F('all_terms');
	if(empty($terms)){
		$term_obj= M("Terms");
		$terms=$term_obj->where("status=1")->select();
		$mterms=array();
		
		foreach ($terms as $t){
			$tid=$t['term_id'];
			$mterms["t$tid"]=$t;
		}
		
		F('all_terms',$mterms);
		return $mterms["t$term_id"];
	}else{
		return $terms["t$term_id"];
	}
}
/**
 * 返回指定分类下的子分类
 * @param int $term_id 分类id
 * @return array 返回指定分类下的子分类
 */
function sp_get_child_terms($term_id){

	$term_id=intval($term_id);
	$term_obj = M("Terms");
	$terms=$term_obj->where("status=1 and parent=$term_id")->order("listorder asc")->select();
	
	return $terms;
}
/**
 * 返回符合条件的所有分类
 * @param string $tag 查询标签，以字符串方式传入,例："ids:1,2;field:term_id,name,description,seo_title;limit:0,8;order:path asc,listorder desc;where:term_id>0;"<br>
 * 	ids:调用指定id的一个或多个数据,如 1,2,3
 * 	field:调用terms表里的指定字段,如(term_id,name...) 默认全部，用*代表全部
 * 	limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始)
 * 	order:排序方式，如：path desc,listorder asc<br>
 * 	where:查询条件，字符串形式，和sql语句一样
 * 
 * @return array 返回符合条件的所有分类
 * 
 */
function sp_get_terms($tag){
	
	$where=array();
	$tag=sp_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';
	$limit = !empty($tag['limit']) ? $tag['limit'] : '';
	$order = !empty($tag['order']) ? $tag['order'] : 'term_id';
	
	//根据参数生成查询条件
	$where['status'] = array('eq',1);
	
	if (isset($tag['ids'])) {
		$where['term_id'] = array('in',$tag['ids']);
	}
	
	if (isset($tag['where'])) {
		$where['_string'] = $tag['where'];
	}
	
	$term_obj= M("Terms");
	$terms=$term_obj->field($field)->where($where)->order($order)->limit($limit)->select();
	return $terms;
}

/**
 * 获取Portal应用当前模板下的模板列表
 * @return array
 */
function sp_admin_get_tpl_file_list(){
	$template_path=C("SP_TMPL_PATH").C("SP_DEFAULT_THEME")."/Portal/";
	$files=sp_scan_dir($template_path."*");
	$tpl_files=array();
	foreach ($files as $f){
		if($f!="." || $f!=".."){
			if(is_file($template_path.$f)){
				$suffix=C("TMPL_TEMPLATE_SUFFIX");
				$result=preg_match("/$suffix$/", $f);
				if($result){
					$tpl=str_replace($suffix, "", $f);
					$tpl_files[$tpl]=$tpl;
				}else if(preg_match("/\.php$/", $f)){
				    $tpl=str_replace($suffix, "", $f);
				    $tpl_files[$tpl]=$tpl;
				}
			}
		}
	}
	return $tpl_files;
}
function get_taotoken($data){

    Vendor('TaobaoApi.TopSdk');
    date_default_timezone_set('Asia/Shanghai');
    $c = new TopClient;
    $token = C('TOKEN');
    $key = array_rand($token);
    //$c->appkey = C('TOKEN_APPKEY');
    //$c->secretKey = C('TOKEN_SECRETKEY');
    $c->appkey = $token[$key]['TOKEN_APPKEY'];
    $c->secretKey = $token[$key]['TOKEN_SECRETKEY'];
    $c->format = 'json';
    $req = new WirelessShareTpwdCreateRequest;
    $tpwd_param = new IsvTpwdInfo;
    $tpwd_param->ext="{\"xx\":\"xx\"}";
    $tpwd_param->logo=$data['logo'];
    $tpwd_param->text=$data['text'];
    $tpwd_param->url=$data['url'];
    $tpwd_param->user_id="24234234234";
    $req->setTpwdParam(json_encode($tpwd_param));
    $resp = $c->execute($req);
//    print_r($resp);
    return $resp->model;
}

function http_get_content($url, $cache = false){
	// 定义当前页面请求的cache key
	$key = md5($url);
	// 如果使用cache时只读一次
	if($cache){
		$file_contents = $_SESSION[$key];
		if(!empty($file_contents)) return $file_contents;
	}

	// 通过curl模拟请求页面
	$ch = curl_init();
	// 设置超时时间
	$timeout = 30;
	curl_setopt($ch, CURLOPT_URL, $url);
	// 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));
	curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
	//curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);

	curl_close($ch);

	// 匹配出当前页的charset
	$charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";
	//$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";

	// 非utf8编码时转码
	if($charset != 'utf-8'){
		$file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);
	}
	// 将结果记录到session中，方便下次直接读取
	$_SESSION[$key] = $file_contents;

	return $file_contents;
}

function http_post_content($url, $data,$cache = false){
	// 定义当前页面请求的cache key
	$key = md5($url);
	// 如果使用cache时只读一次
	if($cache){
		$file_contents = $_SESSION[$key];
		if(!empty($file_contents)) return $file_contents;
	}

	// 通过curl模拟请求页面
	$ch = curl_init();
	// 设置超时时间
	$timeout = 30;
	curl_setopt($ch, CURLOPT_URL, $url);
	// 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));
	curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
	//curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$file_contents = curl_exec($ch);

	curl_close($ch);

	// 匹配出当前页的charset
	$charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";
	//$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";

	// 非utf8编码时转码
	if($charset != 'utf-8'){
		$file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);
	}
	// 将结果记录到session中，方便下次直接读取
	$_SESSION[$key] = $file_contents;

	return $file_contents;
}

function get_coupon_info($url){

	$out = http_get_content($url);
	preg_match_all('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))/', $out, $effectdate);
	if ($effectdate) {
		$item['coupon_start_time'] = strtotime($effectdate[0][0]);
		$item['coupon_end_time'] = strtotime($effectdate[0][1]);
	}


	if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
		$item['quan_surplus'] = $match[1];
	if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
		$item['quan_receive'] = $match[1];

	if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
		$item['quan_condition'] = $match[1];

	//$item['quanurl'] = $quanurl;
	//$item['Quan_surplus'] = $quan_surplus;
	//$item['Quan_receive'] = $quan_receive;
	if(preg_match('/<dt>\d*/', $out, $match)){
		$quanprice = explode("<dt>", $match[0]);
		$item['quan'] = ($quanprice[1]);
	}
	return $item;

}

function get_coupon_info_v1($url){
	$out = http_get_content($url);
	$json = json_decode($out,true);
	
	if($json['result']['retStatus'] == '0'){
		$item['quan'] = $json['result']['amount'];
		$item['coupon_end_time'] = strtotime($json['result']['effectiveEndTime']);
		$item['coupon_start_time'] = strtotime($json['result']['effectiveStartTime']);
		//return $item;
		return true;
	}
	else{
//		echo $url;
	print_r($json);
		return false;
	} 
}

function is_cun($iid){
	$url = "https://cunlist.taobao.com/?q=" . $iid;
	$content = http_get_content($url);
	if(preg_match('/<div class=\"b1\">.*<\/div>/',$content,$match)){
		return true;
	}
	else return false;
}

function get_url_data($str){
	$data = array();
	$parameter = explode('&',end(explode('?',$str)));
	foreach($parameter as $val){
		$tmp = explode('=',$val);
		$data[$tmp[0]] = $tmp[1];
	}
	return $data;
}

function get_item_info($iid){
    Vendor('TaobaoApi.TopSdk');
    date_default_timezone_set('Asia/Shanghai');
    $c = new TopClient;
    
    $token = C('TOKEN');
    $key = array_rand($token);
    $c->appkey = $token[$key]['TOKEN_APPKEY'];
    $c->secretKey = $token[$key]['TOKEN_SECRETKEY'];
    
    $c->format = 'json';

    $req = new TbkItemInfoGetRequest;
    $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,nick,seller_id,volume");
    $req->setPlatform("1");
    $req->setNumIids($iid);
    $resp = $c->execute($req);
    
    //dump($resp);
    return $resp->results->n_tbk_item[0];
    
}
