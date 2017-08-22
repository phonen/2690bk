<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\AdminbaseController;

class AdminHaozhuController extends AdminbaseController{
	protected $items;
	protected $users;
	
	//预定义模块
	function _initialize() {
		parent::_initialize();
		$this->items = D("Portal/HaozhuItems");
		$this->users = D("Portal/HaozhuUsers");
	}
	
	//主页
	function index(){
		$this->_list();
		$this->display();
	}
	
	//排序方法
	function _list(){
		$status = I("post.status",null);
		$uid = sp_get_current_admin_id();
		
		$where_ands = array();
		
		$sort = array(
			0=>"status",
			1=>"top_status",
			2=>"server_status",
		);
		$where_ands = $this->sort($sort,$where_ands);
		$where_ands = $this->search($where_ands);
		$where_ands[] = "uid = {$uid}";
		
		$where= join(" and ", $where_ands);
			
		$count=$this->items->where($where)->count();
			
		$page = $this->page($count, 20);
			
		$rankarr = I("post.rank",array(0=>array('name'=>'status','status'=>1)));
		// $ranks = array();
		// foreach ($rankarr as $ran=>$value){
		// 	$ranks[] = array(
		// 		'name'=>$value['name'],
		// 		'status'=>$value['status'],
		// 	);
		// }
		$rank = $this->rank($rankarr);

		$posts=$this->items
		->where($where)->limit($page->firstRow . ',' . $page->listRows)
		->order("top_status DESC,time_add DESC,{$rank}")
		->select();
		//echo $this->items->getLastSql();
		
		foreach($posts as $value){
			$rqe = M("HaozhuJudge")->where("num_iid={$value['num_iid']}")->order("time_judge DESC")->find();
			if($rqe){
				$posts[array_search($value,$posts)]['j']=$rqe;
			}
		}
		
		
		$users_obj = M("Users");
		$users_data=$users_obj->field("id,user_login")->select();
		
		$this->assign("user",$users_data);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		unset($_GET[C('VAR_URL_PARAMS')]);
		$this->assign("formget",$_POST);
		$this->assign("posts",$posts);
		$this->assign("post",I("post."));
		
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
	 * 排序查询
	 * @author changeheart
	 * @param array 排序内容
	 */
	public function rank($namearr){
		if(empty($namearr)){
			return 'time_add DESC';
		}
		$where = "";
		$status = array(
			0=>"ASC",
			1=>"DESC",
		);
		foreach($namearr as $name){
			$where .= "{$name['name']} {$status[$name['status']]},";
		}
		$where = trim($where,",");
		
		return $where;
	}
	/**
	 * 获取节点
	 * @author changeheart
	 * @param
	 */
	public function _tree(){
		$id = I("post.status");
		$mes = $this->feedback_type_model->select();
		$tree = new \Tree();
		foreach($mes as $val){
			$r['id'] = $val['id'];
			$r['name'] = $val['type_name'];
			$r['selected']=$id;
			$array[] = $r;
		}
		$tree->init($array);
		$str = "<option value='\$id' \$selected>\$spacer\$name</option>";
		$danoption = $tree->get_tree(0, $str);
		$this->assign("danoption",$danoption);
		
	}
	/**
	 * 修改页面
	 * @author changeheart
	 * @param
	 */
	/*
	public function edit(){
		$iid = I("get.id");
		$mes = $this->items
		->where("num_iid = {$iid}")
		->find();
		if($mes){
			$users_data=M("Users")->field("id,user_login")->select();
			$this->assign("user",$users_data);
			$this->assign("post",$mes);
			$this->display();
		}else{
			$this->error("网络错误，请不要修改url地址");
		}
		
	}
	*/
	/**
	 * 个人信息添加_修改页面
	 * @author changeheart
	 * @param
	 */
	public function person_edit(){
		$id = sp_get_current_admin_id();
		$mes = I("post.person",NULL);
		if(is_null($mes)){
			$this->error("请填写信息");
			return;
		}
		$rs = $this->users->where("id={$id}")->find();
		$data = array(
			"id"=>$id,
			"name"=>$mes['name'],
			"phone"=>$mes['phone'],
			"contact"=>$mes['contact'],
			"contact_type"=>$mes['contact_type'],
			"edit_time"=>date("Y-m-d H:i:s",time()),
			"status"=>1,
			"type"=>19,
		);
		if($rs){
			$mes = $this->users->where("id={$id}")->save($data);
		}else{
			$data["add_time"] = date("Y-m-d H:i:s",time());
			$mes = $this->users->add($data);
		}
		
		if($mes){
			admin_record_operation(321,$data);
			$this->success("信息修改成功");
		}else{
			$this->error("信息修改失败");
		}
	}
	/**
	 * 个人信息页面
	 * @author changeheart
	 * @param
	 */
	public function person(){
		$id = sp_get_current_admin_id();
		$mes = $this->users
		->where("id = {$id}")
		->find();
		if($mes){
			$this->assign("mes",$mes);
		}else{
			$user = M("Users")->where("id={$id}")->find();
			$mes_a = array(
				"name"=>$user["user_login"],
				"phone"=>$user["mobile"],
				"status"
			);
			if(!empty($user["qq"])){
				$mes_a["contact"]=$user["qq"];
				$mes_a["contact_type"]="qq";
			}
			$this->assign("mes",$mes_a);
		}
		$this->display();
	}
	/**
	 * 商品添加页面
	 * @author changeheart
	 * @param
	 */
	public function add(){
		if(!$this->check_mes()){
			$this->error("还未填写用户信息");
			return;
		}
		$rs = M("CunItemsType")->order("type_id ASC")->select();
		$this->assign("type",$rs);
		$this->display();
	}
	/**
	 * 商品删除
	 * @author changeheart
	 * @param
	 */
	public function dsh_post(){
		$iid = I("get.id",NULL);
		if(!$iid){
			$this->error("网络错误，请重试");
			return;
		}
		$rs = $this->items->where("num_iid={$iid}")->delete();
		if($rs){
			admin_record_operation(324,$iid);
			$this->success("商品删除成功");
		}else{
			$this->error("该商品已被删除");
		}
	}
	/**
	 * ajax打包
	 * @author changeheart
	 * @param string 送出的信息
	 * @param string 送出的布尔值
	 */
	public function send_ajax($mes,$boor){
		$data = array(
			'status'=>$boor,
			'mes'=>$mes,
			);
		$mess = json_encode($data);
		echo $mess;
	}
	/**
	 * 商品存在检测
	 * @author changeheart
	 * @param
	 */
	public function check_iid(){
		$url = I("get.num_iid");
		$iid = $this->create_iid($url);
		$info = get_item_info($iid);
		if(!$info || empty($info->zk_final_price)){
			$this->send_ajax("链接错误或者该商品没有优惠券",0);
		}
		$taoke = $this->items->where("num_iid={$iid}")->find();
		if($taoke){
			$this->send_ajax("该商品已经存在",0);
		}else{
			$this->send_ajax("可以使用",1);
		}
	}
	/**
	 * 商品链接转iid
	 * @author changeheart
	 * @param
	 */
	public function create_iid($url){
		if(is_numeric($url))
			return $iid = $url;
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
			return trim($iid);
		}
	}

