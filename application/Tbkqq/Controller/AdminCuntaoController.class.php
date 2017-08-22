<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Tbkqq\Controller;
use Common\Controller\AdminbaseController;
class AdminCuntaoController extends AdminbaseController {
	function _initialize() {
//		parent::_initialize();
	}
	//我的商品列表
	public function item(){

		$uid = sp_get_current_admin_id();
		$where_ands =array("uid='" . $uid . "'");
		$fields=array(
			'startdate'=> array("field"=>"add_time","operator"=>">="),
			'enddate'=> array("field"=>"add_time","operator"=>"<="),

			'item'=> array("field"=>"dtitle","operator"=>"like"),

		);
		if(IS_POST){
			foreach ($fields as $param =>$val){
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		else{
			foreach ($fields as $param =>$val){
				if (isset($_GET[$param]) && !empty($_GET[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_GET[$param];
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		$where= join(" and ", $where_ands);
		$count=M("CunItems")
			->where($where)
			->count();

		$page = $this->page($count, 20);

		$items = M("CunItems")->where($where)
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		$this->assign("items",$items);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		
		$this->item_items_fuc();
		$this->display();

	}
	public function item_items_fuc(){
		$rs = M("ItemsPreferentialType")->select();
		$this->assign("pre_type",$rs);
		$this->assign("formget",$_GET);
		$this->assign("post",I("post."));
	}
	//判断是否为链接，送出加工好的um_iid
	public function send_iid($url){
		$url = trim($url);
		if(stripos($url,"http")===false){
			return $url;
		}else{
			if(is_numeric($url))
				$iid = $url;
			else{
				$iid_data = array();
				$parameter = explode('&',end(explode('?',$url)));
				foreach($parameter as $val){
					$tmp = explode('=',$val);
					$tmp[0] = str_replace("amp;","",$tmp[0]);
					$tmp[1] = str_replace("amp;","",$tmp[1]);
					$iid_data[$tmp[0]] = $tmp[1];
				}
				foreach($iid_data as $val){
					if(get_item_info($val)){
						$iid = $val;
						break;
					}
				}
				
			}
			return $iid;
		}
	}
	//所有商品列表
	public function items(){
		$model = M("CunItems");
		$status = I("post.status",NULL);
		//$where_ands = array();
		$where_ands =array("status!=2");
		
		$sort = array(
			0=>"source",
		);
		$where_ands = $this->sort($sort,$where_ands);
		$where_ands = $this->search_item($where_ands);




		$where= join(" and ", $where_ands);


		$count=$model
			->where($where)
			->count();

		$page = $this->page($count, 20);

		$items = $model->where($where)->order("add_time desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();

		$items = $this->create_taokouling($items);
		$this->assign("items",$items);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		
		$this->item_items_fuc();
		$this->display();

	}
	/**
	 * 分类查询
	 * @author changeheart
	 * @param array分类数组
	 * @param array记录数组
	 */
	public function sort($namearr,$where_ands){
		foreach($namearr as $name){
			$status = I("post.{$name}",NULL);
			if(is_null($status) || $status==0){
				
			}else{
				$where_ands[]="{$name}={$status}";
			}
			
		}
		if(empty($where_ands)){
			$where_ands[]="1";
		}
		
		return $where_ands;
	}
	/**
	 * 搜索方法
	 * @author changeheart
	 * @patram array()数组
	 */
	public function search_item($where){
		$kw = I("post.kw");
		$kwkey = I("post.kwkey");
		if($kwkey == "item"){
			$kwarr = array(
				1 => array("key"=>"title","op"=>"like"),
				2 => array("key"=>"dtitle","op"=>"like"),
				3 => array("key"=>"num_iid","op"=>"like"),
			);
		}else if($kwkey == "user"){
			$kwarr = array(
				1 => array("key"=>"uid","op"=>"like"),
				2 => array("key"=>"uname","op"=>"like"),
			);
		}else{
			//$this->error("请勿修改请求信息");
			return array(1);
		}
		
		$arr = array();
		if($kw){
			foreach($kwarr as $val){
				array_push($arr,"{$val['key']} {$val['op']} '%{$kw}%'");
			}
		}
		$ac = join(" or ", $arr);
		if($ac == ''){
			$ac = 1;
		}
		array_push($where,"{$ac}");
		return $where;
	}
	//搜索
	public function search(){
		
		$kw = I("get.kw");
		$kw = $this->send_iid($kw);
		$where_ands = array();
		$fields=array(
				'id'=> array("field"=>"id","operator"=>"like"),
				'num_iid'=> array("field"=>"num_iid","operator"=>"like"),
		
				'title'=> array("field"=>"dtitle","operator"=>"like")
		);
		foreach ($fields as $param =>$val){
			$operator=$val['operator'];
			$field   =$val['field'];
			if($operator=="like"){
				$get="%$kw%";
			}
			array_push($where_ands, "$field $operator '$get'");
		}
		$where= join(" or ", $where_ands);
		$item_model = M("CunItems");
		$rs = $item_model->where($where)->select();
		if($rs){
			$this->assign("items",$rs);
		}else {
			$this->error("没有找到相关商品");
		}
		$this->display("/AdminCuntao/items");
	}
	
	//待审核商品
	public function item_dsh(){

		$where_ands =array("status='2'");
		$fields=array(
			'startdate'=> array("field"=>"add_time","operator"=>">="),
			'enddate'=> array("field"=>"add_time","operator"=>"<="),

			'item'=> array("field"=>"dtitle","operator"=>"like")
		);
		if(IS_POST){
			foreach ($fields as $param =>$val){
				if (isset($_POST[$param]) && !empty($_POST[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_POST[$param];
					$_GET[$param]=$get;
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		else{
			foreach ($fields as $param =>$val){
				if (isset($_GET[$param]) && !empty($_GET[$param])) {
					$operator=$val['operator'];
					$field   =$val['field'];
					$get=$_GET[$param];
					if($operator=="like"){
						$get="%$get%";
					}
					array_push($where_ands, "$field $operator '$get'");
				}
			}
		}
		$where= join(" and ", $where_ands);
		$items = M("CunItems")->where($where)->order("add_time DESC")->select();

		$this->assign("items",$items);
		$this->assign("formget",$_GET);
		$this->display();

	}
	//商品审核
	public function item_audit(){
		$status = $_REQUEST['status'];
		if($status == '1') $status = 'underway';
		$data['status'] = $status;
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));


			if (M("CunItems")->where("id=$id")->save($data)) {
				admin_record_operation(287,$id);
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}

		}

		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);

			$items = M("CunItems")->where("id in ($ids)")->save($data);
			if($items){
					admin_record_operation(287,$ids);
					$this->success("审核成功！");
			}
			else $this->error("审核失败！");
		}
	}


	public function item_add(){
		//查找商品类别表
		$shop_model = M("CunItemsType");
		$rs = $shop_model->order("type_id ASC")->where("type_id!=0")->select();
		$this->assign("type",$rs);
		$this->add_edit_fuc();
		$this->display();
	}


//添加提交新优惠券
	public function item_add_post(){
		if (IS_POST) {
			$item_model = M("CunItems");

			$item=I("post.item");
			$item['num_iid'] = trim($item['num_iid']);
			if(is_numeric($item['num_iid']))
				$iid = $item['num_iid'];
			else{
				$iid_data = array();
				$parameter = explode('&',end(explode('?',$item['num_iid'])));
				foreach($parameter as $val){
					$tmp = explode('=',$val);
					$tmp[0] = str_replace("amp;","",$tmp[0]);
					$tmp[1] = str_replace("amp;","",$tmp[1]);
					$iid_data[$tmp[0]] = $tmp[1];
				}
				//$iid_data = get_url_data($item['num_iid']);
				//if(is_numeric($iid_data['id']))$iid = $iid_data['id'];
				//else $this->error("这个商品链接id错误,添加失败！");
				foreach($iid_data as $val){
					if(get_item_info($val)){
						$iid = $val;
						break;
					}
				}
				
			}
			$iid = trim($iid);
			$item['status'] = '2';
			$item['add_time'] = time();
			$item['uid'] = sp_get_current_admin_id();
			$item['uname'] = $_SESSION['name'];
			$item['quanurl'] = str_replace('&amp;', '&', $item['quanurl']);
			$data = get_url_data($item['quanurl']);
			if($data['activity_id'] == "")$quan_id = $data['activityId'];
			else $quan_id = $data['activity_id'];

			$item['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_21582792_72310921&itemId=" . $iid ."&src=qhkj_dtkp&dx=1";
			if($iid != ""){
				$item_model->where("num_iid={$iid} and source!='2690'")->delete();
				$taoke = $item_model->where(array("num_iid"=>$iid))->find();

				if(!$taoke){
					$info = get_item_info($iid);
					for($i=0;$i<3;$i++){
						if(empty($info->zk_final_price)){
							$info = get_item_info($iid);
							break;
						}
					}
					if($info && !empty($info->zk_final_price)){
						$item['title'] = $info->title;
					$item['pic_url'] = $info->pict_url;
					if(!$item['price']){
						$item['price'] = $info->zk_final_price;
					}
					$item['nick'] = $info->nick;
					$item['sellerId'] = $info->seller_id;
					$item['volume'] = $info->volume;
					}else {
						$this->error("该商品没有优惠券或者不存在");
					}
					$item['num_iid'] = $iid;
					if(is_cun($item['num_iid'])){
						$item['is_cun']=1;
					}else{
						$item['is_cun']=2;
					}
					

					$item['quanurl'] = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] . "&activity_id=" . $quan_id;
					$item['type'] = '1';
					$item['source'] = '2690';
					$item['quan'] = $item['price']-$item['coupon_price'];
					$item['cun'] = '1';
					$coupon_info = get_coupon_info($item['quanurl']);
					if($coupon_info) $item1 = array_merge($item,$coupon_info);

					$result=$item_model->field("id,ordid,cate_id,orig_id,num_iid,title,intro,nick,sellerId,uid,uname,pic_url,price,click_url,volume,commission,coupon_price,coupon_rate,coupon_start_time,coupon_end_time,pass,status,ems,hits,mobile,isshow,likes,inventory,add_time,last_rate_time,is_collect_comments,cu,sex,isq,quan,quanurl,quan_condition,quan_surplus,quan_receive,cun,dtitle,source,is_cun,preferential_type")->add($item1);

					if ($result) {
						admin_record_operation(282,$iid);
						$this->success("添加成功！");
					} else {
						$this->error("添加失败！");
					}
				}
				else{
					if($taoke['source'] == '2690')
						$this->error("添加失败！已经存在该商品id");
					else {
						$info = get_item_info($iid);
						if($info && !empty($info->zk_final_price)){
						$item['title'] = $info->title;
					$item['pic_url'] = $info->pict_url;
					$item['price'] = $info->zk_final_price;
					$item['nick'] = $info->nick;
					$item['sellerId'] = $info->seller_id;
					$item['volume'] = $info->volume;
					}
					else {
						$this->error("找不到这个商品id信息,添加失败！");
					}
					

						$item['type'] = '1';
						$item['source'] = '2690';
						$item['cun'] = '1';
						$coupon_info = get_coupon_info($item['quanurl']);
						if($coupon_info) $item1 = array_merge($item,$coupon_info);
						$item1['id'] = $taoke['id'];
						$result=$item_model->save($item1);

						if ($result) {
							admin_record_operation(282,$iid);
							$this->success("添加成功！");
						} else {
							$this->error("添加失败！");
						}
					}
				}

			}
			else $this->error("添加失败！");
		}
	}



	//编辑商品
	public function item_edit(){
		//查找商品类别表
		$shop_model = M("CunItemsType");
		$rs = $shop_model->order("type_id ASC")->where("type_id!=0")->select();
		$this->assign("type",$rs);
		$this->add_edit_fuc();
		$id=  intval(I("get.id"));		
		$item = M("CunItems")->where("num_iid=$id")->find();
		$this->assign("item",$item);
		$this->display();
	}
	//编辑商品
	public function item_edit_post(){
		if (IS_POST) {
			$item=I("post.item");			//权重变更			$num_iid = $item['num_iid'];			$cun = $item['cun'];						$item_model = M('CunItems');						$rs = $item_model->where("cun={$cun}")->find();						if($rs){				$up = $item_model->where("cun>10 and cun<={$cun}")->setDec("cun");				if($up === false){					$this->error("更新出现错误");				}				$last = $item_model->where("cun=10")->setDec("cun",9);			}			//$it = $item_model->where("num_iid={$num_iid}")->save($data);			
			$result=M("CunItems")->save($item);
			if ($result!==false) {
				admin_record_operation(284,$item['num_iid']);
				$this->success("保存成功！");
			} else {
				$this->error("保存失败！");
			}
		}
	}

	public function item_dsh_post(){
		set_time_limit(0);
		if(IS_POST){
			$ids = $_POST['ids'];
			$username=I("post.username");
			$proxys = M("TbkqqTaokeMedia")->where(array("username"=>$username,'status'=>'1'))->select();
			$proxys1 = M("TbkqqTaokeMedia")->where(array("username"=>'15219198262','status'=>'1'))->select();
			if($proxys1)$proxys = array_merge($proxys,$proxys1);
			foreach($ids as $id){
				$no = M("TbkqqTaokeItem")->where(array("status"=>"1"))->max("no");
				$no = $no?$no:0;
				$no = $no+1;
				$item = M("TbkqqTaokeItem")->where(array("id"=>$id))->find();
//				$no1 = M("TbkqqTaokeItem")->where(array("id"=>$id))->getField("no");
				$no1 = $item['no'];
				$iid = $item['iid'];
				if($no1)$data['no'] = $no1;
				else $data['no'] = $no;
				$data['status'] = '1';
				M("TbkqqTaokeItem")->where(array("id"=>$id))->save($data);

				foreach($proxys as $proxy){
					$itemurl = array();
					$proxyid = substr($proxy['proxy'],strlen(C('SITE_APPNAME')));
					$itemurl = M("TbkqqTaokeItemurl")->where(array("iid"=>$iid,"proxyid"=>$proxyid))->find();
					if($itemurl){
						continue;
					}
					else {
						$itemurl['iid'] = $iid;
						$data = get_url_data($item['quan_link']);
						if($data['activity_id'] == "")$quan_id = $data['activityId'];
						else $quan_id = $data['activity_id'];
						$itemurl['qurl'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=".$item['type'];
						$itemurl['shorturl'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=" . $item['type'];

						//if($proxyid == '001' || $proxyid == '0001'){
                            $token_data = array();
                            $token_data['logo'] = $item['img'];
                            $token_data['text'] = $item['item'];
                            $token_data['url'] = $itemurl['qurl'];
						$taotokenstr = '';
						$taotokenstr = get_taotoken($token_data);
						if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);
						if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);
						if($taotokenstr == '')$taotokenstr = get_taotoken($token_data);

						//   $itemurl['quankl'] = get_taotoken($token_data);
						$itemurl['quankl'] = $taotokenstr;
                        //    $itemurl['quankl'] = get_taotoken($token_data);
                        //}

						$itemurl['proxyid'] = $proxyid;
						$itemurl['itime'] = date("Y-m-d H:i:s",time());
						unset($itemurl['id']);
						M("TbkqqTaokeItemurl")->add($itemurl);
					}

				}
			}
			$this->success("正式推广成功！");
		}
	}

	public function item_post(){
		if(IS_POST){
			$ids = $_POST['nos'];
			foreach ($ids as $id => $r) {
				$data['no'] = $r;
				M("CunItems")->where(array("id" => $id))->save($data);
			}
			$this->success("编号更新成功！");
		}
	}
	//商品曝光权限操作
	public function item_cun(){
		set_time_limit(0);
		echo 1;
	}
	//删除商品
	public function item_delete(){
        set_time_limit(0);
		$data['status']='-1';
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));


				if (M("CunItems")->where("num_iid=$id")->delete()) {
					admin_record_operation(286,$id);
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}

		}

		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);


				if (M("CunItems")->where("id in ($ids)")->delete()) {
					$this->success("删除成功！");
				} else {
					$this->error("删除失败！");
				}
		}
	}


	protected function get_coupon_info($url){

		$header[] = "Accept-Language: zh-CN,zh;q=0.8";
		$header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_REFERER, $tu);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($ch, CURLOPT_NOBODY,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
		curl_setopt($ch, CURLOPT_MAXREDIRS,2);
		$out = curl_exec($ch);
		$dd =  curl_getinfo($ch);
		curl_close($ch);
		$host = parse_url($dd['url'], PHP_URL_HOST);
		if($host == 'login.taobao.com'){
			$urldata = get_url_data($dd['url']);
			$url = urldecode($urldata['redirectURL']);

			$url = "http://shop.m.taobao.com/shop/coupon.htm?" . parse_url($url, PHP_URL_QUERY);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			//curl_setopt($ch, CURLOPT_REFERER, $tu);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			//curl_setopt($ch, CURLOPT_NOBODY,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
			curl_setopt($ch, CURLOPT_MAXREDIRS,2);
//				$out = curl_exec($ch);
//				$dd =  curl_getinfo($ch);
			curl_close($ch);
		}
		$quanurl = $dd['url'];

		$out = http_get_content($url);
		preg_match_all('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))/', $html, $effectdate);
		if (empty($effectdate)) {
			$this->error("添加券日期失败！");
			exit;
		}
		$item['coupon_start_time'] = strtotime($effectdate[0][0]);
		$item['coupon_end_time'] = strtotime($effectdate[0][1]);

		if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
			$item['Quan_surplus'] = $match[1];
		if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
			$item['Quan_receive'] = $match[1];

		if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))
			$item['Quan_condition'] = $match[1];

		//$item['quanurl'] = $quanurl;
		//$item['Quan_surplus'] = $quan_surplus;
		//$item['Quan_receive'] = $quan_receive;
		preg_match('/<dt>\d*/', $out, $quan);
		if (empty($quan)) {
			$this->error("券failed");
			exit;
		}
	}
	public function items_video(){
		$this->display();
	}
	//添加修改页面通用电费方法
	public function add_edit_fuc(){
		$rs = M("ItemsPreferentialType")->select();
		if($rs){
			$this->assign("pre_type",$rs);
		}
	}
	//生成一堆的淘口令
	public function create_taokouling($data){
		set_time_limit(0);
		foreach($data as $val){

			$token_data = array(
				"logo"=>$val["pic_url"],
				"text"=>$val['title'],
				"url"=>$val["click_url"],
			);
			$data[array_search($val,$data)]['token'] = get_taotoken($token_data);
		}
		return $data;
	}
	//生成单个淘口令
	public function create_one_taokouling($data){
		$token_data = array(
			"logo"=>$data['pic_url'],
			"text"=>$data['title'],
			"url"=>$data['click_url'],
		);
		return get_taotoken($token_data);
	}
	public function test(){
		echo "恭喜进入";
	}
}