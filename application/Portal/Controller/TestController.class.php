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
class TestController extends HomebaseController {
	
    //首页
	public function index(){
			$this->display();
			
	}
	public function ajax(){
		$a = I("post.id",NULL);
		if(is_null($a)){
			echo 'haha';
		}else{
			echo '666';
		}
	}
	//查询分类数目
	public function checksort($where){
		
		$appname = C("SITE_APPNAME");
		
		$dataoke_model = M('CunItems');
		
		
		//获取分类信息表
		$type = M("CunItemsType")->group("type_id")->select();
		
		//防止循环添加，新建新的where_push数组
		$where_push = $where;
		foreach ($type as $key => $val){
			if($val['type_id']!=0){
				$where_push = $where;
				array_push($where_push,"cate_id='{$val['type_id']}'");
			}
			$count = $dataoke_model->where($where_push)->count();
			//echo $dataoke_model->getlastSql();
			$type[$key]['count'] = $count;
			
		}
		return $type;
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
	public function send2(){
		set_time_limit(0);
		$rs = M("CunItems")->where("cun>=1")->select();
		$dd = array();
		foreach ($rs as $val){
			$active = $val['click_url'];
			$ac_id = explode('&',end(explode('?',$active)));
			$data = array();
			foreach($ac_id as $acval){
				$acfun = explode('=',$acval);
				$data[$acfun[0]]=$acfun[1];
			}
			$url = "http://uland.taobao.com/cp/coupon?activityId={$data['activityId']}&pid=mm_120456532_20542124_69830211&itemId={$val['num_iid']}&src=cd_cdll";
			$hulala=get_coupon_info_v1($url);
			if(!$hulala){
				$dd[]=array(
					'id'=>$val['num_iid'],
					'url'=>$val['click_url'],
					'activity'=>$data['activityId'],
					'acurl'=>$url,
					'item'=>$hulala,
				);
				
			}
			sleep(1);
			
		}
		$this->assign('fee',$dd);
		$this->display("index");
	}
	public function send1(){
		set_time_limit(0);
		$rs = M("CunItems")->where("cun>=1")->select();
		$dd = array();
		foreach ($rs as $val){
			$active = $val['click_url'];
			$ac_id = explode('&',end(explode('?',$active)));
			$data = array();
			foreach($ac_id as $acval){
				$acfun = explode('=',$acval);
				$data[$acfun[0]]=$acfun[1];
			}
			$url = "http://uland.taobao.com/cp/coupon?activityId={$data['activityId']}&pid=mm_120456532_20542124_69830211&itemId={$val['num_iid']}&src=cd_cdll";
			$hulala=get_coupon_info_v1($url);
				$dd[]=array(
					'id'=>$val['num_iid'],
					'url'=>$val['click_url'],
					'activity'=>$data['activityId'],
					'acurl'=>$url,
					'item'=>$hulala,
				);
			sleep(0.5);
			
		}
		$this->assign('fee',$dd);
		$this->display("index");
	}
	public function geturl(){
		$rs = M("CunItems")->where("num_iid=550098166029")->select();
		$dd = array();
		foreach ($rs as $val){
			$active = $val['click_url'];
			$ac_id = explode('&',end(explode('?',$active)));
			$data = array();
			foreach($ac_id as $acval){
				$acfun = explode('=',$acval);
				$data[$acfun[0]]=$acfun[1];
				
			}
			dump($data);
		}
		
	}
	public function send3(){
		set_time_limit(0);
		$item = M("CunItems");
		$operate = M("UserOperate");
        $rs = $item->where("cun>=1")->select();
        $feewrong = array();
        //$retstatus = array();
        foreach ($rs as $val){
            $active = $val['click_url'];
            $ac_id = explode('&',end(explode('?',$active)));
            $data = array();
            foreach($ac_id as $acval){
                $acfun = explode('=',$acval);
                $data[$acfun[0]]=$acfun[1];
            }
            $url = "http://uland.taobao.com/cp/coupon?activityId={$data['activityId']}&pid=mm_120456532_20542124_69830211&itemId={$val['num_iid']}&src=cd_cdll";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            
            curl_close($ch);
            $resule = json_decode($result,true);
            if($resule['result']['retStatus']!=0){
                $feewrong[]=array(
                    'id'=>$val['num_iid'],
                    'url'=>$val['click_url'],
                    'activity'=>$data['activityId'],
                    'acurl'=>$url,
                );
                //$item->where("num_iid={$val['num_iid']}")->delete();
                //$feewrong[] = $val['num_iid'];
                //admin_record_operation(335,$val['num_iid']);
                // echo $item->getLastSql();
                // $o_data = array(
                //     "uid"=>1162,
                //     "operate"=>335,
                //     "ip"=>1162,
                //     "time"=>array("exp"=>"NOW()"),
                //     "explain_i"=>$val['num_iid'],
                //     );
                // $operate->add($ab);
                // echo $val['num_iid']."<br>";

            }
            
            sleep(0.5);
        }
        $this->assign('fee',$feewrong);
        $this->display("index");
	}
	public function send(){
		set_time_limit(0);
		$rs = M("CunItemsCopy23")->where("cun>=1")->select();
		$feewrong = array();
		//$retstatus = array();
		foreach ($rs as $val){
			$active = $val['click_url'];
			$ac_id = explode('&',end(explode('?',$active)));
			$data = array();
			foreach($ac_id as $acval){
				$acfun = explode('=',$acval);
				$data[$acfun[0]]=$acfun[1];
			}
			$url = "http://uland.taobao.com/cp/coupon?activityId={$data['activityId']}&pid=mm_12045642_20542124_69830211&itemId={$val['num_iid']}&src=cd_cdll";
			$resule = $this->send_curl($url);
			if($resule['result']['retStatus']!=0){
				$feewrong[]=array(
					'id'=>$val['num_iid'],
					'url'=>$val['click_url'],
					'activity'=>$data['activityId'],
					'acurl'=>$url,
				);
			}
			/*
			if($resule['result']['retStatus']==2){
				$retstatus[]=array(
						'id'=>$val['num_iid'],
						'url'=>$val['click_url'],
						'activity'=>$data['activityId'],
				);
			}
			*/
			sleep(0.5);
		}
		$this->assign('fee',$feewrong);
		//$this->assign('ret',$retstatus);
		$this->display("index");
		
	}
	public function a(){
		$a = M("Users")->where("id=1166")->find();
		echo $a['user_login'];
	}
	public function send_curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		
		curl_close($ch);
		return json_decode($result,true);
	}
	public function test(){
		$rs = M("CunItems")->add(array("quan_url"=>NULL));
		echo M("CunItems")->getLastSql();
		echo 22;
	}

