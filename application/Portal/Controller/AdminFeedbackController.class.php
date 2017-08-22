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

class AdminFeedbackController extends AdminbaseController{
	protected $feedback_model;
	protected $feedback_type_model;
	
	//预定义模块
	function _initialize() {
		parent::_initialize();
		$this->feedback_model = D("Portal/Feedback");
		$this->feedback_type_model = D("Portal/FeedbackType");
	}
	
	//主页
	function index(){
		$this->_list();
		$this->_tree();
		$this->display();
	}
	//排序方法
	function _list(){
		$status = I("post.status",null);
		
		if(is_null($status) || $status==0){
			$where_ands=array("1");
		}else{
			$where_ands=array("status=$status");
		}
		
		$a = array(
			0=>1,
			1=>2,
		);
		$where_ands = $this->search($where_ands);
		
		$where= join(" and ", $where_ands);
			
			
		$count=$this->feedback_model
		->where($where)
		->count();
			
		$page = $this->page($count, 20);
			
			
		$posts=$this->feedback_model
		->where($where)->limit($page->firstRow . ',' . $page->listRows)
		->order("status DESC,alter_time DESC")
		->select();
		
		
		
		$users_obj = M("Users");
		$users_data=$users_obj->field("id,user_login")->select();
		
		$sta = $this->feedback_type_model
		->select();
		
		$this->assign("user",$users_data);
		$this->assign("type",$sta);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("current_page",$page->GetCurrentPage());
		unset($_GET[C('VAR_URL_PARAMS')]);
		$this->assign("formget",$_GET);
		$this->assign("posts",$posts);
		dump(I("post."));
		$this->assign("post",I("post."));
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
	 * 处理页面
	 * @author changeheart
	 * @param
	 */
	public function opinion(){
		$id = I("get.id");
		$mes = $this->feedback_model
		->where("id = {$id}")
		->find();
		if($mes){
			$sta = $this->feedback_type_model->select();
			$this->assign("type",$sta);
			$users_data=M("Users")->field("id,user_login")->select();
			$this->assign("user",$users_data);
			$this->assign("post",$mes);
			$this->display();
		}else{
			$this->error("网络错误，请不要修改url地址");
		}
		
	}
	
	/**
	 * 处理反馈
	 * @author changeheart
	 * @param
	 */
	public function opinion_do(){
		$ma = I("post.",null);
		if(is_null($ma)){
			$this->error("网络错误，请重试");
		}
		foreach($ma as $val){
			if(empty($val)){
				$this->error("请完整填写表单");
				break;
			}
		}
		//修改feedback表
		$data = array(
			"id"	=>	$ma['id'],
			"alter_time"	=>	date("Y-m-d H:i:s",time()),
			"status"	=>	$ma['status'],
			"uid"	=>	sp_get_current_admin_id(),
			"opinion_mes"=>$ma['contain'],
		);
		$mes = $this->feedback_model->save($data);
		if($mes){
			$rs = M("Feedback")->where("id={$ma['id']}")->find();
			admin_record_operation(306,$rs['post_title']);
			$this->success("意见处理成功");
		}else{
			$this->error("网络错误，请联系我");
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
			1 => array("key"=>"post_title","op"=>"like"),
			2 => array("key"=>"post_content","op"=>"like"),
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
}

