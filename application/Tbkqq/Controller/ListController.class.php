<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Tbkqq\Controller;
use Common\Controller\HomebaseController;
/**
 * 文章列表
*/
class ListController extends HomebaseController {

	//文章内页
	public function index() {
		if(sp_is_user_login()){
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));

			$count = M("TbkqqTaokeItem")->where(array("status"=>"1"))->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			if(C('SITE_APPNAME') == 'tc')$order = "no desc";
			else $order = "no";
			$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
				$itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
				$urls = "";
				foreach ($itemurls as $itemurl) {
					$urls .= "下单链接：" . $itemurl['shorturl'] . "<br>";
				}
				$item['memo'] = str_replace("</p><p>","<br>",$item['memo']);
//				$item['memo'] = str_replace("<br/>","",$item['memo']);
//				$item['imgmemo'] = "<p><img src=\'" . $item['img'] . "\'/></p>";
				$item['urls'] = $urls;
				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);

			$this->display(":list_masonry");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function index_weixin() {
		if(sp_is_user_login()){
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));

			$count = M("TbkqqTaokeItem")->where(array("status"=>"1"))->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";
			$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order("no")->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
			//	$itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
				$itemurl = M("TbkqqTaokeItemurl")->where($where)->find();
				$uid = $itemurl['id'];
				$urls = "";
			//	foreach ($itemurls as $itemurl) {
					$urls = "下单链接：" .  $baseurl.$itemurl['id'] . "<br>";
			//	}
				$item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($uid) {
					return convert_dwz($matches[0]) . "&uid=" . $uid;
				},$item['memo']);

//				$item['memo'] = str_replace("<br/>","",$item['memo']);
//				$item['imgmemo'] = "<p><img src=\'" . $item['img'] . "\'/></p>";
				$item['urls'] = $urls;
				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);

			$this->display(":list_masonry");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function listgrid(){
		$proxyid = $_REQUEST['proxyid'];
		$count = M("TbkqqTaokeItem")->where(array("status"=>"1"))->count();

		import('Page');

		$pagesize = 20;
		$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
		$PageParam = C("VAR_PAGE");
		$page = new \Page($count,$pagesize);
		$page->setLinkWraper("li");
		$page->__set("PageParam", $PageParam);
		$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
/*
		$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order("no")->limit($page->firstRow . ',' . $page->listRows)->select();
		foreach ($items as $key=>$item) {
			$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
			$itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
			$urls = "";
			foreach ($itemurls as $itemurl) {
				$urls .= "下单链接：" . $itemurl['shorturl'] . "<br>";
			}
			$item['memo'] = str_replace("</p><p>","<br>",$item['memo']);
//				$item['memo'] = str_replace("<br/>","",$item['memo']);
//			$item['imgmemo'] = str_replace("<br/>","",$item['imgmemo']);
//			$item['imgmemo'] = "<p><img src=\"" . $item['img'] . "\"/></p>";
			$item['urls'] = $urls;
			$items[$key] = $item;
		}
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);

		$this->display(":list_qingtao");
*/

		$baseurl = "http://dwz." . C("BASE_DOMAIN");
		$imgurl = "http://img." . C("BASE_DOMAIN");
		if(C('SITE_APPNAME') == 'tc')$order = "no desc";
		else $order = "no";
		$mm1 = "  \n------------------------------
长按复制这条消息，打开→手机淘宝→即可领卷下单";
		$mm2 = "";

		if($proxyid == '001' || $proxyid == '0001') $www = 'www';
		else $www = $proxyid;
		$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
		$mm4 = "";
		$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
		foreach ($items as $key=>$item) {
			$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
			$itemurl = M("TbkqqTaokeItemurl")->where($where)->find();
			$uid = $itemurl['id'];

			$item['urls'] = "下单链接：" .  $baseurl. "/?id=" .$itemurl['id'] . "<br>";
			$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
			$item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($uid) {
					return convert_dwz($matches[0]) . "&uid=" . $uid;
				},$item['memo']) . $mm1 . $kouling .$mm2 . $mm4;

			$item['imgmemo'] = $imgurl. "/" .$item['id'] . ".jpg";

			$items[$key] = $item;
		}
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);
		$this->display(":list_pc");
	}

	public function qingtao() {
		if(sp_is_user_login()){
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));

			$count = M("TbkqqTaokeItem")->where(array("status"=>"1"))->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$imgurl = "http://img." . C("BASE_DOMAIN");

			if(C('SITE_APPNAME') == 'tc')$order = "no desc";
			else $order = "no";
			$mm1 = "  \n------------------------------