	 public function check_unuse_quan(){
        set_time_limit(0);
        $item = M("CunItemsCopy1");
        $operate = M("UserOperateCopy");
        $test = M("Test");
        $where = array(
        	"cun"=>array("egt",1),
        );
        $count = $item->where($where)->count();
        $page = 100;
        $pagenum = ceil($count/$page);
        $pagenow = 0;
        //echo "page {$page},count {$count},pagenow {$pagenow},pagenum {$pagenum}";
        
        for(;$pagenow<$pagenum;$pagenow++){
        	$pagels = $pagenow*$page;
        	 $rs = $item->field("num_iid,click_url")->where($where)->limit("{$pagels},{$page}")->select();
	        foreach ($rs as $val){
	            if(!is_numeric($val['num_iid'])){
	            	echo 1;
	                continue;
	            }
	            $active = $val['click_url'];
	            $ac_id = explode('&',end(explode('?',$active)));
	            $data = array();
	            foreach($ac_id as $acval){
	                $acfun = explode('=',$acval);
	                $data[$acfun[0]]=$acfun[1];
	            }
	            $url = "http://uland.taobao.com/cp/coupon?activityId={$data['activityId']}&pid=mm_120456532_20542124_69830211&itemId={$val['num_iid']}&src=cd_cdll";
	            try{
	            	$ch = curl_init();
		            curl_setopt($ch, CURLOPT_URL, $url);
		            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		            $result = curl_exec($ch);
		            
		            curl_close($ch);
	            }catch(Exception $e){
	            	echo 2;
	            	continue;
	            }
	            
	           // echo "haha";

	            $resule = json_decode($result,true);
	            //if($status || !is_null($rsesult)){
	                $status = 1;
	                $num += 1;
	                 if($resule['result']['retStatus']!=0){
	                    $item->where("num_iid={$val['num_iid']}")->delete();
	                    $o_data = array(
	                        "uid"=>1162,
	                        "operate"=>335,
	                        "ip"=>1162,
	                        "time"=>array("exp"=>"NOW()"),
	                        "explain_i"=>$val['num_iid'],
	                    );
	                    $operate->add($o_data);
	                    $test->add(array("id"=>$url));
	                }else{
	                	$item->where("num_iid={$val['num_iid']}")->save(array("vol_day"=>2));
	                }
	               
        	}
        	 sleep(10);
           
       }
    }
    public function show_unuse_items(){
    	$model = M("CunItems");
    	// $where = array(
    	// 	"vol_day"=>1
    	// );
    	// $rs = $model->field("num_iid as id,click_url as url")->where($where)->limit(100,100)->select();
    	$rs = $model->field("click_url as url")->where("source='haodanku'")->limit("100")->select();
    	$qb = array();
    	foreach($rs as $val){
    		$qb[] = array(
    			"url"=>$val['url'],
    			"id"=>1,
    		);
    	}
    	$this->assign('fee',$qb);
		$this->display("index");
    }
    public function show_use_items(){
    	$model = M("CunItemsCopy1");
    	$where = array(
    		"vol_day"=>2
    	);
    	$rs = $model->field("num_iid as id,click_url as url")->where($where)->limit(100,100)->select();
    	$this->assign('fee',$rs);
		$this->display("index");
    }
	public function check_cun(){
		set_time_limit(0);

		$model = M("CunItemsCopy3");
		$count = $model->count();
		$page = 500;
		$pagenow = 0;
		$pagenum = ceil($count/$page);
		for(;$pagenow<$pagenum;$pagenow++){

		

			$rs = $model->field("num_iid")->where("is_cun is NULL")->limit(($pagenow*$page).",".$page)->select();
			// $arr = array();
			// foreach($rs as $val){
			// 	if(!is_cun($val['num_iid'])){
			// 		$arr[]=array(
			// 			0=>$val['num_iid'],
			// 		);
			// 	}
			// 	sleep(0.5);
			// }
			// $this->assign("arr",$arr);
			foreach($rs as $val){
				
				if(!is_numeric($id)){
					continue;
				}
				try{
					$status = is_cun($val['num_iid']);
				}catch(Exception $e){
					continue;
				}
				if($status){
					$model->where("num_iid={$val['num_iid']}")->save(array("is_cun"=>1));
				}else{
					$model->where("num_iid={$val['num_iid']}")->save(array("is_cun"=>2));
				}
				
			}
			sleep(5);

		}
		echo "end";
	}
	public function getshort_url($url,$data=NULL){
		if(!is_string($url)){
			return false;
		}
		Vendor('TaobaoApi.TopSdk');
		data_default_timezone_set('Asia/Shanghai');
		$c = new TopClient;
		$token = C('TOKEN');
		$key = array_rand($token);
		$c->appkey = $token[$key]['TOKEN_APPKEY'];
		$c->secreKey = $token[$key]['TOKEN_SECRETKEY'];
		$c->format = 'json';
		$req = new  WirelessXcodeCreateRequest;

		$req->setBizCode("top");
		$req->setContent($url);

		if(is_null($data) || is_array($data)){
			$style = new QrCodeStyle;
			$style->bg_color = $data['bg_color'];
			$style->logo = $data['logo'];
			$style->level = $data['level'];
			$style->color = $data['color'];
			$style->margin = $data['margin'];
			$style->size = $data['size'];
			$req->setStyle(json_encode($style));
		}
		
		$resp = $c->execute($req,$c);
		dump($resp->model);
	}
	public function send_url(){
		$url = "https%3A%2F%2Fuland.taobao.com%2Fcoupon%2Fedetail%3FactivityId%3D201141d69059427a8af146fb98d6242e%26pid%3Dmm_120456532_20542124_69830211%26itemId%3D525103259432%26src%3Dcd_cdll";
		$this->make_short_url($url);
	}
	public function make_short_url($url){
		if(!is_string($url)&&is_null($url)){
			return false;
		}
		$url = "http://short.taokemiao.com/ajax/short?_=1496914317019&callback=jQuery19009248923093297237_1496914317017&url=".$url;
		echo $url;
		$content = http_get_content($url);
		dump($content);
	}
	public function show_token(){
		get_token_api();
	}
	public function jvhuasuan(){
		$url = "https://detail.ju.taobao.com/home.htm?spm=a220o.1000855.1998059570.1.mcsK95&item_id=";
	}
	public function get_haodanku(){
		set_time_limit(0);
		$url = "http://www.haodanku.com/index/index/nav/1/starttime/7/cuntao/1.html?json=true";
		$str = file_get_contents($url);
		if($str == '[]'||$str=='')echo "web wrong";
		$json = json_decode($str,true);
		$a = array();
		foreach($json as $val){
			
			echo "<br>".$val['activity_type']."<br>";
			$a[]=$val['activity_type'];
		}
		$this->assign("ac",$a);
		$this->display("index");
	}
	 public function get_cun_dataoke(){

        set_time_limit(0);
        echo "get_dataoke start";
        $dataoke_model = M("CunItemsCopy7");

        for($p=1;;$p++){
        	echo "0";
            $str = "";
            $Appkey = "lrkq59badw";
            $u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey={$Appkey}&v=2&page={$p}";
            echo $u;
            $str = file_get_contents($u);
            if($str == '[]'|| $str == '')break;
            $datype = M("CunDaType")->select();
            $json = json_decode($str,true);
            if($json){
            	$json = $json['result'];
                foreach($json as $item){
                    unset($coupon_info);
                    $data = array();

                    $data['num_iid'] = $item['GoodsID'];
                	$dataoke_item = $dataoke_model->where(array("num_iid"=>$item['GoodsID']))->find();
                    if($dataoke_item){
                    	echo "2";
                    	continue;
                    }
                    if(is_cun($item['GoodsID'])){
						$data["is_cun"]=1;
					}else{
						$data["is_cun"]=2;
					}
                    $data['title'] = $item['Title'];
                    $data['dtitle'] = $item['D_title'];
                    $data['intro'] = $item['Introduce'];
                    $data['pic_url'] = $item['Pic'];
                    foreach($datype as $val){
                    	if($val['da_tid'] == $item['Cid']){
                    		$data['cate_id'] = $val['num_tiid'];
                    		break;
                    	}
                    }

                    $data['price'] = $item['Org_Price'];
                    $data['quan'] = $item['Quan_price'];
                    $data['coupon_price'] = $item['Price'];


                    $data['quanurl'] = $item['Quan_link'];
                    

                    $urldata = get_url_data($item['Quan_link']);
                    if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
                    else $quan_id = $urldata['activity_id'];
                    if($urldata['seller_id'] != "")$data['sellerId'] = $urldata['seller_id'];
                    else $data['sellerId'] = $urldata['sellerId'];

                    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;

                    $data['add_time'] = time();
                    $data['source'] = 'dataoke';
                    try{
						$dataoke_result =$dataoke_model->add($data);
                    }catch(Exception $e){
                    	echo 3;
                    	continue;
                    }
                    
                }
            }else{
            	echo 4;
            }

        }
    }
}


