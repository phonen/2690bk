<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class CuntaoController extends HomebaseController {
	
    //首页
	public function index(){

			$appname = C("SITE_APPNAME");

			$dataoke_model = M('CunItems');

			//判断排序标准
			$sortnorm = array(
					'zonghe'	=>	'cun',
					'new'		=>	'add_time',
					'commi'		=>	'commission_rate',
					'price'		=>	'coupon_price'
			);
			if(isset($_GET['sort'])){
				$sort1 = $_GET['sort'];
				$sort = $sortnorm[$sort1];
			}
			//获取分类
			$shoparr = M("CunItemsType")->group("type_id")->select();
			//二维数组转一维
			$shop = array();
			foreach($shoparr as $val){
				$shop[$val['type_id']] = $val['type_name'];
			}
			//获取分类信息表
			if(isset($_GET['cid'])){
				$shop1 = $_GET['cid'];
				$type = $shop1;
			}else{
				$shop1 = 0;
				$type = 0;
			}
			
			
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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

			//在分类送出前插入的条件
			//array_push($where_ands,"cun>='1'");
			$where_ands[]="status!=2";
			$where_ands[]="is_cun=1";
			
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
			
			
			//全部排序展示
			if($shop1 != '全部'){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 50;

			import('Page');


			if(sp_is_mobile()){
				$pagetpl='{first}{liststart}{list}{listend}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}else{
				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}
			

			if($sort){
				$items = $dataoke_model->where($where)->order($sort." desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				$this->assign("lei",$sort1);
			}else {
				$items = $dataoke_model->where($where)->order("cun desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			}


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			
			$this->check_wechat();
			$this->get_quanxian();
			$this->send_sort();
			$this->display();
			
	}
	//送出商品分类信息
	public function send_sort(){
		$rs = M("CunItemsType")->order("type_id ASC")->select();
		if($rs){
			$this->assign("sortname",$rs);
		}
		return;
	}
	
	//搜索
	public function search(){

		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');

		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'commi'		=>	'commission_rate',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			

		$where_ands = array("1=1");
		$fields=array(

			'kw'  => array("field"=>"title","operator"=>"like"),
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
		}else{
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

		//在分类送出前插入的条件
		//array_push($where_ands,"cun>='1'");
		$where_ands[]="status!='2'";
		$where_ands[]="is_cun=1";
		//获取分类，送出分类
		$shoptype = $this->checksort($where_ands);
		$this->assign("shoptype",$shoptype);
		$this->assign("cid",$shop1);
			
		//全部排序展示
		if($shop1 != 0){
			array_push($where_ands,"cate_id='{$type}'");
		}
		$where= join(" and ", $where_ands);

		$count = $dataoke_model->where($where)->count();

		$pagesize = 100;

		import('Page');


		if(sp_is_mobile()){
			$pagetpl='{first}{liststart}{list}{listend}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		}else{
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		}

		//$items = $dataoke_model->where($where)->order("source,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		if($sort){
			$items = $dataoke_model->where($where)->order($sort." desc,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			$this->assign("lei",$sort1);
		}else {
			$items = $dataoke_model->where($where)->order("source,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		}
		
		//遍历未知高拥
		foreach($items as $val){
			if(is_null($val['cun'])){
				$val['commission_rate'] = "未知";
			}
		}


		$this->assign("formget",$_GET);
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);
		
		$this->get_quanxian();
		$this->check_wechat();
		$this->send_sort();
		
		$this->display("qlist_ctgy");
	}

	public function qlist_ctgy(){
		$appname = C("SITE_APPNAME");

			$dataoke_model = M('CunItems');

			//判断排序标准
			$sortnorm = array(
					'zonghe'	=>	'cun',
					'new'		=>	'add_time',
					'commi'		=>	'commission_rate',
					'price'		=>	'coupon_price'
			);
			if(isset($_GET['sort'])){
				$sort1 = $_GET['sort'];
				$sort = $sortnorm[$sort1];
			}
			//获取分类
			$shoparr = M("CunItemsType")->group("type_id")->select();
			//二维数组转一维
			$shop = array();
			foreach($shoparr as $val){
				$shop[$val['type_id']] = $val['type_name'];
			}
			//获取分类信息表
			if(isset($_GET['cid'])){
				$shop1 = $_GET['cid'];
				$type = $shop1;
			}else{
				$shop1 = 0;
				$type = 0;
			}
			
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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

			//在分类送出前插入的条件
			//array_push($where_ands,"cun>='1'");
			$where_ands[]="status!='2'";
			$where_ands[]="is_cun=1";
			
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
			
			//全部排序展示
			if($shop1 != 0){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 50;

			import('Page');


			if(sp_is_mobile()){
				$pagetpl='{first}{liststart}{list}{listend}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}else{
				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}

			if($sort){
				$items = $dataoke_model->where($where)->order($sort." desc,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				$this->assign("lei",$sort1);
			}else {
				$items = $dataoke_model->where($where)->order("source,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			}


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			
			$this->get_quanxian();
			$this->check_wechat();
			$this->send_sort();
			
			$this->display();
	}
	//查询分类数目
	public function checksort($where){
		
		$dataoke_model = M('CunItemsType');
		foreach($where as $key=>$val){
			if($val == "1=1"){
				continue;
			}
			$where[$key] = "b.".$val;
		}
		$type = $dataoke_model
		->alias("a")
		->field("a.type_id,count(*) as count")
		->join("LEFT JOIN cmf_cun_items as b on a.type_id=b.cate_id")
		->where($where)
		->group("a.type_id")
		->order("a.type_id")
		->select();
		$type[0]['count'] = M("CunItems")->count();
		
		return $type;
	}
	public function weixin(){
		$this->display();
	}
	public function login_reg(){
		$this->display();
	}
	public function login(){
		$this->display();
	}
	//测试地址
	public function login_rega(){
		$this->display();
	}
	//各个页面跳转个人中心
	public function changepwd(){
		if(!sp_is_user_login()){
			$this->error("请登录");
			return;
		}
		//获取当前用户信息
		$id = sp_get_current_userid();
		$user_model = M('Users');
		$rs = $user_model->where("id={$id}")->select();
		//获取用户信息分类表
		$earn_model = M('UEarnType');
		$team_model = M('UTeamType');
		$work_model = M('UWorkType');
		$type['earn'] = $earn_model->select();
		$type['team'] = $team_model->select();
		$type['work'] = $work_model->select();
		
		$this->assign('type',$type);
		$this->assign('user',$rs[0]);
		$this->display("personal");
	}
	
	//个人中心
	public function personal(){
		if(!sp_is_user_login()){
			$this->error("请登录");
			return;
		}
		//获取当前用户信息
		$id = sp_get_current_userid();
		$user_model = M('Users');
		$rs = $user_model->where("id={$id}")->select();
		//获取用户信息分类表
		$earn_model = M('UEarnType');
		$team_model = M('UTeamType');
		$work_model = M('UWorkType');
		$type['earn'] = $earn_model->select();
		$type['team'] = $team_model->select();
		$type['work'] = $work_model->select();
		
		$this->assign('type',$type);
		$this->assign('user',$rs[0]);
		$this->display();
	}
	public function personal_data(){
		if(!sp_is_user_login()){
			$this->error("请登录");
			return;
		}
		//获取当前用户信息
		$id = sp_get_current_userid();
		$user_model = M('Users');
		$rs = $user_model->where("id={$id}")->select();
		//获取用户信息分类表
		$earn_model = M('UEarnType');
		$team_model = M('UTeamType');
		$work_model = M('UWorkType');
		$type['earn'] = $earn_model->select();
		$type['team'] = $team_model->select();
		$type['work'] = $work_model->select();
		
		$this->assign('type',$type);
		$this->assign('user',$rs[0]);
		$this->display();
	}
	public function personal_password(){
		if(!sp_is_user_login()){
			$this->error("请登录");
			return;
		}
		$this->display();
	}
	public function personal_pid(){
		if(!sp_is_user_login()){
			$this->error("请登录");
			return;
		}
		//获取当前用户信息
		$user = sp_get_current_user();
		$this->assign('user',$user);
		//判断有无pid权限
		$sta = M("UserPidStatus")->where("user_id={$user['id']} and status=1")->find();	
		if($sta){
			$this->assign('spid',1);
		}else{
			$this->assign('spid',2);
		}
		//输出pid表
		$user_model = M('UsersMes');
		$rs = $user_model->where("id={$user['id']}")->find();
		$this->assign('pid',$rs);
		$this->display();
	}
	public function personal_put(){
		if(!sp_is_user_login()){
			$this->error("请登录");
			return;
		}
		//获取当前用户信息
		$userid = sp_get_current_userid();
		$user = M("Users")->where("id={$userid}")->find();
		$this->assign('user',$user);
		$this->display();
	}

	//琅琊榜
	public function top_tui(){
		$appname = C("SITE_APPNAME");


		$dataoke_model = M('CunItems');
			
		
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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

			//array_push($where_ands,"cun>='1'");

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 50;

				import('Page');


				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

				$items = $dataoke_model->where($where)->order("commission_rate desc")->limit($page->firstRow . ',' . $page->listRows)->select();


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			$this->display();
	}

	//搜索
	public function search_qlist(){
	
		$appname = C("SITE_APPNAME");
	
		$dataoke_model = M('CunItems');
	
		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'commi'		=>	'commission_rate',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			
	
		$where_ands = array("1=1");
		$fields=array(
	
				'kw'  => array("field"=>"title","operator"=>"like"),
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
		}else{
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
	
		//在分类送出前插入的条件
		array_push($where_ands,"isnull(cun)");
		//获取分类，送出分类
		$shoptype = $this->checksort($where_ands);
		$this->assign("shoptype",$shoptype);
		$this->assign("cid",$shop1);
			
		//全部排序展示
		if($shop1 != 0){
			array_push($where_ands,"cate_id='{$type}'");
		}
		$where= join(" and ", $where_ands);
	
		$count = $dataoke_model->where($where)->count();
	
		$pagesize = 100;
	
		import('Page');
	
	
		if(sp_is_mobile()){
			$pagetpl='{first}{liststart}{list}{listend}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		}else{
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		}
	
		//$items = $dataoke_model->where($where)->order("source,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		if($sort){
			$items = $dataoke_model->where($where)->order($sort." desc,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			$this->assign("lei",$sort1);
		}else {
			$items = $dataoke_model->where($where)->order("source,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		}
	
		//遍历未知高拥
		foreach($items as $val){
			if(is_null($val['cun'])){
				$val['commission_rate'] = "未知";
			}
		}
	
	
		$this->assign("formget",$_GET);
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);
		
		$this->get_quanxian();
		
		$this->display("qlist");
	}
	public function qlist() {
		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');


		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'commi'		=>	'commission_rate',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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
			//在分类送出前插入的条件
			array_push($where_ands,"status!=2");
			
				
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
				
			//全部排序展示
			if($shop1 != 0){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 100;

				import('Page');


			if(sp_is_mobile()){
				$pagetpl='{first}{liststart}{list}{listend}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}else{
				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}

				//$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				if($sort){
					$items = $dataoke_model->where($where)->order($sort." desc,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
					$this->assign("lei",$sort1);
				}else {
					$items = $dataoke_model->where($where)->order("cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				}


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			
			$this->get_quanxian();
			$this->check_wechat();
			$this->send_sort();
			
			$this->display();


    }


	//旧版本
	public function gettp2(){
		$id = $_GET['id'];
		$dataoke_model = M('CunItems');
		$item = $dataoke_model->where(array("id"=>$id))->find();
		if($item){
			echo "<img src=\"http://cunimg.2690.cn/qr/" .$item['num_iid'] .".jpg\">";
		}
		//$data = get_url_data($item['quan_link']);
//		$item['quan_link'] = "http://uland.taobao.com/coupon/edetail?activityId=" .$data['activity_id'] ."&pid=" . $site['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=1";
//		$this->assign("item",$item);
//		$this->display("item");


	}
	public function gettpl1(){
		$id = $_GET['id'];
		//获取用户pid
		//判断id是否存在
    	$uid = sp_get_current_userid();
    	if($uid){
			$sta = M("UserPidStatus")->where("user_id=$uid")->find();
			if(empty($sta) || $sta['status']==2){
				$pid = 'mm_120456532_21582792_72310921';
			}else{
				//获取pid
				$rs = M("UsersMes")->where("id=$uid")->find();
				if($rs){
					$pid = $rs['pid'];
				}else{
					$pid = 'mm_120456532_21582792_72310921';
				}
			}
    	}else{
    		$pid = 'mm_120456532_21582792_72310921';
    	}
		$dataoke_model = M('CunItems');
		$item = $dataoke_model->where(array("id"=>$id))->find();
		if($item){
			echo "<img src=\"http://www.2690.cn/?m=img&a=erwei_test&id=".$item['num_iid']."&pid=".$pid."\">";
		}
		//$data = get_url_data($item['quan_link']);
		//		$item['quan_link'] = "http://uland.taobao.com/coupon/edetail?activityId=" .$data['activity_id'] ."&pid=" . $site['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=1";
		//		$this->assign("item",$item);
		//		$this->display("item");
	
	
	}
	//配权版本
	public function gettpl(){
		$id = $_GET['id'];
		//获取用户pid
		//判断id是否存在
    	$uid = sp_get_current_userid();
    	if($uid){
			$uid = $uid;
    	}else{
    		$uid = 0;
    	}
		$dataoke_model = M('CunItems');
		$item = $dataoke_model->where(array("id"=>$id))->find();
		if($item){
			echo "<img src=\"http://cunimg.2690.cn/qr/".$uid."_".$item['num_iid'] .".jpg\">";
		}
		//$data = get_url_data($item['quan_link']);
		//		$item['quan_link'] = "http://uland.taobao.com/coupon/edetail?activityId=" .$data['activity_id'] ."&pid=" . $site['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=1";
		//		$this->assign("item",$item);
		//		$this->display("item");
	
	
	}
	
//后台文案用生成图片
	public function adgettpl(){
		$id = $_GET['id'];
		$dataoke_model = M('CunItems');
		$item = $dataoke_model->where(array("id"=>$id))->find();
		if($item){
			echo "http://cunimg.2690.cn/qr".$item['num_iid'].".jpg";
		}
	}
	//通过iid获取商品信息
	public function getmes(){
		$iid = I("get.iid");
		$rs = M("CunItems")->where("num_iid=$iid")->find();
		if($rs){
			//转化json对象
			$js = json_encode($rs);
			echo $js;
		}else{
			echo 0;
		}
	}
	
	
	//2690全天销量榜
	public function top_all(){
		$this->display();
	}
	
	//2690视频商品
	public function video(){
		$this->error("敬请期待");
		//$this->display();
	}
	public function video_test(){
		$page = $_GET['id'];
		$video = "video_test$page";
		$this->display($video);
	}
	//2690视频文件下载
	public function video_download(){
		$file_name = $_GET['filename'];
		//获取路径
		$file_dir = 'video/';
		if(!file_exists($file_dir.$file_name)){
			$this->error("没有找到文件");
		}else{
			$file = fopen($file_dir.$file_name,'r');
			$size = filesize($file_dir.$file_name);
			header("Content-type:application/octet-stream");
			header("Accept-Ranges:bytes");
			header("Accept-Length:".$size);
			header("Content-Disposition:attachment;filename=$file_name");
			echo fread($file,$size);
			fclose($file);
		}
	}
	//村淘村
	public function pic(){
		$this->display();
	}
	//村淘村二级
	public function news_test(){
		$page = $_GET['id'];
		$news = "news_test$page";
		$this->display($news);
	}
	//获取用户查看商品发布人权限信息
	public function get_quanxian(){
		//判断用户有无权限
		$uid = sp_get_current_userid();
		$sql = "select * from cmf_role_user a inner join cmf_auth_access b on b.role_id=a.role_id and a.user_id=$uid and b.rule_name='tbkqq/admincuntao/items';";
		$rs = M()->query($sql);
		if(empty($rs)){
			$this->assign('quanxian',0);
		}else{
			$this->assign('quanxian',1);
		}
	}
	//判断用户是否为微信浏览器或者iphone登陆
	public function check_wechat(){
		$http = $_SERVER['HTTP_USER_AGENT'];
		if(strstr($http,"iPhone")!==false || (strstr($http,"Linux")!==false && strstr($http,"wv")!==false)){
			$this->assign("wechat",1);
		}else{
			$this->assign("wechat",2);
		}
	}
	//使用帮助
	public function common_problem(){
		$this->display();
	}
	//帮助中心
	public function help_test(){
		$this->display();
	}

	//关于我们
	public function about(){
		$this->display();
	}
	public function feedback(){
		$this->display();
	}
	
	
	public function tbdq(){
		$this->display();
	}
	public function tbsg(){
		$this->display();
	}
	/**
	 * author changeheart
	 * 填写反馈
	 * 
	 */
	public function feedback_up(){
		if(!sp_check_verify_code()){
			$this->error("验证码错误");
			return 0;
		}
		$uid = sp_get_current_userid();
		if(!$uid){
			$uid = 0;
		}
		$feed = I("post.");
		
		$post = htmlspecialchars_decode($feed["feedback-content"]);
		//$this->success(dump($feed));
		$feedback = array(
			"post_content" => $post,
			"author"       => $uid,
			"post_title"   => $feed["feedback-title"],
			"post_phone"   => $feed["feedback-tel"],
			"status"	   => 5,
			"add_time"	   => array('exp','NOW()'),
			"alter_time"   => array('exp','NOW()'),
		);
		$rs = M("Feedback")->field("post_content,author,post_title,post_phone,status,add_time,alter_time")->add($feedback);
		
		if($rs){
			$this->success("反馈上传成功");
			//记录前台操作
			user_record_operation(7,$post);
		}else{
			$this->error("反馈上传失败");
		}
	}
	/**
	 * author changeheart
	 * 使用流程页面
	 * 
	 */
	public function use_flow(){
		$this->display();
	}
	/**
	 * author changeheart
	 * 招商页面
	 * 
	 */
	public function business_cooperation(){
		$this->display();
	}
	/**
	 * author changeheart
	 * 查看当前用户是否为村小二
	 * 
	 */
	public function is_xiaoer_permissions(){
		$uid = sp_get_current_userid();
		$rs = M("Users")->alias("a")->join("cmf_role_user as b on a.id=b.user_id")->where("b.role_id=16 and a.id={$uid}")->find();
		if($rs){
			$this->assign('xiaoer',1);
		}else{
			$this->assign('xiaoer',0);
		}
	}
	/**
	 * author changeheart
	 * 公众号登陆login
	 * 
	 */
	public function official_accounts_login(){
		$this->display();
	}
	/**
	 * author changeheart
	 * 公众号方法
	 * 
	 */
	public function official_accounts(){

			$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');

		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'commi'		=>	'commission_rate',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			

		$where_ands = array("1=1");
		$fields=array(

			'kw'  => array("field"=>"title","operator"=>"like"),
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
		}else{
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

		//在分类送出前插入的条件
		array_push($where_ands,"cun>='1'");
		$where_ands[]="status='underway'";
		//获取分类，送出分类
		$shoptype = $this->checksort($where_ands);
		$this->assign("shoptype",$shoptype);
		$this->assign("cid",$shop1);
			
		//全部排序展示
		if($shop1 != 0){
			array_push($where_ands,"cate_id='{$type}'");
		}
		$where= join(" and ", $where_ands);

		$count = $dataoke_model->where($where)->count();

		$pagesize = 50;

		import('Page');


		$pagetpl='{first}{liststart}{list}{listend}{last}';
		$PageParam = C("VAR_PAGE");
		$page = new \Page($count,$pagesize);
		$page->setLinkWraper("li");
		$page->__set("PageParam", $PageParam);
		$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

		//$items = $dataoke_model->where($where)->order("source,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		if($sort){
			$items = $dataoke_model->where($where)->order($sort." desc,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			$this->assign("lei",$sort1);
		}else {
			$items = $dataoke_model->where($where)->order("source,cun desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		}
		
		//遍历未知高拥
		foreach($items as $val){
			if(is_null($val['cun'])){
				$val['commission_rate'] = "未知";
			}
		}
		//寻找角色信息



		$this->assign("formget",$_GET);
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);
		
		$this->get_quanxian();
		$this->is_xiaoer_permissions();
		
		$this->display();
			
	}

	/**
	*@author changeheart
	*请求商品信息
	**/
	public function ajax_getmes(){
		$iid = I("post.iid");
		$rs = M("CunItems")->where("id={$iid}")->find();
		if(!$rs){
			$data = array(
				'mes_status'=>0,
				'mes'=>"查询错误",
			);
		}else{
			$admincuntao = A("Tbkqq/AdminCuntao");
			$rs['token'] = $admincuntao->create_one_taokouling($rs);
			$data = $rs;
			$data['mes_status']=1;
		}
		$mes = json_encode($data);
		echo $mes;
	}
	//公众号领券直播
	public function official_accounts_two() {
		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');


		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'commi'		=>	'commission_rate',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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
			//在分类送出前插入的条件
			array_push($where_ands,"isnull(cun)");
			
				
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
				
			//全部排序展示
			if($shop1 != 0){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 50;

				import('Page');


				$pagetpl='{first}{liststart}{list}{listend}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));

				//$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				if($sort){
					$items = $dataoke_model->where($where)->order($sort." desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
					$this->assign("lei",$sort1);
				}else {
					$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				}


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			
			$this->get_quanxian();
			
			$this->display();


    }
    //ajax判断当前登陆用户旧密码
    public function check_old_pwd(){
    	$id = sp_get_current_userid();
    	$mes = array(

    	);
    	if(!$id){
    		$mes['status']=0;
    		$mes['mes']="未登录";
    		$mess = json_encode($mes);
    		echo $mess;
    		return;
    	}
    	//$pwd = I("post.");
    	$pwd = $_POST['opwd'];
    	if(is_null($pwd)){
    		$mes['status']=0;
    		$mes['mes']="请填写旧密码";
    		$mess = json_encode($mes);
    		echo $mess;
    		return;
    	}
    	$data = array(
    		"id"=>$id,
    		"user_pass"=>sp_password($pwd),
    	);
    	$rs = M("Users")->field("id")->where($data)->find();
    	if($rs){
    		$mes['status']=1;
    		$mes['mes']="密码正确";
    		$mess = json_encode($mes);
    		echo $mess;
    		return;
    	}else{
    		$mes['status']=0;
    		$mes['mes']="密码错误";
    		$mess = json_encode($mes);
    		echo $mess;
    		return;
    	}

    }
    //生成淘口令
    public function create_taokouling(){
		$url = I("post.url",NULL);
		if(!$url){
			$mes['status']=0;
			$mes['mes']="url未发送";
			$mess = json_encode($mes);
			echo $mess;
			return;
		}
		$admin = A("Tbkqq/AdminCuntao");
		$token = $admin->create_one_taokouling($url);
		$mes['status']=1;
		$mes['mes']=$token;
		$mess = json_encode($mes);
		echo $mess;
		return;
	}
	//聚划算商品
	public function jvhuasuan() {
		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');


		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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
			//在分类送出前插入的条件
			$where_ands[]="status!=2";
			$where_ands[]="preferential_type='jvhuasuan'";
			
				
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
				
			//全部排序展示
			if($shop1 != 0){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 100;

				import('Page');


			if(sp_is_mobile()){
				$pagetpl='{first}{liststart}{list}{listend}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}else{
				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}

				//$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				if($sort){
					$items = $dataoke_model->where($where)->order($sort." desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
					$this->assign("lei",$sort1);
				}else {
					$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				}


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			
			$this->get_quanxian();
			$this->check_wechat();
			$this->send_sort();
			
			$this->display();


    }
    //淘抢购商品
	public function taoqianggou() {
		$appname = C("SITE_APPNAME");

		$dataoke_model = M('CunItems');


		//判断排序标准
		$sortnorm = array(
				'zonghe'	=>	'cun',
				'new'		=>	'add_time',
				'price'		=>	'coupon_price'
		);
		if(isset($_GET['sort'])){
			$sort1 = $_GET['sort'];
			$sort = $sortnorm[$sort1];
		}
		//获取分类
		$shoparr = M("CunItemsType")->group("type_id")->select();
		//二维数组转一维
		$shop = array();
		foreach($shoparr as $val){
			$shop[$val['type_id']] = $val['type_name'];
		}
		//获取分类信息表
		if(isset($_GET['cid'])){
			$shop1 = $_GET['cid'];
			$type = $shop1;
		}else{
			$shop1 = 0;
			$type = 0;
		}
			
			$where_ands = array("1=1");
			$fields=array(

				'kw'  => array("field"=>"title","operator"=>"like"),
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
			}else{
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
			//在分类送出前插入的条件
			$where_ands[]="status!=2";
			$where_ands[]="preferential_type='taoqianggou'";
			
				
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
				
			//全部排序展示
			if($shop1 != 0){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);

			$count = $dataoke_model->where($where)->count();

			$pagesize = 100;

				import('Page');


			if(sp_is_mobile()){
				$pagetpl='{first}{liststart}{list}{listend}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "3", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}else{
				$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
				$PageParam = C("VAR_PAGE");
				$page = new \Page($count,$pagesize);
				$page->setLinkWraper("li");
				$page->__set("PageParam", $PageParam);
				$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			}

				//$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				if($sort){
					$items = $dataoke_model->where($where)->order($sort." desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
					$this->assign("lei",$sort1);
				}else {
					$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				}


			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			
			$this->get_quanxian();
			$this->check_wechat();
			$this->send_sort();
			
			$this->display();


    }
}


