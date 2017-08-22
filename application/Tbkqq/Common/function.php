<?php

// +----------------------------------------------------------------------

// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]

// +----------------------------------------------------------------------

// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.

// +----------------------------------------------------------------------

// | Author: Tuolaji <479923197@qq.com>

// +----------------------------------------------------------------------

function get_url_data($str){

    $data = array();

    $parameter = explode('&',end(explode('?',$str)));

    foreach($parameter as $val){

        $tmp = explode('=',$val);

        $data[$tmp[0]] = $tmp[1];

    }

    return $data;

}

/*

function get_item_url($clickurl){

    $headers = get_headers($clickurl, TRUE);

    $tu = $headers['Location'];

    $eturl = unescape($tu);

    $u = parse_url($eturl);

    $param = $u['query'];

    $ref = str_replace('tu=', '', $param);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $ref);

    curl_setopt($ch, CURLOPT_REFERER, $tu);

    curl_setopt($ch, CURLOPT_HEADER, false);

    curl_setopt($ch, CURLOPT_NOBODY,1);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);

    curl_setopt($ch, CURLOPT_MAXREDIRS,2);

    $out = curl_exec($ch);

    $dd =  curl_getinfo($ch);

    curl_close($ch);

    $item_url = $dd['url'];

    return $item_url;

}

*/



function get_location($u){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $u);

    //curl_setopt($ch, CURLOPT_REFERER, $tu);

    curl_setopt($ch, CURLOPT_HEADER, true);

    curl_setopt($ch, CURLOPT_NOBODY,1);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);

    curl_setopt($ch, CURLOPT_MAXREDIRS,2);

    $out = curl_exec($ch);

    $dd =  curl_getinfo($ch);

    //curl_close($ch);

    $ref = $dd['url'];

    return $ref;

}



function get_item($u){

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $u);

    //curl_setopt($ch, CURLOPT_REFERER, $tu);

    curl_setopt($ch, CURLOPT_HEADER, true);

    curl_setopt($ch, CURLOPT_NOBODY,1);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);

    curl_setopt($ch, CURLOPT_MAXREDIRS,2);

    $out = curl_exec($ch);

    $dd =  curl_getinfo($ch);

    //curl_close($ch);

    $ref = $dd['url'];



    $aa = explode('tu=',$ref);

    $tu = $aa[1];

    //$tu = $headers['Location'];

    $eturl = unescape($tu);

    //$ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $eturl);

    curl_setopt($ch, CURLOPT_REFERER, $ref);

    curl_setopt($ch, CURLOPT_HEADER, true);

    curl_setopt($ch, CURLOPT_NOBODY,0);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,false);

    curl_setopt($ch, CURLOPT_MAXREDIRS,2);

    $out = curl_exec($ch);

    $dd =  curl_getinfo($ch);

    curl_close($ch);



    $item_url = $dd['redirect_url'];

    return $data = get_url_data($item_url);



}





function get_dwz($url){

    $ch=curl_init();

    curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");

    curl_setopt($ch,CURLOPT_POST,true);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $data=array('url'=>$url . "&f=tth");

    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

    $strRes=curl_exec($ch);

    curl_close($ch);

    $arrResponse=json_decode($strRes,true);

    if($arrResponse['status']==0)

    {

        /**错误处理*/

        echo iconv('UTF-8','GBK',$arrResponse['err_msg'])."\n";

    }

    /** tinyurl */

    return $arrResponse['tinyurl']."\n";

}



function unescape($str) {

    $ret = '';

    $len = strlen($str);

    for ($i = 0; $i < $len; $i ++)

    {

        if ($str[$i] == '%' && $str[$i + 1] == 'u')

        {

            $val = hexdec(substr($str, $i + 2, 4));

            if ($val < 0x7f)

                $ret .= chr($val);

            else

                if ($val < 0x800)

                    $ret .= chr(0xc0 | ($val >> 6)) .

                        chr(0x80 | ($val & 0x3f));

                else

                    $ret .= chr(0xe0 | ($val >> 12)) .

                        chr(0x80 | (($val >> 6) & 0x3f)) .

                        chr(0x80 | ($val & 0x3f));

            $i += 5;

        } else

            if ($str[$i] == '%')

            {

                $ret .= urldecode(substr($str, $i, 3));

                $i += 2;

            } else

                $ret .= $str[$i];

    }

    return $ret;

}



