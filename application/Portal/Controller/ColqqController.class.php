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
class ColqqController extends HomebaseController{
	public function index() {
		
	}
	
	public function save_item(){
		if(IS_POST) {
			$appname = C("SITE_APPNAME");
			$msg = $_POST['msg'];
			$source = $_POST['source'];
			

			///////////////////////////////////////////////////
			$arr = explode("\n",$msg);

				$data['dtitle'] = $arr[1];
				$data['intro'] = $arr[5];
				if(preg_match('/https?:\/\/[\w=.?&\/;]+/',$arr[4],$match)) {
					$url = str_replace('amp;', '', $match[0]);
					$host = parse_url($url, PHP_URL_HOST);
					if ($host == 'item.taobao.com' || $host == 'detail.tmall.com' || $host == 'detail.m.tmall.com' || $host == 'item.m.taobao.com' || $host == 'h5.m.taobao.com') {
						$data1 = get_url_data($url);

						$iid = $data1['id'];
					} elseif ($host == 's.click.taobao.com') {
						$data1 = get_item($url);
						$iid = $data1['id'];
					}

					if ($iid != '') {
						$info = get_item_info($iid);
						unset($data['id']);
						$data['num_iid'] = $iid;
						$data['title'] = $info->title;
						$data['pic_url'] = $info->pict_url;
						$data['price'] = $info->zk_final_price;
						$data['nick'] = $info->nick;
						$data['sellerId'] = $info->seller_id;
						$data['volume'] = $info->volume;


							$data['quanurl'] = $quan_link;
							if($quan_link != ""){
								$quan_data = get_url_data($quan_link);
								if($quan_data['activity_id'] == "")$quan_id = $quan_data['activityId'];
								else $quan_id = $quan_data['activity_id'];
								$data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=&itemId=" . $item['iid'] ."&src=qhkj_dtkp&dx=";
							}
							$data['add_time'] = time();

						}
						
							M("CunItemsCopy")->add($data, $options = array(), $replace = true);
					}


				}

		}
	}
	
	
}