	/**
	 * 检测是否填写用户信息
	 * @author changeheart
	 * @param
	 */
	public function check_mes(){
		$id = sp_get_current_admin_id();
		$rs = $this->users->where("id={$id}")->find();
		if($rs){
			return 1;
		}else{
			return 0;
		}
	}
	/**
	 * 商品添加
	 * @author changeheart
	 * @param
	 */
	public function add_post(){
		if (IS_POST) {

			$item=I("post.item");
			/*
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
				foreach($iid_data as $val){
					if(get_item_info($val)){
						$iid = $val;
						break;
					}
				}
				
			}
			$iid = trim($iid);
			*/
			$iid = $this->create_iid($item['num_iid']);
			$item['status'] = '1';
			$item['add_time'] = time();
			$item['uid'] = sp_get_current_admin_id();
			$item['uname'] = $_SESSION['name'];
			$item['quan_url'] = str_replace('&amp;', '&', $item['quan_url']);
			$data = get_url_data($item['quan_url']);
			if($data['activity_id'] == "")$quan_id = $data['activityId'];
			else $quan_id = $data['activity_id'];

			$item['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_21582792_72310921&itemId=" . $iid ."&src=qhkj_dtkp&dx=1";
			if($iid != ""){
				$taoke = $this->items->where(array("num_iid"=>$iid))->find();

				if(!$taoke){
					$info = get_item_info($iid);
					for($i=0;$i<3;$i++){
						if(empty($info->zk_final_price)){
							$info = get_item_info($iid);
							break;
						}
					}
					if($info && !empty($info->zk_final_price)){
						$item['item_title'] = $info->title;
						$item['pic_url'] = $info->pict_url;
						if(empty($item['price'])){
							$item['price'] = $info->zk_final_price;
						}
						if(empty($item['vendor_name'])){
							$item['vendor_name'] = $info->nick;
						}
						
						$item['sellerId'] = $info->seller_id;
						$item['volume'] = $info->volume;
					}else {
						$this->error("该商品没有优惠券或者不存在");
					}
					$item['num_iid'] = $iid;
					

					$item['quan_url'] = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] . "&activity_id=" . $quan_id;
					$item['top_status'] = '1';
					$item['quan'];
					$item['cun'] = '1';
					$coupon_info = get_coupon_info($item['quan_url']);
					if($coupon_info) $item1 = array_merge($item,$coupon_info);


					$uname = M("Users")->field("user_login")->where("id={$item['uid']}")->find();

					if(empty($item['quan_price'])){
						$item1['quan_price'] = $item1['price']-$item1['quan'];
					}else{
						$item1['quan_price'];
						$item1['price'] = $item1['quan']+$item1['quan_price'];
					}
					$item1['uname'] = $uname['user_login'];
					$item1['time_add'] = date("Y-m-d H:i:s",time());
					$item1['time_edit'] = date("Y-m-d H:i:s",time());
					
					$item1['server_status'] = 1;
					//添加缩略图
					$item1['vendor_pic'] = sp_asset_relative_url($item1['vendor_pic']);
					$item1['quan_pic'] = sp_asset_relative_url($item1['quan_pic']);
					$item1['commit_pic'] = sp_asset_relative_url($item1['commit_pic']);
					$item1['pay_pic'] = sp_asset_relative_url($item1['pay_pic']);

					$result=$this->items->field("id,active_type,item_type,add_count,service_charge,num_iid,uid,quan_count,commite_rate,quan_price,quan,action_starttime,action_endtime,item_url,pic_url,item_title,item_dtitle,click_url,price,activeid,quan_url,almm_url,vendor_name,vendor_lever,vendor_pic,quan_pic,pay_pic,commit_pic,status,top_status,sellerId,volume,time_add,time_edit,judge_time,uname,contain_judge,judge_user,judge_name,info,taoke_rate,taoke_pic,postage_status,server_status,vendor_contact")->add($item1);

					if ($result) {
						admin_record_operation(323,$iid);
						$this->success("添加成功！");
					} else {
						$this->error("添加失败！");
					}
				}else{
					$this->error("该商品已经存在");
				}

			}
			else $this->error("商品链接出错，请检查链接！");
		}
	}