function export_txt($filename, $data){

    header("Content-type:application/octet-stream");

    header("Accept-Ranges:bytes");

    header("Content-Disposition:attachment;filename=".$filename.".txt");

    header("Expires: 0");

    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");

    header( "Pragma:public ");



    echo $data;

}

function export_csv($filename, $data){

    header("Content-type:text/csv");

    header("Content-Disposition:attachment;filename=".$filename);

    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');

    header('Expires:0');

    header('Pragma:public');

    echo $data;



}



function openhttp_header($url, $post='',$cookie='',$referfer='',$head='')

{

    $header[] = "Host: pub.alimama.com";

//    $header[] = "Accept-Encoding: gzip, deflate, sdch";

    $header[] = "Accept-Language: zh-CN,zh;q=0.8";

    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";

    if($head == '1') {

        $header[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';

        $header[] = "Accept: application/json, text/javascript, */*; q=0.01";

    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_COOKIE,$cookie);

//	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //放在服务器上，会提示出错

    if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

    if($post != "") {

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    }



    $return = curl_exec($ch);



    curl_close($ch);



    return $return;

}



function convert_dwz($matches,$type = '2'){

    $baseurl = "http://dwz." . C("BASE_DOMAIN") ."/?a=quan&id=";



    $dwz = M("TbkqqDwz")->where(array("url"=>$matches))->find();

    if($dwz){

        $id = $dwz['id'];

    }

    else {

        $id = M("TbkqqDwz")->add(array("url"=>$matches,"type"=>$type));



    }

    return $baseurl . $id;

}



function is_weixin(){

    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {

        return true;

    }

    return false;

}



function convert_a($matches){

    return "<a href='" . $matches . "' >" . $matches . "</a>";

}



function excookie($cookies){

    $cookies_arr = explode(";",$cookies);

    foreach($cookies_arr as $data){



    }



    $data = array();

    $parameter = explode(";",$cookies);

    foreach($parameter as $val){

        $tmp = explode('=',$val);

        $data[ltrim($tmp[0])] = $tmp[1];

    }

    return $data;

}



function create_password($pw_length = 6){

    $randpwd = '';

    for ($i = 0; $i < $pw_length; $i++)

    {

        $randpwd .= chr(mt_rand(33, 126));

    }

    return $randpwd;

}



function campaign($cookie,$iid,$s){

    $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;

    $str = openhttp_header($u, '', $cookie);

    $arr = json_decode($str, true);

    $t = time();



    if ($arr['ok'] == '1' && $arr['data']) {

        $rate = 0;

        $cid = '';

        $keeperid = '';

        $post = array();



        foreach ($arr['data'] as $data) {

            if ($data['commissionRate'] > $rate) {

                $rate = $data['commissionRate'];

                $cid = $data['CampaignID'];

                $keeperid = $data['ShopKeeperID'];

            }

        }

        $post['campId'] = $cid;

        $post['keeperid'] = $keeperid;

        $post['applyreason'] = "淘特惠淘客推广申请";

        $cookie_data = excookie($cookie);

        $post['_tb_token_'] = $cookie_data['_tb_token_'];

        $post['t'] = $t;





        $post_str = "campId=" .$post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];

        //print_r($post);

        $u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";

        $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" .$s;

        sleep(1);

        $ret = openhttp_header($u,$post_str,$cookie,$reffer,'1');

        return $ret;

    }

}



function get_qingtaoke_quan($iid){



}



