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
class AdminVideoController extends AdminbaseController {
	protected $item_model;
	protected $video_model;
	protected $user_model;
	
	//析构方法
	public function _initialize(){
		parent::_initialize();
		$this->item_model = D("Tbkqq/CunItems");
		$this->video_model = D("Tbkqq/CunItemsVideo");
		$this->user_model = D("Tbkqq/Users");
	}
	
	//视频商品初始界面
	public function index(){
		$where = $this->search();
		
		$page = $this->video_model
		->alias("a")
		->join(C("DB_PREFIX")."cun_items b ON a.iid=b.num_iid")
		->where($where)
		->count();
		
		$page = $this->page($count, 20);
		
		$posts = $this->video_model
		->alias("a")
		->join(C("DB_PREFIX")."cun_items b ON a.iid=b.num_iid")
		->where($where)
		->limit($page->firstRow.",".$page->listRows)
		->order("a.video_time desc")
		->select();
		
		$this->assign("posts",$posts);
		$this->assign("Page",$page->show("Admin"));
		$this->search();
		$this->display();
	}
	//添加视频商品
	public function add(){
		//查表
		$iid = I("get.iid");
		$item = array(
			"iid" => $iid,
		);
		$this->assign("item",$item);
		
		$this->display();
	}
	
	//修改视频商品
	public function edit(){
		$this->display();
	}
	//删除视频商品
	public function dsh(){
		$iid = I("get.iid");
		
		$rs = $this->video_model()->where("iid={$iid}")->select();
		if(!$rs){
			$this->error("网络错误，请重试");
		}
		unlink($rs["video_pic"]);
		unlink($rs["video"]);
		
		$result = $this->video_model()->where("iid={$iid}")->delete();
		
		if($result){
			$this->success("删除视频商品成功");
			admin_record_operation(299,$num_iid);
		}else{
			$this->error("该视频商品不存在");
		}
		
	}
	//添加方法
	public function video_add(){
		if(empty($_POST["video"]["iid"])){
			$this->error("页面错误，错误401");
		}
		if(empty($_POST["video"]["video"])){
			$this->error("请上传视频");
		}
		if(empty($_POST["video"]["video_pic"])){
			$this->error("请上传图片");
		}
		
		//存入服务器
		$video['video'] = sp_asset_relative_url($_POST['video']['video']);
		$video['video_pic'] = sp_asset_relative_url($_POST['video']['video_pic']);
		$video['iid'] = $_POST['video']['iid'];
		$video = array(
			"video"	=> sp_asset_relative_url($_POST['video']['video']),
			"video_pic" => sp_asset_relative_url($_POST['video']['video_pic']),
			"iid"	=> $_POST['video']['iid'],
			"video_user"	=> get_current_admin_id(),
			"video_time"	=> array("exp"=>"NOW()"),
		);
		$rs = $this->video_model->add($video);
		if($rs){
			$this->success("添加成功");
			admin_record_operation(300,$num_iid);
		}else{
			$this->error("系统错误,错误801");
		}
	}
	//修改方法
	public function video_edit(){
		$item = I("post.item",null);
		$video = I("post.video",null);
		if(is_null($item) || is_null($video)){
			$this->error("请填写商品信息");
		}
		//商品信息修改
		$rq = $this->item_model()->save($item);
		if(!$rq){
			$this->error("修改商品信息失败");
		}
		
		//视频存入服务器
		$video['video'] = sp_asset_relative_url($_POST['video']['video']);
		$video['video_pic'] = sp_asset_relative_url($_POST['video']['video_pic']);
		$video['iid'] = $item['num_iid'];
		$video = array(
			"video"	=> sp_asset_relative_url($_POST['video']['video']),
			"video_pic" => sp_asset_relative_url($_POST['video']['video_pic']),
			"iid"	=> $_POST['video']['iid'],
			"video_user"	=> get_current_admin_id(),
			"video_time"	=> array("exp"=>"NOW()"),
		);
		$rs = $this->video_model->save($video);
		if(!$rs){
			$this->error("修改视频商品信息失败");
		}
		
		admin_record_operation(301,$num_iid);
	}
	//搜索方法
	public function search(){
		$key = I("post.keyword","");
		$where = array(
			"iid"=>"%{$key}%",
			"title"=>"%{$key}%",
		);
		return $where;
	}
}