	/**
	 * 商品修改页面
	 * @author changeheart
	 * @param
	 */
	public function edit(){
		$iid = I("get.id");
		$rs = $this->items
		->where("num_iid='{$iid}'")
		->find();

		if($rs){
			$qs = M("CunItemsType")->order("type_id ASC")->select();
			$this->assign("type",$qs);
			$this->assign("item",$rs);
			$this->display();
		}else{
			$this->error("商品查询新消息错误，请不要修改请求信息");
		}
		
	}
	/**
	 * 商品修改
	 * @author changeheart
	 * @param
	 */
	public function edit_post(){
		$mes = I("post.item",NULL);
		if(is_null($mes)){
			$this->error("请不要清空信息");
		}
		$mes['num_iid'] = $this->create_iid($mes['num_iid']);

		$mes['time_edit'] = date("Y-m-d H:i:s",time());
		$mes['price'] = $mes['quan_price']+$mes['quan'];
		$rs = $this->items->where("num_iid={$mes['num_iid']}")->
					$result=$this->items->field("id,active_type,item_type,add_count,service_charge,num_iid,uid,quan_count,commite_rate,quan_price,quan,action_starttime,action_endtime,item_url,pic_url,item_title,item_dtitle,click_url,price,activeid,quan_url,almm_url,vendor_name,vendor_lever,vendor_pic,quan_pic,pay_pic,commit_pic,status,top_status,sellerId,volume,time_add,time_edit,judge_time,uname,contain_judge,judge_user,judge_name,info,taoke_rate,taoke_pic,postage_status,server_status,vendor_contact")->save($mes);
		if($rs){
			admin_record_operation(326,$mes);
			$this->success("商品信息修改成功");
		}else{
			$this->error("商品信息修改失败");
		}
	}
	
