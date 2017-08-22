<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class DemoController extends HomebaseController {
	//查询分类数目
	public function checksort($where){
		
		$dataoke_model = M('CunItems');
		
		foreach($where as $key=>$val){
			if($val = "1=1"){
				continue;
			}
			$where[$key] = "b.".$val; 
		}

		
		//防止循环添加，新建新的where_push数组
		// $where_push = $where;
		// foreach ($type as $key => $val){
		// 	if($val['type_id']!=0){
		// 		$where_push = $where;
		// 		array_push($where_push,"cate_id='{$val['type_id']}'");
		// 	}
		// 	$count = $dataoke_model->where($where_push)->count();
		// 	//echo $dataoke_model->getlastSql();
		// 	$type[$key]['count'] = $count;
			
		// }
		$type = M("CunItemsType")
		->alias("a")
		->field("a.type_id,count(*) as count")
		->join("LEFT JOIN cmf_cun_items as b on a.type_id=b.cate_id")
		->where($where)
		->group("a.type_id")
		->order("a.type_id")
		->select();
		$type[0]['count'] = $dataoke_model->count();
		return $type;
	}
	public function demo(){
		$starttime = $endtime = get_micro_time();
		
		$appname = C("SITE_APPNAME");

			$dataoke_model = M('CunItems');

			$thitime = $endtime;
			$endtime = get_micro_time();
			echo ($endtime - $thitime)."one".PHP_EOL;

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
			$thitime = $endtime;
			$endtime = get_micro_time();
			echo ($endtime - $thitime)."two".PHP_EOL;
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
			$thitime = $endtime;
			$endtime = get_micro_time();
			echo ($endtime - $thitime)."three".PHP_EOL;
			
			
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
			$thitime = $endtime;
			$endtime = get_micro_time();
			echo ($endtime - $thitime)."four".PHP_EOL;

			//在分类送出前插入的条件
			//array_push($where_ands,"cun>='1'");
			$where_ands[]="status!=2";
			$where_ands[]="is_cun=1";
			
			//获取分类，送出分类
			$shoptype = $this->checksort($where_ands);
			$this->assign("shoptype",$shoptype);
			$this->assign("cid",$shop1);
			
			$thitime = $endtime;
			$endtime = get_micro_time();
			echo ($endtime - $thitime)."mysort<br>";
			
			//全部排序展示
			if($shop1 != '全部'){
				array_push($where_ands,"cate_id='{$type}'");
			}

			$where= join(" and ", $where_ands);
			$thitime = $endtime;
			$endtime = get_micro_time();
			echo ($endtime - $thitime)."fire".PHP_EOL;
			$starttime = get_micro_time();
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
			$endtime = get_micro_time();
			echo ($endtime - $starttime)."count".PHP_EOL;
			
			$starttime=get_micro_time();
			if($sort){
				$items = $dataoke_model->where($where)->order($sort." desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
				$this->assign("lei",$sort1);
			}else {
				$items = $dataoke_model->where($where)->order("cun desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			}
			$endtime = get_micro_time();
			echo ($endtime - $starttime)."select".PHP_EOL;

			$starttime = get_micro_time();
			$this->assign("formget",$_GET);
			$content['items']=$items;
			$content['page']=$page->show('default');
			$content['count']=$count;
			$this->assign("lists",$content);
			$endtime = get_micro_time();
			echo ($endtime - $starttime)."et".PHP_EOL;
			
			$starttime = get_micro_time();
			$this->check_wechat();
			$this->get_quanxian();
			$this->send_sort();
			$this->display("demoh");
			$endtime = get_micro_time();
			echo $endtime - $starttime."end".PHP_EOL;
			
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
	//送出商品分类信息
	public function send_sort(){
		$rs = M("CunItemsType")->order("type_id ASC")->select();
		if($rs){
			$this->assign("sortname",$rs);
		}
		return;
	}
	public function test(){
		echo 123;
	}
	public function demo1(){
		$this->display();
	}
	public function teststatus(){
		//判断id是否存在
		$uid = sp_get_current_userid();
		
		if($uid){
			$sta = M("UserPidStatus")->where("user_id=$uid")->find();
			dump($sta);
			if(empty($sta) || $sta['status']==2){
				$pid = 'mm_120456532_21582792_72310921';
			}else{
				//获取pid
				$rs = M("UsersMes")->where("id=$uid")->find();
				dump($rs);
				if($rs){
					$pid = $rs['pid'];
				}else{
					$pid = 'mm_120456532_21582792_72310921';
				}
			}
		}else{
			$uid = 0;
			$pid = 'mm_120456532_21582792_72310921';
		}
		echo $uid .'<br>'. $pid;
	}
	public function openpid(){
		$uid = I("get.uid");
		$sta = M("UserPidStatus")->where("user_id={$uid}")->find();
		$data = array(
				"user_id"=>$uid,
				"status"=>'1'
			);
		if(empty($sta)){
			echo 1;
			M("UserPidStatus")->add($data);
		}else{
			echo 2;
			M("UserPidStatus")->where("user_id={$uid}")->save($data);
		}
		$uip = get_client_ip();
		$ab = array(
			"uid"=>$uid,
			"operate"=>291,
			"time"=>array("exp"=>"NOW()"),
			"ip"=>$uip
		);
		//记录操作
		$rs = M("UserOperate")->add($ab);
		if($rs){
			$this->success("权限给与成功");
		}else{
			$this->error("权限给予失败");
		}
	}
	
	public function closepid(){
		$uid = I("get.uid");
		$sta = M("UserPidStatus")->where("user_id={$uid}")->find();
		$data = array(
				"user_id"=>$uid,
				"status"=>2
			);
		if(empty($sta)){
			M("UserPidStatus")->add($data);
		}else{
			M("UserPidStatus")->where("user_id={$uid}")->save($data);
		}
		$uip = get_client_ip();
		$ab = array(
			"uid"=>$uid,
			"operate"=>292,
			"time"=>array("exp"=>"NOW()"),
			"ip"=>$uip
		);
		//记录操作
		$rs = M("UserOperate")->add($ab);
		if($rs){
			$this->success("权限关闭成功");
		}else{
			$this->error("权限关闭失败");
		}
	}
	public function operate_record($op){
		$uip = get_client_ip();
		$uid = sp_get_current_userid();
		$ab = array(
			"uid"=>$uid,
			"operate"=>$op,
			"time"=>array("exp"=>"NOW()"),
			"ip"=>$uip
		);
		//记录操作
		return M("UserOperate")->add($ab);
	}
	public function ye(){
		$ab = array(
			"uid"=>2,
			"operate"=>2,
			"time"=>array("exp"=>"NOW()"),
			"ip"=>2
		);
		//记录操作
		return M("UserOperate")->add($ab);
	}
	public function index(){
		$appname = C("SITE_APPNAME");
		$ab = C("URL_ROUTE_RULES");

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
			
			$this->send_sort();

			// $user = sp_get_current_user();
			// $this->assign("user",$user);
			// //判断有无pid权限
			// $sta = M("UserPidStatus")->where("user_id={$user['id']} and status=1")->find();	
			// if($sta){
			// 	$this->assign('spid',1);
			// }else{
			// 	$this->assign('spid',2);
			// }
			// //输出pid表
			// $user_model = M('UsersMes');
			// $rs = $user_model->where("id={$user['id']}")->find();
			$this->assign('pid',$rs);
				
			$this->display("demo");
	}
	//curl发送数据方法
	public function send_url($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		//返回php数据
		return json_decode($result,true);
	}
	public function tbk(){
		$c = new TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$req = new TbkItemCouponGetRequest;
		$req->setPlatform("1");
		$req->setCat("16,18");
		$req->setPageSize("1");
		$req->setQ("女装");
		$req->setPageNo("1");
		$req->setPid("mm_123_123_123");
		$resp = $c->execute($req);
		dump($resp);
	}
	public function ypy(){
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
		
		//在分类送出前插入的条件
		array_push($where_ands,"cun>='1'");
			
		//全部排序展示
		if($shop1 != '全部'){
			array_push($where_ands,"cate_id='{$type}'");
		}
		
		$where= join(" and ", $where_ands);
		
		$count = $dataoke_model->where($where)->count();
		
		$pagesize = 60;
		
		import('Page');
		
		
		$pagetpl='{first}{prev}{liststart}{list}{listend}{next}{last}';
		$PageParam = C("VAR_PAGE");
		$page = new \Page($count,$pagesize);
		$page->setLinkWraper("li");
		$page->__set("PageParam", $PageParam);
		$page->SetPager('default', $pagetpl, array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
		
		if($sort){
			$items = $dataoke_model->where($where)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
			$this->assign("lei",$sort1);
		}else {
			$items = $dataoke_model->where($where)->order("cun desc,id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		}
		
		
		$this->assign("formget",$_GET);
		$content['items']=$items;
		$content['page']=$page->show('default');
		$content['count']=$count;
		$this->assign("lists",$content);
		
		
		$this->display();
	}
	public function wpd(){
		$data = array(
			1=>1,
			2=>2,
		);
		$da = json_encode($data);
		echo $da;
	}
	public function set(){
		$this->start_session(60);
		dump(session("haha","wawa"));
		dump(session);
	}
	public function a(){
		dump(session("haha"));
		dump(session);
	}
	public function start_session($expire = 0){
		if ($expire == 0) {
			$expire = ini_get('session.gc_maxlifetime');
		} else {
			ini_set('session.gc_maxlifetime', $expire);
		}
		if (empty($_COOKIE['PHPSESSID'])) {
			session_set_cookie_params($expire);
			session_start();
		} else {
			session_start();
			setcookie('PHPSESSID', session_id(), time() + $expire);
		}
	}
	public function boy(){
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
			
			$this->get_quanxian();
			$this->send_sort();
			$this->display();
		$demo = A("Tbkqq/AdminCuntao");
		echo 123124;
		$demo->test();
	}
	public function testforeach(){
		$arr = array(
			"a",
			"b",
			"c",
			"d",
			"e",
			"f",
			"g",
		);
		$a = 1;
		foreach($arr as $val){
			
			echo $val;
			if($a == 3){
				$a = 0;
				echo $a."<br>";
			}
			$a++;
		}
	}
	public function test_se(){

		dump(session());
	}
}