长按复制这条消息，打开→手机淘宝→即可领卷下单";
			$mm2 = "";

			if($proxyid == '001' || $proxyid == '0001') $www = 'www';
			else $www = $proxyid;
			$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
			$mm4 = "";
			$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
				$itemurls = M("TbkqqTaokeItemurl")->where($where)->select();
				$urls = "";
				foreach ($itemurls as $itemurl) {
					$urls .= "下单链接：" . $itemurl['shorturl'] . "<br>";
//					$urls .= "下单链接：" . $baseurl.$itemurl['id'] . "<br>";
				}
				$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];
				$item['memo'] =  str_replace("</p><p>","<br>",$item['memo']). $mm1 . $kouling .  $mm2 . $mm4;

//				$item['memo'] = str_replace("<br/>","",$item['memo']);
				$item['imgmemo'] = $imgurl. "/" .$item['id'] . ".jpg";
				$item['img'] = $item['img'] . '_290x290.jpg';
				$item['urls'] = $urls;
				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);

			$this->display(":list_qingtao");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function weixin() {
		if(sp_is_user_login()){
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));

			$count = M("TbkqqTaokeItem")->where(array("status"=>"1"))->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$baseurl = "http://dwz." . C("BASE_DOMAIN") . "/?id=";

			$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order("no")->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
				$itemurl = M("TbkqqTaokeItemurl")->where($where)->find();
				$uid = $itemurl['id'];
				$urls = "";
				//	foreach ($itemurls as $itemurl) {
				$urls = "下单链接：" .  $baseurl.$itemurl['id'] . "<br>";
				//	}
				$item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($uid) {
					return convert_dwz($matches[0]) . "&uid=" . $uid;
				},$item['memo']);
//				$item['memo'] = str_replace("<br/>","",$item['memo']);
				$item['imgmemo'] = str_replace("<br/>","",$item['imgmemo']);
//				$item['imgurl'] =
				$item['urls'] = $urls;
				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);

			$this->display(":list_qingtao");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function oneclick() {
		if(sp_is_user_login()){
			$t = $_GET['t'];//手机1，其他pc
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));
			$appname = C('SITE_APPNAME');
			$count = M("TbkqqTaokeItem")->where(array("status"=>"1"))->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$baseurl = "http://dwz." . C("BASE_DOMAIN");
			$imgurl = "http://img." . C("BASE_DOMAIN");
			if($appname == 'tc' || $appname == 'yhg')$order = "no desc";
			else $order = "no";
			//if(C('SITE_APPNAME') == "yhg")$mm1 = "";
			//else
			$mm1 = "  \n------------------------------
长按复制这条消息，打开→手机淘宝→即可领卷下单";
				$mm2 = "";

			if($proxyid == '001' || $proxyid == '0001') $www = 'www';
			else $www = $proxyid;
			$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
			$mm4 = "";
			$items = M("TbkqqTaokeItem")->where(array("status"=>"1"))->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$where =  "proxyid='" .$proxyid . "' and iid='" . $item['iid'] . "'";
				$itemurl = M("TbkqqTaokeItemurl")->where($where)->find();
				$uid = $itemurl['id'];
//if(C('SITE_APPNAME') == 'yhg')$item['urls'] = "领券下单：" .  convert_dwz($itemurl['qurl']) . "<br>";
				//else
				$item['urls'] = "下单链接：" .  $baseurl. "/?id=" .$itemurl['id'] . "<br>";
				$kouling = $itemurl['quankl']==''?$itemurl['taokl']:$itemurl['quankl'];

				//if(C('SITE_APPNAME') == 'yhg')$item['memo'] = $item['memo'] . $mm1 . $kouling . $mm2  . $mm4;
//else
				$item['memo'] = preg_replace_callback('/(https?\:\/\/.*)/',function ($matches)  use ($uid) {
		return convert_dwz($matches[0]) . "&uid=" . $uid;
	},$item['memo']) . $mm1 . $kouling . $mm2  . $mm4;

				//$item['imgmemo'] = $imgurl. "/dtk/" .$item['id'] . ".jpg";

			$item['imgmemo'] = $imgurl. "/" .$item['id'] . ".jpg";

				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			if($t == '1')	$this->display(":list_mobile");
			else $this->display(":list_pc");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function qq_items() {
		if(sp_is_user_login()){
			$t = $_GET['t'];//手机1，其他pc
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));
			$taoke_model = M('CaijiqqItems','cmf_','DB_DATAOKE');
			$count =$taoke_model->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$baseurl = "http://dwz." . C("BASE_DOMAIN");
			$imgurl = "http://img." . C("BASE_DOMAIN");
			$order = "id desc";
			$proxy = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['user']['user_login']))->order("rand()")->find();
			$mm1 = "";
			$mm2 = "";

			if($proxyid == '001' || $proxyid == '0001') $www = 'www';
			else $www = $proxyid;
			$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
			$mm4 = "";
			$items = $taoke_model->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$data = get_url_data($item['quan_link']);
				if($data['activity_id'] == "")$quan_id = $data['activityId'];
				else $quan_id = $data['activity_id'];
				$url = "http://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=" . $item['type'];


				$item['urls'] = "下单链接：" .  convert_dwz($url) . "<br>";
				$kouling = "";