	/**
	 * 商品审核页面
	 * @author changeheart
	 * @param
	 */
	public function items(){
		$this->_items_list();
		$this->display();
	}
	/**
	 * 商品审核页面数据查询
	 * @author changeheart
	 * @param
	 */
	public function _items_list(){
		$status = I("post.status",null);
		
		$where_ands = array();
		
		$sort = array(
			0=>"status",
			1=>"top_status",
			2=>"server_status",
			3=>"top_status",
		);
		

		$where_ands = $this->sort($sort,$where_ands);
		$where_ands = $this->search($where_ands);

		$datetime = I("post.timedate","");

		$where_ands[] = "time_add like '{$datetime}%'";
		
		$where= join(" and ", $where_ands);
			
			
		$count=$this->items
		->where($where)
		->count();
			
		$page = $this->page($count, 20);
			
		$rankarr = I("post.rank",array(0=>array('name'=>'status','status'=>1)));
		$rank = $this->rank($rankarr);
			
		$posts=$this->items
		->where($where)->limit($page->firstRow . ',' . $page->listRows)
		->order("top_status DESC,{$rank}")
		->select();
		
		$judge_data = M("HaozhuJudge")->select();

		
		$users_obj = M("Users");
		$users_data=$users_obj->field("id,user_login")->select();
		
		
		$this->assign("judge",$judge_data);
		$this->assign("user",$users_data);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		unset($_GET[C('VAR_URL_PARAMS')]);
		$this->assign("formget",$_GET);
		$this->assign("posts",$posts);
		$this->assign("post",I("post."));
	}
	/**
	 * 审核页面
	 * @author changeheart
	 * @
	 */
	public function judge(){
		$iid = I("get.id");

		$rs = $this->items
		->where("num_iid={$iid}")
		->find();
		//dump($this->items->getLastSql());
		if(!$rs){
			$this->error("查询错误，该商品已被删除");
		}
		$users = $this->users->field("name,phone,contact,contact_type")->where("id={$rs['uid']}")->find();
		$this->assign("posts",$rs);
		$this->assign("user",$users);
		$this->display();
	}
	/**
	 * 审核提交
	 * @author changeheart
	 * @
	 */
	public function judge_post(){
		$data = I("post.");
		if(!$data){
			$this->error("请填写提交意见");
			return;
		}
		$adminid = sp_get_current_admin_id();

		$judge_model = M("HaozhuJudge");

		$judge_data = $data;
		$judge_data['user_judge'] = $adminid;
		$judge_data['time_judge'] = date("Y-m-d H:i:s",time());

		$items_data['judge_time'] = date("Y-m-d H:i:s",time());
		$items_data['status'] = $data['judge_status'];
		$items_data['contain_judge'] = $data['contain_judge'];
		$items_data['judge_user'] = $adminid;
		$judge_name = M("Users")->field("user_login")->where("id={$adminid}")->find();
		$items_data['judge_name'] = $judge_name['user_login'];
		//dump($judge_data);
		
		
		//添加
		$resu = $judge_model->add($judge_data);
		$item_re = $this->items->field("judge_time,status,contain_judge,judge_user,judge_name")->where("num_iid={$judge_data['num_iid']}")->save($items_data);
		if($resu && $item_re){
			admin_record_operation(319,$judge_data);
			$this->success("审核成功");
		}else{
			$this->error("审核错误，请联系管理员");
		}
	}

	/**
	 * 搜索方法
	 * @author changeheart
	 * @patram array()数组
	 */
	public function search($where){
		$kw = I("post.kw");
		$kwarr = array(
			1 => array("key"=>"item_title","op"=>"like"),
			2 => array("key"=>"item_dtitle","op"=>"like"),
			3 => array("key"=>"num_iid","op"=>"like"),
		);
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
	/**
	 * 结算页面
	 * @author changeheart
	 * 
	 */
	public function settle(){
		$iid = I("get.id",NULL);
		$rs = $this->items->where("num_iid={$iid}")->find();
		
		if(!$rs){
			$this->error("查询商品信息出错，请不要更改请求头");
			return;
		}
		if($rs['status'] != 3){
			$this->error("该商品尚未通过审核");
		}
		$user = $this->users->where("id={$rs['uid']}")->find();
		$rs['name']=$user['name'];
		$this->assign("posts",$rs);
		$this->display();
	}
	/**
	 * 结算方法
	 * @author changeheart
	 */
	public function settle_post(){
		$mes = I("post.",NULL);
		if(is_null($mes)){
			$this->error("表单不能为空");
			return;
		}
		$data['turnover'] = $mes['end_count'];
		$data['server_status'] = 2;
		$data['server_price_count'] = $mes['count'];
		$eq = $this->items->where("num_iid={$mes['num_iid']}")->save($data);
		if($eq){
			admin_record_operation(332,$data);
			$this->success("结算成功");
		}else{
			$this->error("结算失败，请联系管理员");
		}
	}
	/**
	 * 顶置方法
	 * @author changeheart
	 */
	public function overhead(){
		$iid = I("get.id",NULL);
		$rs = $this->items->where("num_iid={$iid}")->save(array("top_status"=>2));
		if($rs){
			$this->success("顶置成功");
		}else{
			$this->error("顶置错误，请不要修改请求头");
		}
	}
	/**
	 * 解除顶置方法
	 * @author changeheart
	 */
	public function unoverhead(){
		$iid = I("get.id",NULL);
		$rs = $this->items->where("num_iid={$iid}")->save(array("top_status"=>1));
		if($rs){
			$this->success("解除顶置成功");
		}else{
			$this->error("解除顶置错误，请不要修改请求头");
		}
	}
}