function get_taotoken($data){



    Vendor('TaobaoApi.TopSdk');

    date_default_timezone_set('Asia/Shanghai');

    $c = new TopClient;

    $token = C('TOKEN');

    $key = array_rand($token);

    //$c->appkey = C('TOKEN_APPKEY');

    //$c->secretKey = C('TOKEN_SECRETKEY');

    $c->appkey = $token[$key]['TOKEN_APPKEY'];

    $c->secretKey = $token[$key]['TOKEN_SECRETKEY'];

    $c->format = 'json';

    $req = new WirelessShareTpwdCreateRequest;

    $tpwd_param = new IsvTpwdInfo;

    $tpwd_param->ext="{\"xx\":\"xx\"}";

    $tpwd_param->logo=$data['logo'];

    $tpwd_param->text=$data['text'];

    $tpwd_param->url=$data['url'];

    $tpwd_param->user_id="24234234234";

    $req->setTpwdParam(json_encode($tpwd_param));

    $resp = $c->execute($req);

//    print_r($resp);

    return $resp->model;

}



function get_item_info($iid){

    Vendor('TaobaoApi.TopSdk');

    date_default_timezone_set('Asia/Shanghai');

    $c = new TopClient;

    $c->appkey = C('TOKEN_APPKEY');

    $c->secretKey = C('TOKEN_SECRETKEY');

    $c->format = 'json';



    $req = new TbkItemInfoGetRequest;

    $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,nick,seller_id,volume");

    $req->setPlatform("1");

    $req->setNumIids($iid);

    $resp = $c->execute($req);

    return $resp->results->n_tbk_item[0];

}



function get_item_clickid($url){

    Vendor('TaobaoApi.TopSdk');

    date_default_timezone_set('Asia/Shanghai');

    $c = new TopClient;

    $c->appkey = C('TOKEN_APPKEY');

    $c->secretKey = C('TOKEN_SECRETKEY');

    $c->format = 'json';

    $req = new TbkItemClickExtractRequest;

    $req->setClickUrl($url);

    $resp = $c->execute($req);



    return $resp->item_id;

}



/**

 * 获取网页内容

 * @param $url

 * @param $cache

 * @return mixed|string

 */

function http_get_content($url, $cache = false){

    // 定义当前页面请求的cache key

    $key = md5($url);

    // 如果使用cache时只读一次

    if($cache){

        $file_contents = $_SESSION[$key];

        if(!empty($file_contents)) return $file_contents;

    }



    // 通过curl模拟请求页面

    $ch = curl_init();

    // 设置超时时间

    $timeout = 30;

    curl_setopt($ch, CURLOPT_URL, $url);

    // 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));

    curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");

    //curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $file_contents = curl_exec($ch);



    curl_close($ch);



    // 匹配出当前页的charset

    $charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";

    //$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";



    // 非utf8编码时转码

    if($charset != 'utf-8'){

        $file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);

    }

    // 将结果记录到session中，方便下次直接读取

    $_SESSION[$key] = $file_contents;



    return $file_contents;

}



function get_coupon_info($url){



	$out = http_get_content($url);

	preg_match_all('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]|[0-9][1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))/', $out, $effectdate);

	if ($effectdate) {

		$item['coupon_start_time'] = strtotime($effectdate[0][0]);

		$item['coupon_end_time'] = strtotime($effectdate[0][1]);

	}





	if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))

		$item['quan_surplus'] = $match[1];

	if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))

		$item['quan_receive'] = $match[1];



	if(preg_match('/<dd>(.*)<\/dd>/',$out,$match))

		$item['quan_condition'] = $match[1];



	//$item['quanurl'] = $quanurl;

	//$item['Quan_surplus'] = $quan_surplus;

	//$item['Quan_receive'] = $quan_receive;

	if(preg_match('/<dt>\d*/', $out, $match)){

		$quanprice = explode("<dt>", $match[0]);

		$item['quan'] = ($quanprice[1]);

	}

	return $item;



}



function get_coupon_info_v1($url){

	$out = http_get_content($url);

	$json = json_decode($out,true);

	if($json['result']['retStatus'] == '0'){

		$item['quan'] = $json['result']['amount'];

		$item['coupon_end_time'] = strtotime($json['result']['effectiveEndTime']);

		$item['coupon_start_time'] = strtotime($json['result']['effectiveStartTime']);

		return $item;

	}

	else return false;

}
//check weather the cun items
function is_cun($iid){

    $url = "https://cunlist.taobao.com/?q=" . $iid;

    $content = http_get_content($url);

    if(preg_match('/<div class=\"b1\">.*<\/div>/',$content,$match)){

        return true;

    }

    else return false;

}