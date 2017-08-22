<?php
// +----------------------------------------------------------------------
// | 2609
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.2690.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: changeheart
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
/**
 * 首页
 */
class ApiController extends AdminbaseController {
	protected $item_model;
	protected $api_model;

	function _initialize(){
		parent::_initialize();
		$this->item_model = D("Portal/CunItems");
		$this->api_model = D("Portal/Api");
	}

	//api主页面
	public function index(){
		echo 11;
	}

	public function test(){
		echo 222;
	}
	public function create_apikey(){
		function get_password( $length = 8 ) {
			$str = substr(md5(time()), 0, $length);
			return $str;
		}
	}
}