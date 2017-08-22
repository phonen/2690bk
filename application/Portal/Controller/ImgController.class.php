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
 
 define('PNG_TEMPLATE', "./model1.jpg");//模板文件
 define('PNG_TEMPLATE_WU', "./model1.jpg");//无水印模板文件
define('PNG_FONT',"./msyh.ttc" );//字体
define('PNG_FONT_YOU',"./simyou.ttf");//2号字体


define('IMG', serialize(array(0,0,700,1000)));
//define('PNG_TOUMING',serialize(array(0,397,444,473))); //透明大小
define('PNG_PIC_RECT', serialize(array(0,0,700,700)));//图片、、一个矩行的左上角和右下角

define('PNG_QR_RECT', serialize(array(442,743,658,959)));//二维码 这里可以调节二维码大小，第1,2个数据是左上角的x，y。第3,4个数据是右下角的x，y。eg：往上增大就改第二个数据

define('PNG_TITLE_TEXT', serialize(array(30,730,400,870)));//标题
define('PNG_PRICE_TEXT', serialize(array(90,890,170,900)));//原价
define('PNG_COUPON_PRICE_TEXT', serialize(array(211,930,330,940)));//优惠后价
define('PNG_DESC_TEXT', serialize(array(30,730,300,870)));//详情
define('PNG_COUPON_TEXT', serialize(array(60,924,111,950)));//优惠券

define('PNG_QR_LEVEL', QR_ECLEVEL_M);//二维码容错率
define('PNG_QR_SIZE', 2);//二维码大小




define ('DT_LEFT', 0);//二进制定位
define ('DT_RIGHT', 1);
define ('DT_CENTER', 2);
define ('DT_FLAG_HORZ', 3);

define ('DT_BOTTOM', 0);
define ('DT_TOP', 4);
define ('DT_VCENTER', 8);
define ('DT_FLAG_VERT', 12);

class ImgController extends HomebaseController {

    //首页
    public function index() {

        $id = $_GET['id'];
        if($id != ''){
            $itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$id))->find();
            if($itemurl){
                $url = $itemurl['shorturl'];
                $this->assign("url",$url);

                if($this->is_weixin()){
                    $item = M("TbkqqTaokeItem")->where(array("iid"=>$itemurl['iid']))->find();
                    if($item){
                        $this->assign("item",$item);

                    }
                    $this->display(":dwz_index_wx");
                }
                else {
                    $this->display(":dwz_index");
                }


            }
            else {
                $itemurl = M("TbkqqTaokeItemurlHistory")->where(array("id"=>$id))->find();
                if($itemurl){
                    $url = $itemurl['shorturl'];
                    $this->assign("url",$url);

                    if($this->is_weixin()){
                        $item = M("TbkqqTaokeItem")->where(array("iid"=>$itemurl['iid']))->find();
                        if($item){
                            $this->assign("item",$item);

                        }
                        $this->display(":dwz_index_wx");
                    }
                    else {
                        $this->display(":dwz_index");
                    }


                }
            }
        }
    }

    public function quan0(){
        $id = $_GET['id'];
        $baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?a=quan&id=";
        $dwz = M("TbkqqDwz")->where("id=$id")->find();
        if($dwz){
            $this->assign("url",$dwz['url']);
            if($this->is_weixin()){
                /*
                if($_GET['uid'] != ""){
                    $itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$_GET['uid']))->find();
                    if($itemurl){
                        $this->assign("shorturl",$baseurl . $id);
                        $this->assign("iid",$itemurl['iid']);
                        $this->display(":dwz_quan_wx1");
                    }
                }
                */
                $this->display(":dwz_quan_wx");
            }
            else{
                header("location:" . $dwz['url']);
            }
        }

    }

    public function quan(){
        $baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?id=";
        $quanurl = "http://dwz." . C("BASE_DOMAIN") . "/?a=quan&id=";
        $id = $_GET['id'];
        $uid = $_GET['uid'];
        if(is_numeric($id)){

            if($this->is_weixin()){
                $this->assign("url",$quanurl . $id . "&uid=" . $uid);
                $this->display(":dwz_quan_wx");
            }
            else {
                if($_GET['uid'] != ""){
                    if(is_numeric($uid)){
                        $itemurl = M("TbkqqTaokeItemurl")->where(array("id"=>$_GET['uid']))->find();
                        if($itemurl){

                            $this->assign("url",$quanurl . $id);
                            $this->assign("shorturl",$baseurl . $_GET['uid']);
                            $this->assign("iid",$itemurl['iid']);
                            $this->display(":dwz_quan_wx1");
                        }
                        else{
                            $itemurl = M("TbkqqTaokeItemurlHistory")->where(array("id"=>$_GET['uid']))->find();
                            if($itemurl){

                                $this->assign("url",$quanurl . $id);
                                $this->assign("shorturl",$baseurl . $_GET['uid']);
                                $this->assign("iid",$itemurl['iid']);
                                $this->display(":dwz_quan_wx1");
                            }
                        }
                    }

                }
                else {
                    $dwz = M("TbkqqDwz")->where("id=$id")->find();
                    if($dwz){
                        if($dwz['type'] == '1'){
                            if(sp_is_mobile()){
                                $this->assign("url",$dwz['url']);
                                $this->display(":dwz_index");
                                exit();
                            }
                        }
                        //$this->assign("url",$dwz['url']);
                        //if($this->is_weixin()){
                        //	$this->display(":dwz_quan_wx");
                        //}
                        //else{
                        header("location:" . $dwz['url']);
                        //}
                    }
                }
            }
        }
    }
