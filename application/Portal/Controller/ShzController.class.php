<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class ShzController extends HomebaseController {
	private $param;
	private $endpoint;
	private $model;
	
	public function demo(){
		//生成用户识别码
		$this->param = array(
				"APPID: 1704201628538164",
				"APPKEY: 2dd79739501eabd2c06b788d47f183bf",
		);
		//网站地址
		$this->endpoint = 'http://gateway.shihuizhu.net/open';
		$this->model = 'CunItems';
		$this->get_items();
	}
	
	//获取商品
	public function get_items(){
		//获取2690与优惠猪类型对应列表
		$lei = M("CunZhuType")->order("zhu_tid")->select();
		foreach ($lei as $val){
			$this->gettl($val['zhu_tid'],$val['num_tiid']);
		}
		return $lei;

	}
	//获取每页商品
	public function gettl($lei,$num_tiid){
		$url = $this->endpoint.'/goods/'.$lei.'/1';
		echo $url."<url";

		$itemarr = $this->send_url($url);
		
		$page = $itemarr['pagecount'];
		$this->add_item($itemarr,$num_tiid);
		dump($page);
		for($i=2;$i<=$page;$i++){
			$url = $this->endpoint.'/goods/'.$lei.'/'.$i;
			$itemsarr = $this->send_url($url);
			$this->add_item($itemsarr,$num_tiid);
			echo 2;
		}
	}
	//商品插入数据库
	public function add_item($arr,$num_tiid){
		foreach ($arr['result'] as $val){
			//查重
			$same = M($this->model)->where("num_iid={$val['gid']}")->find();
			if(empty($same)){
				$item['num_iid']=$val['gid'];
				$item['status'] = 1;
				$item['add_time']=time();
				$item['uid'] = 1;
				$item['uname']='admin';
				$item['quanurl'] = $val['coupon_url'];
				$item['click_url']=$val['new_url'];
				$item['title'] = $val['title'];
				//$item['pic_url']='http:'.$val['thumb'];
				$item['price'] = $val['prime'];
				$item['volume'] = $val['final_sales'];
				$item['cate_id'] = $num_tiid;
				$item['coupon_start_time'] = $val['timeline'];
				$item['coupon_end_time']= $val['stoptime'];
				$item['type'] = 1;
				$item['source']='shihuizhu';
				$item['cun'] = null;
				$item['intro']=$val['intro_foot'];
				$item['coupon_price'] = $val['price'];
				$item['commission_rate'] = $val['ratio'];
				//去掉_400x400.jpg尾巴
				$val['thumb'] = str_replace('_400x400.jpg','',$val['thumb']);
				echo $item['pic_url']='http:'.$val['thumb'];

				//获取sellerId
				$info = get_item_info($val['gid']);
				$item['sellerId'] = $info->seller_id;

				//添加条目
				$result = M($this->model)->add($item);
			}
		}
	}
	//curl发送数据方法
	public function send_url($url){
		//echo $url;
		if(is_null($url)){
			//错误信息
			exit;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->param); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		//返回php数据
		//dump($result);
		return json_decode($result,true);
	}
}