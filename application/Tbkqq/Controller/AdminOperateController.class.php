<?php
// +----------------------------------------------------------------------
// | 2690 Village junior promotion tool
// +----------------------------------------------------------------------
// | Used to display background operation records
// +----------------------------------------------------------------------
// | Author: changeheart
// +----------------------------------------------------------------------
namespace Tbkqq\Controller;
use Common\Controller\AdminbaseController;
class AdminOperateController extends AdminbaseController {
	protected $o_model;
	protected $m_model;
	function _initialize() {
		parent::_initialize();
		$this->o_model = D("Tbkqq/UserOperate");
		$this->m_model = D("Tbkqq/Menu");
	}
	//header page
	public function index(){
		$this->_list();
		$this->display();
	}
	//list of goods
	public function _list(){

		$where_ands = array();
		$class = array(
			0=>"status"
			);
		$sort = array(
			0=>"time",
			1=>"",

			);
		$where_ands = $this->search_method($where_ands);
		$where_ands = $this->class_query();
		$order = $this->sort_query();
			
		$count = $this->m_model
		->alias("a")
		->join("cmf_user_operate as b on a.id=b.operate")
		->where($where_ands)
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
	//the search method
	public function search_method($where){
		$kw = I("post.search.kw",NULL);
		$kwarr = array(
			0=>	array("key"=>"name","op"=>"like",""=>"");
			);
		$arr = array();
		if($kw){
			foreach($kwarr as $val){
				$arr[] = "{$val['key']} {$val['op']} '%{$kw}%'";
			}
		}
		$or = join(" or ",$arr);
		if($or == ''){
			$or = 1;
		}
		array_push($where,"{$or}");
		return $where;
	}
	//classiification of query
	public function class_query($namearr,$where){
		foreach($namearr as $name){
			$status = I("post.status.{name}",NULL);
			if(!is_null($status) && $status!=0){
				$where[] = "{$name}={$status}";
			}
		}
		if(empty($where)){
			$where[] = "1";
		}
		return $where;
	}
	//Sorting query
	public function sort_query($namearr,$where,$default_sort="time DESC"){
		if(empty($namearr)){
			return $default_sort;
		}
		$where = '';
		$status = array(
			0=>"ASC",
			1=>"DESC",
			);
		foreach($namearr as $name){
			$sort = I("post.{$name['name']}",NULL);
			if(!is_null($sort)){
				$where .= "{$name['name']} {$status[$sort]},";
		
			}
		}	
		//$where .= $default_sort;
		$where = trim($where,",");

		return $where;
	}
}