//不需要添加pid的
    public function erwei(){
       $uid = I('get.uid',0);
       $bit = 1;
    	$iid = $_GET['id'];
		if($uid){
			$sta = M("UserPidStatus")->where("user_id=$uid")->find();
			if(empty($sta) || $sta['status']==2){
				$pid = 'mm_120456532_21582792_72310921';
			}else{
				//获取pid
				$rs = M("UsersMes")->where("id=$uid")->find();
				if($rs){
					$pid = $rs['pid'];
					$bit = 2;
				}else{
					$pid = 'mm_120456532_21582792_72310921';
				}
			}
    	}else{
    		$pid = 'mm_120456532_21582792_72310921';
    	}
    	$dataoke_model = M('CunItems');
    	$item =$dataoke_model->where("num_iid=$iid")->find();
    	if(!$item['dtitle']){
    		$item['dtitle']=$item['title'];
    	}
    	
		$item['quanurl'] = str_replace('&amp;', '&', $item['quanurl']);
    	$data = get_url_data($item['quanurl']);
    	if($data['activity_id'] == "")$quan_id = $data['activityId'];
    	else $quan_id = $data['activity_id'];
    	$click_url = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id."&pid=".$pid."&itemId=".$iid."&src=qhkj_dtkp&dx=1";
		$this->generatePic($item['dtitle'],$item['pic_url'],$click_url,$item['price'],$item['coupon_price'],$item['intro'],$bit);
		user_record_operation(2,$item["num_iid"]);
    }
    //获取没有包含pid的文案
    public function erwei_test(){
		$obj = I("get.");
		//判断id是否存在
		$uid = sp_get_current_userid();
		$bit = 1;
		if($uid){
			$sta = M("UserPidStatus")->where("user_id=$uid")->find();
			if(empty($sta) || $sta['status']==2){
				$pid = 'mm_120456532_21582792_72310921';
			}else{
				//获取pid
				$rs = M("UsersMes")->where("id=$uid")->find();
				if($rs){
					$pid = $rs['pid'];
					$bit = 2;
				}else{
					$pid = 'mm_120456532_21582792_72310921';
				}
			}
		}else{
			$uid = 0;
			$pid = 'mm_120456532_21582792_72310921';
		}
		/*
		//获取click_url
		if(is_numeric($obj['num_iid']))
		$iid = $obj['num_iid'];
		else{
			$iid_data = $this->get_i_id($obj['num_iid']);
			if(is_numeric($iid_data['id']))$iid = $iid_data['id'];
			else $iid = intval($iid_data['id']);
		}
		$coupon_url = $obj['quanurl'];
		$coupon_url = str_replace('&amp;', '&', $coupon_url);
		$data = $this->get_i_id($coupon_url);
		
		$parameter = explode('&',end(explode('?',$coupon_url)));
		foreach($parameter as $val){
			$tmp = explode('=',$val);
			$tmp[0] = str_replace("amp;","",$tmp[0]);
			$tmp[1] = str_replace("amp;","",$tmp[1]);
			$data["{$tmp[0]}"] = $tmp[1];
		}
		if($data['activity_id'] == "")$quan_id = $data['activityId'];
		else $quan_id = $data['activity_id'];
		
		$obj['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=".$pid."&itemId=" . $iid ."&src=qhkj_dtkp&dx=1";
		*/
		print_r($pid);
		
		/*
		$info = get_item_info($iid);
		//dump($info);
		if($info && !empty($info->zk_final_price)){
		$pic_url = $info->pict_url;
		$price = $info->zk_final_price;
		}else{
			echo 0;
			exit;
		}
		//执行合成图片
		header("Content-type: image/jpg");
		$pic = $this->generatePic($obj['dtitle'],$pic_url,$obj['click_url'],$price,$obj['coupon_price'],$obj['intro'],$bit);
		$this->assign($pic);
		*/
		//$this->assign($pic);
    }
	//$info获取图片链接失败，解决
	public function create_erwei(){
		$obj = I("get.");
		//判断id是否存在
		if($obj["id"]){
			$uid = $obj["id"];
		}else{
			$uid = sp_get_current_userid();
		}
		
		$bit = 1;
		if($uid){
			$sta = M("UserPidStatus")->where("user_id=$uid")->find();
			if(empty($sta) || $sta['status']==2){
				$pid = 'mm_120456532_21582792_72310921';
			}else{
				//获取pid
				$rs = M("UsersMes")->where("id=$uid")->find();
				if($rs){
					$pid = $rs['pid'];
					$bit = 2;
				}else{
					$pid = 'mm_120456532_21582792_72310921';
				}
			}
		}else{
			$uid = 0;
			$pid = 'mm_120456532_21582792_72310921';
		}
		$file = fopen("log.txt","a");
		fwrite($file,$pid);
		fwrite($file,$uid);
		fclose($file);
		
		//获取click_url
		if(is_numeric($obj['num_iid']))
		$iid = $obj['num_iid'];
		else{
			$iid_data = $this->get_i_id($obj['num_iid']);
			//if(is_numeric($iid_data['id']))$iid = $iid_data['id'];
			//else $iid = intval($iid_data['id']);
			foreach($iid_data as $val){
				if(get_item_info($val)){
					$iid = $val;
					break;
				}
			}
		}
		$coupon_url = $obj['quanurl'];
		$coupon_url = str_replace('&amp;', '&', $coupon_url);
		$data = $this->get_i_id($coupon_url);
		
		$parameter = explode('&',end(explode('?',$coupon_url)));
		foreach($parameter as $val){
			$tmp = explode('=',$val);
			$tmp[0] = str_replace("amp;","",$tmp[0]);
			$tmp[1] = str_replace("amp;","",$tmp[1]);
			$data["{$tmp[0]}"] = $tmp[1];
		}
		if($data['activity_id'] == "")$quan_id = $data['activityId'];
		else $quan_id = $data['activity_id'];
		
		$obj['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=".$pid."&itemId=" . $iid ."&src=qhkj_dtkp&dx=1";

		
		$info = get_item_info($iid);
		//dump($info);
		if($info && !empty($info->zk_final_price)){
		$pic_url = $info->pict_url;
		$price = $info->zk_final_price;
		}else{
			echo 0;
			exit;
		}
		//执行合成图片
		header("Content-type: image/jpg");
		$pic = $this->generatePic($obj['dtitle'],$pic_url,$obj['click_url'],$obj['price'],$obj['coupon_price'],$obj['intro'],$bit);
		$this->assign($pic);
		//dump($pic);
	}
	
	//获取
	public function get_i_id($str){
		$data = array();
		$parameter = explode('&',end(explode('%3F',$str)));
		foreach($parameter as $val){
			$tmp = explode('=',$val);
			$tmp[0] = str_replace("amp;","",$tmp[0]);
			$tmp[1] = str_replace("amp;","",$tmp[1]);
			$data[$tmp[0]] = $tmp[1];
		}
		return $data;
	}

    public function qrcode($level=3,$size=4){
        $iid = $_GET['id'];
        $dataoke_model = M('CunItems');
        $item =$dataoke_model->where("num_iid=$iid")->find();
        if($item) {
            Vendor('phpqrcode.phpqrcode');

            $errorCorrectionLevel =intval($level) ;//容错级别
            $matrixPointSize = intval($size);//生成图片大小
//生成二维码图片
            //echo $_SERVER['REQUEST_URI'];
            $object = new \QRcode();
//			$object->png($item['quan_link'],false, $errorCorrectionLevel, $matrixPointSize, 2);
            $object->jpg($item['click_url'], "./itemimg/$iid.jpg", $level, $size);

            $qr_source = imagecreatefromjpeg("./itemimg/$iid.jpg");
            $qr_size = getimagesize("./itemimg/$iid.jpg");
            $target_img = imagecreatetruecolor(400,750);
            $white = imagecolorallocate($target_img,0xFF,0xFF,0xFF);
            $black = imagecolorallocate($target_img,0x00,0x00,0x00);
            imagefill($target_img, 0, 0, $white);

            $img = file_get_contents($item['pic_url']. "_400x400.jpg");
            $img_source = imagecreatefromstring($img);
            $img_size = getimagesizefromstring($img);

            imagecopy ($target_img,$img_source,0,0,0,0,$img_size[0],$img_size[1]);

            $str = $item['dtitle'] . "\n【原 价】 " . $item['price'] . "元 【券后价】 " . $item['coupon_price'] . "元\n 请使用手机村淘扫描下面二维码领券购买";
            imagettftext($target_img,15,0,2,420,$black,"./simkai.ttf",$str);
            imagecopy ($target_img,$qr_source,(400-$qr_size[0])/2,480,0,0,$qr_size[0],$qr_size[1]);
            imagejpeg($target_img);
        }

    }

    public function dtkimg(){
        $id = $_GET['id'];
        $taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
        $item = $taoke_model->where("id=$id")->find();
        if($item) {
            if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_290x290.jpg");  //假设当前文件夹已有图片001.jpg
            else {
                $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                preg_match($preg, $item['imgmemo'], $imgArr);
                $imgurl = $imgArr[1];
                $image = file_get_contents($imgurl);
            }
            header('Content-type: image/jpg');
            echo $image;
        }
    }
    public function img_by_no(){
        $no = $_GET['no'];
        $item = M("TbkqqTaokeItem")->where(array("no"=>$no,"status"=>'1'))->find();
        if($item) {
            if($item['imgmemo'] == '')		$image = file_get_contents($item['img'] ."_290x290.jpg");  //假设当前文件夹已有图片001.jpg
            else {
                $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                preg_match($preg, $item['imgmemo'], $imgArr);
                $imgurl = $imgArr[1];
                $image = file_get_contents($imgurl);
            }
            header('Content-type: image/jpg');
            echo $image;
        }
    }

    private function bitblt($dest,$src,$rect){//写入图片

	$pic = imagecreatefromstring(file_get_contents($src));

	$src_width = imagesx ($pic);
	$src_height = imagesy ($pic);

	$dest_width =  $rect[2]-$rect[0]+1;
	$dest_height = $rect[3]-$rect[1]+1;



	imagecopyresampled ( $dest, $pic, $rect[0], $rect[1], 0, 0, $dest_width, $dest_height, $src_width, $src_height);
}

    private function drawText($dest,$text,$font,$size,$color,$text_rect,$alignment,$strlen=28) {

    if(strlen($text)>$strlen*4){
    	$text = mb_substr($text,0,$strlen,"utf-8")."..";
    }

	$width = $text_rect[2]-$text_rect[0];

	$lines = $this->autowrap($size, 0, PNG_FONT, $text, $width);

	if ( !is_array($lines) ) $lines = [$lines];
	if ( is_array($lines) && count($lines)==0 ) return;

	$rect = ImageTTFBBox($size,0, $font, $lines[0]);
	$line_height = $rect[1]-$rect[5];




	$max_row = ceil(($text_rect[3]-$text_rect[1])/$line_height);
	$rows = $max_row>count($lines)?count($lines):$max_row;

	$height = $rows*$line_height;




	$align = $alignment & DT_FLAG_VERT;
	switch($align) {
		case DT_TOP:
			$y = $text_rect[1];
			break;
		case DT_BOTTOM:
			$y = $text_rect[3];
			break;
		case DT_VCENTER:
			$y = ($text_rect[3]-$text_rect[1]-$height)/2+$text_rect[1]+$line_height;
			break;
	}

	for( $i=0;$i<$rows;$i++ ) {

		$this->outText($dest,$lines[$i],$font,$size,$color,[$text_rect[0], $y, $text_rect[2], $y+$line_height],$alignment);
		$y += $line_height;

	}
}

    private function outText($dest,$text,$font,$size,$color,$text_rect,$alignment) {

	$rect = ImageTTFBBox($size,0, $font, $text);

	$width = $rect[4]-$rect[0];
	$height = $rect[1]-$rect[5];

	$x = 0;
	$y = $text_rect[1];

	$align = $alignment & DT_FLAG_HORZ;

	switch($align) {
		case DT_LEFT:
			$x = $text_rect[0];
			break;
		case DT_RIGHT:
			$x = $text_rect[2]-$width;
			break;
		case DT_CENTER:
			$x = ($text_rect[2]-$text_rect[0]-$width)/2+$text_rect[0];
			break;
	}


	imagettftext($dest, $size, 0, $x, $y, $color, $font, $text);
}

    private function generatePic($dtitle, $pic_url, $click_url, $price, $coupon_price, $intro,$bit=1) {

		$rect=unserialize(IMG);
	$imge=imagecreatetruecolor($rect[2]-$rect[0],$rect[3]-$rect[1]);//生成一副黑色图像


	if($bit == 1){
		$this->bitblt($imge,PNG_TEMPLATE,$rect);//导入模板
	}else{
		$this->bitblt($imge,PNG_TEMPLATE_WU,$rect);//导入模板
	}
	


	$rect = unserialize(PNG_PIC_RECT);
	$this->bitblt($imge, $pic_url, $rect);//导入图片

	$rect= unserialize(PNG_TOUMING);
	$white = imagecolorallocatealpha($imge, 255, 255, 255, 50);//最后一个参数是透明度0-127,127全透明
	imagefilledrectangle($imge, $rect[0], $rect[1], $rect[2], $rect[3]+1, $white);


	$qr = $this->generateQR($click_url);
	$rect = unserialize(PNG_QR_RECT);
	$this->bitblt($imge, $qr, $rect);//导入二维码

	$color = imagecolorallocate($imge, 40, 40, 40);
	$rect = unserialize(PNG_TITLE_TEXT);
	$this->drawText($imge,$dtitle,PNG_FONT_YOU,25,$color,$rect,DT_VCENTER|DT_CENTER,60);//写入标题

	$color = imagecolorallocate($imge, 195, 195, 195);
	$rect = unserialize(PNG_PRICE_TEXT);
	$this->drawText($imge,number_format($price,1),PNG_FONT,14,$color,$rect,DT_LEFT|DT_VCENTER);//写入原始价格

	$color = imagecolorallocate($imge, 255,127,0);
	$rect = unserialize(PNG_COUPON_PRICE_TEXT);
	$this->drawText($imge,number_format($coupon_price,1),PNG_FONT,30,$color,$rect,DT_LEFT|DT_VCENTER);//写入优惠价格

	// $rect = unserialize(PNG_DESC_TEXT);
	// $this->drawText($imge,$intro,PNG_FONT,12,$color,$rect,DT_LEFT|DT_VCENTER);

	$rect = unserialize(PNG_COUPON_TEXT);
	$this->drawText($imge,$price-$coupon_price,PNG_FONT,14,$color,$rect,DT_LEFT|DT_VCENTER);


	imagejpeg($imge);
	imagedestroy($imge);
	return;
    }


    private function generateQR($value) { //获得二维码
        Vendor('phpqrcode.phpqrcode');
        $object = new \QRcode();
        $file = "./itemimg/" . md5($value);
        if ( !file_exists($file)) {
            $object->png($value, $file, PNG_QR_LEVEL, PNG_QR_SIZE,2);
        }
        return $file;
    }

    private function autowrap($fontsize, $angle, $fontface, $string, $width) {//文字换行
	$content = "";
	$lines = [];
	// 将字符串拆分成一个个单字 保存到数组 letter 中
	preg_match_all("/./u",$string,$arr);
	$letter = $arr[0];
	foreach ($letter as $l) {
		$teststr = $content.$l;
		$testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
		// 判断拼接后的字符串是否超过预设的宽度
		if (($testbox[2] > $width) && ($content !== "")) {
			array_push($lines, $content);
			$content = '';
		}
		$content .= $l;
	}
	if ( $content!=='') array_push($lines, $content);
	return $lines;
}

//测试案例
function testGeneratePic() {
	$click_url = 'https://uland.taobao.com/coupon/edetail?activityId=fc52235b4a4c42f6a740978d42ff2e3a&pid=mm_16532_20542124_69830211&itemId=530172981582&src=cd_cdll';

	$pic_url = 'http://img.alicdn.com/imgextra/i3/759605781/TB2n17CaCCI.eBjy1XbXXbUBFXa_!!759605781.jpg';

	$dtitle = '邦贝酷装儿童纯棉内裤三角女童内裤';

	$price = 30.9;

	$coupon_price = 20.5;

	$end_time ='能根据给的参数条件自动换行。。。但是换行之w24后可能会出现乱码。我认为原因应该23dgssdf是年水电费水电费是~~';

	header("Content-type: image/jpg");
	generatePic($dtitle, $pic_url, $click_url, $price, $coupon_price, $end_time);
}
}