//				$item['memo'] = $item['memo'] . $mm1 . $kouling . $mm2  . $mm4;
				$aftprice = $item['aftprice'] != ''?$item['aftprice']:$item['price']-$item['coupon_price'];
				$item['memo'] = "【VIP独享】" .$item['d_title']." \n【 原 价 】 ".$item['price']." 元\n【券后价】 ".$aftprice." 元\n【 亮 点 】".$item['intro']."\n━┉┉┉┉∞┉┉┉┉━\n【 下 单 】:复制整段信息，打开→手机淘宝→即可领券下单内部码：".$kouling;

				$item['imgmemo'] = $imgurl. "/dtk/" .$item['id'] . ".jpg";

				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			if($t == '1')	$this->display(":list_mobile");
			else $this->display(":list_pc");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function oneclick_yhg() {
		if(sp_is_user_login()){
			$t = $_GET['t'];//手机1，其他pc
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));
			$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
			$count =$taoke_model->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$baseurl = "http://dwz." . C("BASE_DOMAIN");
			$imgurl = "http://img." . C("BASE_DOMAIN");
			$order = "id desc";
			$proxy = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['user']['user_login']))->order("rand()")->find();
			$mm1 = "";
			$mm2 = "";

			if($proxyid == '001' || $proxyid == '0001') $www = 'www';
			else $www = $proxyid;
			$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
			$mm4 = "";
			$items = $taoke_model->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$data = get_url_data($item['quan_link']);
				if($data['activity_id'] == "")$quan_id = $data['activityId'];
				else $quan_id = $data['activity_id'];
				$url = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=1";


				$item['urls'] = "下单链接：" .  convert_dwz($url) . "<br>";
				$kouling = "";

				$item['memo'] = $item['memo'] . $mm1 . $kouling . $mm2  . $mm4;

				$item['imgmemo'] = $imgurl. "/dtk/" .$item['id'] . ".jpg";

				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			if($t == '1')	$this->display(":list_mobile");
			else $this->display(":list_pc");
		}
		else {
			redirect(__ROOT__."/");
		}

	}

	public function oneclick_qqgroup() {
		if(sp_is_user_login()){
			$t = $_GET['t'];//手机1，其他pc
			//$proxyid = substr($_SESSION['user']['user_login'], -3,3);
			$proxyid = substr($_SESSION['user']['user_login'],strlen(C('SITE_APPNAME')));
			$taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
			$count =$taoke_model->count();

			import('Page');

			$pagesize = 20;
			$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
			$PageParam = C("VAR_PAGE");
			$page = new \Page($count,$pagesize);
			$page->setLinkWraper("li");
			$page->__set("PageParam", $PageParam);
			$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
			$baseurl = "http://dwz." . C("BASE_DOMAIN");
			$imgurl = "http://img." . C("BASE_DOMAIN");
			$order = "id desc";
			$proxy = M("TbkqqTaokeMedia")->where(array("proxy"=>$_SESSION['user']['user_login']))->order("rand()")->find();
			$mm1 = "";
			$mm2 = "";

			if($proxyid == '001' || $proxyid == '0001') $www = 'www';
			else $www = $proxyid;
			$mm3 = "\n------------------------------
省钱网站：http://" . $www . "." .  C("BASE_DOMAIN") ;
			$mm4 = "";
			$items = $taoke_model->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
			foreach ($items as $key=>$item) {
				$data = get_url_data($item['quan_link']);
				if($data['activity_id'] == "")$quan_id = $data['activityId'];
				else $quan_id = $data['activity_id'];
				$url = "http://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $proxy['pid'] ."&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=" . $item['type'];


				$item['urls'] = "下单链接：" .  convert_dwz($url) . "<br>";
				$kouling = "";

				$item['memo'] = $item['memo'] . $mm1 . $kouling . $mm2  . $mm4;

				$item['imgmemo'] = $imgurl. "/dtk/" .$item['id'] . ".jpg";

				$items[$key] = $item;
			}
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			if($t == '1')	$this->display(":list_mobile");
			else $this->display(":list_pc");
		}
		else {
			redirect(__ROOT__."/");
		}

	}
}
