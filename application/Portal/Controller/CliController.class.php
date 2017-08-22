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
class CliController extends HomebaseController {

    //首页
    public function index() {

    }

    public function get_dataoke(){
        set_time_limit(0);
        $dataoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');

        $iids = array();

        $dataokes = $dataoke_model->field('iid')->select();
        $curtime = date("Y-m-d H:i:s");
        foreach($dataokes as $dataoke){

            $iids[] = $dataoke['iid'];
        }
        for($p=1;$p<=110;$p++){
            $str = "";
            $u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=bnsdd1etil&v=2&page=$p";
            echo $u;
            $str = file_get_contents($u);
//echo $str;
            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if($json['result']){
                foreach($json['result'] as $item){

                    $data = array();
//$iids[$item['GoodsID']]++;
                    $data['iid'] = $item['GoodsID'];
                    $data['title'] = $item['Title'];
                    $data['d_title'] = $item['D_title'];
                    $data['img'] = $item['Pic'];
                    $data['cid'] = $item['Cid'];
                    $data['org_price'] = $item['Org_Price'];
                    $data['price'] = $item['Price'];
                    $data['istmall'] = $item['IsTmall'];
                    $data['sales_num'] = $item['Sales_num'];
                    $data['dsr'] = $item['Dsr'];
                    $data['sellerid'] = $item['SellerID'];
                    $data['commission'] = $item['Commission'];
                    $data['commission_jihua'] = $item['Commission_jihua'];
                    $data['commission_queqiao'] = $item['Commission_queqiao'];
                    $data['jihua_link'] = $item['Jihua_link'];
                    $data['que_siteid'] = $item['Que_siteid'];
                    $data['jihua_shenhe'] = $item['Jihua_shenhe'];
                    $data['introduce'] = $item['Introduce'];
                    $data['quan_id'] = $item['Quan_id'];
                    $data['quan_price'] = $item['Quan_price'];
                    $data['quan_time'] = $item['Quan_time'];
                    $data['quan_surplus'] = $item['Quan_surplus'];
                    $data['quan_receive'] = $item['Quan_receive'];
                    $data['quan_condition'] = $item['Quan_condition'];
                    $data['quan_m_link'] = $item['Quan_m_link'];
                    $data['quan_link'] = $item['Quan_link'];
                    $data['itime'] = $curtime;
                    //if($dataoke_model->where(array("iid"=>$item['GoodsID']))->find())
                    if(in_array($item['GoodsID'],$iids))
                        $dataoke_result =$dataoke_model->where(array("iid"=>$item['GoodsID']))->save($data);
                    //continue;
                    else {
                        $dataoke_result =$dataoke_model->add($data);

                    }
                }
            }
            //sleep(3);
        }
//print_r($iids);


    }

    public function  get_haodanku(){
        set_time_limit(0);
        echo "get_haodanku start";
        $dataoke_model = M('TbkItem3','cmf_','DB_DATAOKE');
        $dataokes = $dataoke_model->field('iid')->select();
        $curtime = date("Y-m-d H:i:s");
        foreach($dataokes as $dataoke){
            $iids[] = $dataoke['iid'];
        }

        $p = $_GET['page'];
        for($p=1;$p<100;$p++){
            $str = "";
            $u = "http://www.haodanku.com/index/index/nav/3/starttime/7/p/" . $p .".html?json=true";
            echo $u;
            $str = file_get_contents($u);
            if($str == '[]')break;
            $json = json_decode($str,true);
            if($json){
                foreach($json as $item){

                    $data = array();

                    $data['iid'] = $item['itemid'];
                    $data['item'] = $item['itemtitle'];

                    $data['img'] = $item['itempic'];

                    $data['aftprice'] = $item['itemprice']-$item['couponmoney'];
                    //$data['quan_price'] = $item['couponmoney'];
                    $data['qtime'] = date("Y-m-d",$item['couponendtime']);
                    $data['quan_surplus'] = $item['couponsurplus'];
                    $data['quan_receive'] = $item['couponreceive'];
                    $data['quan'] = $item['couponexplain'];

                    $data['quan_link'] = $item['couponurl'];
                    $data['itime'] = $curtime;
                    if(in_array($item['itemid'],$iids))
                        $dataoke_result =$dataoke_model->where(array("iid"=>$item['itemid']))->save($data);
                    //continue;
                    else {
                        $dataoke_result =$dataoke_model->add($data);

                    }
                }
            }

        }

    }

    public function clean_quan(){
        set_time_limit(0);
        $taoke_model = M('TbkItem','cmf_','DB_DATAOKE');
        $taoke_model->where("qtime<now() - interval 1 day")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['quan_link'];
            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("iid"=>$iid))->delete();

            else
                $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"qtime"=>$qtime));
        }




        ///////////////////////////////////
        $taoke_model = M('TbkItem3','cmf_','DB_DATAOKE');
        $taoke_model->where("qtime<now() - interval 1 day")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['quan_link'];
            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("iid"=>$iid))->delete();

            else
                $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"qtime"=>$qtime));
        }

        $taoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
        $taoke_model->where("quan_time<now() - interval 1 day")->delete();
        $items = $taoke_model->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['Quan_link'];
            $iid = $item['iid'];
            //$header[] = "Accept-Language: zh-CN,zh;q=0.8";
            $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.154 Safari/537.36 LBBROWSER";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $quan_link);
            //curl_setopt($ch, CURLOPT_REFERER, $tu);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            //curl_setopt($ch, CURLOPT_NOBODY,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_MAXREDIRS,2);
            $out = curl_exec($ch);
            curl_close($ch);


            if(preg_match('/<span class=\"rest\">(\d+)<\/span>/',$out,$match))
                $quan_surpluse = $match[1];

            if(preg_match('/<span class=\"count\">(\d+)<\/span>/',$out,$match))
                $quan_receive = $match[1];
            if(preg_match('/(\d+-\d+-\d+)<\/dd>/',$out,$match))
                $qtime = $match[1];

            if($quan_surpluse == '')
                $taoke_model->where(array("iid"=>$iid))->delete();

            else
                $taoke_model->where(array("iid"=>$iid))->save(array("quan_receive"=>$quan_receive,"quan_surpluse"=>$quan_surpluse,"quan_time"=>$qtime));
        }
    }


    public function clean_dataoke(){
        set_time_limit(0);
        $taoke_model = M('TbkqqDataokeItem','cmf_','DB_DATAOKE');
        $taoke_model->where("quan_time<now() - interval 1 day")->delete();
        $items = $taoke_model->limit(5000)->select();

        foreach($items as $item){
            $header = array();
            $match = array();
            $quan_surpluse = "";
            $quan_receive = "";
            $qtime = "";
            $quan_link = $item['Quan_link'];
            $iid = $item['iid'];
            $coupon_info = get_coupon_info($quan_link);
            if($coupon_info){
            	if(empty($coupon_info['coupon_start_time'])){
            		echo $quan_link . "\n";
            		$taoke_model->where(array("iid"=>$iid))->delete();
            	}
            	
            	
            }
            else{
            	echo $quan_link . "\n";
            	$taoke_model->where(array("iid"=>$iid))->delete();
            }
                

            
        }

    }

    public function detail_autoload_post(){
        set_time_limit(0);
        $startdate = date("Y-m-d",time()-2*86400);
        $enddate =date("Y-m-d");
        $where = "ctime>='" . $startdate . "'";
        $filename_arr = array();
        $filename = "";
        $u="http://pub.alimama.com/report/getTbkPaymentDetails.json?queryType=1&payStatus=&DownloadID=DOWNLOAD_REPORT_INCOME_NEW&startTime=$startdate&endTime=$enddate";

        $options_model = M("Options");
        $option=$options_model->where("option_name='cookie_options'")->find();
        if($option){

            $options = (array)json_decode($option['option_value'],true);

            foreach($options as $data) {
                $media = M("TbkqqTaokeMedia")->where(array("username" => $data['username']))->find();
                if ($media) {

                    $cookie = $data['cookie'];
                    $str = openhttp_header($u, '', $cookie);

                    if($str == "") exit();
                    $curtime = time();
                    $filename = './Uploads/details_' . $curtime . ".xls";
                    $f = fopen($filename, 'w');
                    echo $data['username'] . "\n";
                    if ($f) {
                        fwrite($f, $str);
                        $filename_arr[] = $filename;
                        fclose($f);
                        sleep(3);
                    }

                }
            }

            M("TbkqqTaokeDetails")->where($where)->delete();
            foreach($filename_arr as $filename){
                $this->details_import($filename, "xls",'taoke');
            }

            $filename_arr = array();
            $filename = "";
            foreach($options as $data) {
                $fanli_media = M("TbkqqFanliMedia")->where(array("username"=>$data['username']))->find();
                if($fanli_media){
                    $cookie = $data['cookie'];
                    $str = openhttp_header($u, '', $cookie);
                    if($str == "") exit();
                    $curtime = time();
                    $filename =  './Uploads/details_' . $curtime . ".xls";
                    $f=fopen($filename,'w');
                    echo $data['username'] . "\n";
                    if($f){
                        fwrite($f,$str);
                        $filename_arr[] = $filename;
                        fclose($f);
                        sleep(3);
                    }
                }
            }

            M("TbkqqFanliDetails")->where($where)->delete();
            foreach($filename_arr as $filename){
                $this->details_import($filename, "xls",'fanli');
            }
        }

    }

    protected function details_import($filename,$exts,$type){
        if($type == "taoke")	$model = M("TbkqqTaokeDetails");
        if($type == "fanli") $model = M("TbkqqFanliDetails");
        import("Org.Util.PHPExcel");
        $PHPExcel=new \PHPExcel();
        if($exts == 'xls'){
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }
//$ctime = M("TbkqqTaokeDetail")->max(ctime);
//		$maxctime = strtotime($ctime);
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        $allColumn++;
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=2;$currentRow<=$allRow;$currentRow++) {
            $data = array();
            for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$arr中
                $cell = $currentSheet->getCell($address)->getValue();
                /*
                                if ($cell instanceof PHPExcel_RichText) {
                                    $cell = $cell->__toString();
                                }
                */
                if(is_object($cell))  $cell= $cell->__toString();
                switch ($currentColumn)
                {
                    case 'A':
                        $data['ctime']=$cell;
                        break;
                    case 'C':
                        $data['goods']=$cell;
                        break;
                    case 'D':
                        $data['gid']=$cell;
                        break;
                    case 'E':
                        $data['wangwang']=$cell;
                        break;
                    case 'F':
                        $data['shop']=$cell;
                        break;
                    case 'G':
                        $data['gcount']=$cell;
                        break;
                    case 'H':
                        $data['gamount']=$cell;
                        break;
                    case 'I':
                        $data['ostatus']=$cell;
                        break;
                    case 'J':
                        $data['otype']=$cell;
                        break;
                    case 'K':
                        $data['srrate']=$cell;
                        break;
                    case 'L':
                        $data['fcrate']=$cell;
                        break;
                    case 'M':
                        $data['fukuan']=$cell;
                        break;
                    case 'N':
                        $data['effect']=$cell;
                        break;
                    case 'O':
                        $data['jiesuan']=$cell;
                        break;
                    case 'P':
                        $data['pre_amount']=$cell;
                        break;
                    case 'Q':
                        $data['jstime']=$cell;
                        break;
                    case 'R':
                        $data['yjrate']=$cell;
                        break;
                    case 'S':
                        $data['yongjin']=$cell;
                        break;
                    case 'T':
                        $data['btrate']=$cell;
                        break;
                    case 'U':
                        $data['butie']=$cell;
                        break;
                    case 'V':
                        $data['bttype']=$cell;
                        break;
                    case 'W':
                        $data['third']=$cell;
                        break;
                    case 'X':
                        $data['pingtai']=$cell;
                        break;
                    case 'Y':
                        $data['orderid']=$cell;
                        break;
                    case 'Z':
                        $data['class']=$cell;
                        break;
                    case 'AA':
                        $data['sourceid']=$cell;
                        break;
                    case 'AD':
                        $data['adname']=$cell;
                        break;
                }
            }
//			print_r($data);
            //$detail = M("TbkqqTaokeDetail")->where(array("orderid"=>$data['orderid'],"gid"=>$data['gid'],"gcount"=>$data['gcount']))->find();
            //if($detail) M("TbkqqTaokeDetail")->where(array("orderid"=>$data['orderid'],"gid"=>$data['gid'],"gcount"=>$data['gcount']))->save($data);

            $model->add($data);

        }

    }

    public function item_campaign_post(){
        set_time_limit(0);
        if (IS_POST) {
            if(isset($_POST['ids'])) {
                $ids = join(",", $_POST['ids']);
            }
            $username=I("post.username");

            $options_model = M("Options");
            $option=$options_model->where("option_name='cookie_options'")->find();
            if($option){
                $options = (array)json_decode($option['option_value'],true);
                foreach($options as $data) {
                    if($data['username'] == $username) $cookie = $data['cookie'];
                }
            }
            $ret = "";
            if($cookie != ""){
                $items = M("TbkqqTaokeItem")->where("id in ($ids)")->select();
                if($items) {
                    foreach ($items as $item) {
                        $t = time();
                        $iid = $item['iid'];
                        $u = "http://pub.alimama.com/items/search.json?q=https%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D" .$iid . "&auctionTag=&perPageSize=40&shopTag=";
                        $str = openhttp_header($u,'',$cookie);
                        $arr = json_decode($str,true);
                        $sellerId = $arr['data']['pageList'][0]['sellerId'];
                        $tkRate = $arr['data']['pageList'][0]['tkRate'];
                        $eventRate = $arr['data']['pageList'][0]['eventRate'];
                        $type = '1';
                        $u = "http://pub.alimama.com/pubauc/getCommonCampaignByItemId.json?itemId=" . $iid;
                        $str = openhttp_header($u, '', $cookie);
                        $arr = json_decode($str, true);
                        if ($arr['ok'] == '1' && $arr['data']) {
                            $rate = $tkRate;
                            if($eventRate != ''){
                                if($rate<$eventRate){
                                    $rate = $eventRate;
                                    $type = '0';
                                }
                            }
                            $cid = '';
                            $keeperid = '';
                            $post = array();

                            foreach ($arr['data'] as $data) {
                                if($data['manualAudit'] == '1') continue;
                                if ($data['commissionRate'] > $rate) {
                                    $rate = $data['commissionRate'];
                                    $cid = $data['CampaignID'];
                                    $keeperid = $data['ShopKeeperID'];
                                }
                            }
                            if($cid != ""){
                                $post['campId'] = $cid;
                                $post['keeperid'] = $keeperid;
                                $post['applyreason'] = "淘特惠淘客推广申请";
                                $cookie_data = excookie($cookie);
                                $post['_tb_token_'] = $cookie_data['_tb_token_'];
                                $post['t'] = $t;
                                $type = '1';
                                $post_str = "campId=" . $post['campId'] . "&keeperid=" . $post['keeperid'] . "&applyreason=" . $post['applyreason'] . "&_tb_token_=" . $post['_tb_token_'] . "&t=" . $post['t'];
                                //print_r($post);
                                $u = "http://pub.alimama.com/pubauc/applyForCommonCampaign.json";
                                $reffer = "http://pub.alimama.com/promo/search/index.htm?queryType=2&q=" . $item['itemurl'];
                                sleep(1);
                                $ret = openhttp_header($u, $post_str, $cookie, $reffer, '1');
                                sleep(1);

                            }
                            if($type == '0')M("TbkqqTaokeItem")->where(array("iid"=>$iid))->save(array("type"=>$type));
                        }
                    }
                }
            }

            $this->success($ret);
        }
    }

    public function check_cun(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');

        $items = $taoke_model->field("id,num_iid")->where("isnull(cun)")->select();

        foreach($items as $item){
            $content = "";
            $url = "https://cunlist.taobao.com/?q=" . $item['num_iid'];
            $content = http_get_content($url);
            if(preg_match('/<div class=\"b1\">.*<\/div>/',$content,$match))
                $data['cun'] = '1';
            else $data['cun'] = '0';
            $data['id'] = $item['id'];
            $taoke_model->save($data);
            echo $match['0'];
        }

    }

    public function check_cun_v1(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');
        $dataoke_model = M('TbkDataokeItem','cmf_','DB_DATAOKE');
        $cun_model = M("CunItems");
        $cun_iids = $cun_model->field('num_iid')->where(array("cun"=>"1"))->select();
        foreach($cun_iids as $cun_iid){

            $iids[] = $cun_iid['num_iid'];
        }
        $count = $taoke_model->where("isnull(cun)")->count();
        echo $count;
        //$page = $this->page($count, 100);
        $p = $count/100;
        for($i=0;$i<=$p+1;$i++){
            echo $i;
            $limit = "'" . $i*100 . ",100'";
            echo $limit;
            $items = $taoke_model->where("isnull(cun)")
                ->limit($limit)
                ->select();
            //$items = $taoke_model->where("isnull(cun)")->select();

            foreach($items as $item){

                if(in_array($item['num_iid'],$iids)) {
                }
                else{
                    if(is_cun($item['num_iid']))
                    {
                        unset($cun_item);

                        $data['cun'] = '1';
                        $cun_item = $item;
                        $cun_item['click_url'] = str_replace('mm_110341117_13180074_52464478','mm_120456532_20542124_69830211',$item['click_url']);
                        $quan_data = get_url_data($item['quanurl']);
                        if($quan_data['activity_id'] == "")$quan_id = $quan_data['activityId'];
                        else $quan_id = $quan_data['activity_id'];
                        $cun_item['quanurl'] = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] ."&activity_id=" . $quan_id;
                        $cun_item['source'] = 'dataoke';
                        $cun_item['dtitle'] = $item['title'];

                        $da_item = $dataoke_model->where(array("iid"=>$item['num_iid']))->find();
                        if($da_item){
                            $cun_item['dtitle'] = $da_item['d_title'];
                            //$cun_item['']
                        }
                        unset($cun_item['id']);
                        $cun_model->add($cun_item);
                    }

                    else $data['cun'] = '0';
                    $data['id'] = $item['id'];
                    $taoke_model->save($data);
                }

            }
        }

    }

    public function check_cun_v3(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');
        $dataoke_model = M('TbkDataokeItem','cmf_','DB_DATAOKE');
        $cun_model = M("CunItems");
        $cun_iids = $cun_model->field('num_iid')->where(array("cun"=>"1"))->select();
        foreach($cun_iids as $cun_iid){

            $iids[] = $cun_iid['num_iid'];
        }
        //$count = $taoke_model->where("isnull(cun)")->count();
        //echo $count;
        //$page = $this->page($count, 100);
        //$p = $count/100;
        //for($i=0;$i<=$p+1;$i++){
        //    echo $i;
        //    $limit = "'" . $i*100 . ",100'";
        //    echo $limit;
        $items = $taoke_model->where("isnull(cun)")
            //        ->limit($limit)
            ->select();
        //$items = $taoke_model->where("isnull(cun)")->select();

        foreach($items as $item){
            unset($coupon_info);


            if(in_array($item['num_iid'],$iids)) {
                continue;
            }
            else{
                if(is_cun($item['num_iid']))
                {
                    unset($cun_item);

                    $data['cun'] = '1';
                    $cun_item = $item;
                    $cun_item['click_url'] = str_replace('mm_110341117_13180074_52464478','mm_120456532_20542124_69830211',$item['click_url']);
                    $quan_data = get_url_data($item['quanurl']);
                    if($quan_data['activity_id'] == "")$quan_id = $quan_data['activityId'];
                    else $quan_id = $quan_data['activity_id'];

                    $cun_item['quanurl'] = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $item['sellerId'] ."&activity_id=" . $quan_id;

                    $cun_item['source'] = 'dataoke';
                    $cun_item['dtitle'] = $item['title'];
                    $coupon_info = get_coupon_info($cun_item['quanurl']);
                    if(empty($coupon_info['coupon_start_time'])) {

                        continue;
                    }
                    $da_item = $dataoke_model->where(array("iid"=>$item['num_iid']))->find();
                    if($da_item){
                        $cun_item['dtitle'] = $da_item['d_title'];
                        //$cun_item['']
                    }
                    unset($cun_item['id']);
                    $cun_model->add($cun_item);
                }

                else $data['cun'] = '0';
                $data['id'] = $item['id'];
                $taoke_model->save($data);
            }

        }


    }



    public function check_cun_v2(){
        set_time_limit(0);
        $cun_model = M("CunItems");


        for($p=1;$p<=110;$p++){
            $str = "";
            $u = "http://api.dataoke.com/index.php?r=Port/index&type=total&appkey=bnsdd1etil&v=2&page=$p";
            echo $u;
            $str = file_get_contents($u);
//echo $str;
            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if($json['result']){
                foreach($json['result'] as $item){

                    $data = array();
//$iids[$item['GoodsID']]++;
                    $data['num_iid'] = $item['GoodsID'];
                    $data['title'] = $item['Title'];
                    $data['dtitle'] = $item['D_title'];
                    $data['pic_url'] = $item['Pic'];
                    $data['cate_id'] = $item['Cid'];
                    $data['price'] = $item['Org_Price'];
                    $data['coupon_price'] = $item['Price'];
                    if($item['IsTmall'] == '1')$data['shop_type'] = 'B';
                    else $data['shop_type'] = 'C';
                    $data['volume'] = $item['Sales_num'];
                    //$data['dsr'] = $item['Dsr'];
                    $data['sellerId'] = $item['SellerID'];
                    $data['commission'] = 0;
                    if($item['Commission']<=$item['Commission_jihua'])$data['commission'] = $item['Commission_jihua'];
                    if($item['Commission_queqiao']>$data['commission'])$data['commission'] = $item['Commission_queqiao'];


                    //$data['jihua_link'] = $item['Jihua_link'];
                    //$data['que_siteid'] = $item['Que_siteid'];
                    //$data['jihua_shenhe'] = $item['Jihua_shenhe'];
                    $data['intro'] = $item['Introduce'];
                    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$item['Quan_id'] ."&pid=mm_110341117_13180074_52464478&itemId=535668601295&src=cd_cdll" ;
                    $data['quan'] = $item['Quan_price'];
                    //$data['quan_time'] = $item['Quan_time'];
                    $data['quan_surplus'] = $item['Quan_surplus'];
                    $data['quan_receive'] = $item['Quan_receive'];
                    $data['quan_condition'] = $item['Quan_condition'];
                    //$data['quan_m_link'] = $item['Quan_m_link'];
                    $data['quanurl'] = $item['Quan_link'];
                    $data['add_time'] = time();
                    //if($dataoke_model->where(array("iid"=>$item['GoodsID']))->find())
                    $da_item = $cun_model->where(array("num_iid"=>$data['num_iid']))->find();
                    if($da_item){
                        //$cun_item['']
                    }
                    else {
                        $url = "https://cunlist.taobao.com/?q=" . $data['num_iid'];
                        $content = http_get_content($url);
                        if(preg_match('/<div class=\"b1\">.*<\/div>/',$content,$match)){
                            $data['cun'] = '1';
                            $data['source'] = 'dataoke';

                            $cun_model->add($data);
                        }

                    }
                }
            }
            //sleep(3);
        }

    }

    //桃迎客取券
    public function get_taoyingke(){
        set_time_limit(0);
        $dataoke_model = M('CunItems');
        $ccc = 0;
        for($p=130;$p<=999;$p++){

            echo $p;
            $str = "";
            $u = "http://120.76.76.124/api.php/api/goods/queryGoodsInfoInner";
            $post['current_page'] = $p;
            $post['token'] = "1";
            $str = http_post_content($u,$post);

            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if(count($json['data'])>0){

                foreach($json['data'] as $item){

                    unset($coupon_info);
                    if($item['goodsStatus'] == '2'){
                        $data = array();
//$iids[$item['GoodsID']]++;
                        $data['orig_id'] = $item['goodsId'];
//    $data['num_iid'] = $item['correspondId'];
                        $iid_data = get_url_data($item['purchaseLink']);
                        $data['num_iid'] = $iid_data['id'];
                        if($data['num_iid'] == "")continue;
                        $data['title'] = $item['goodsName'];



                        $picdata = get_url_data($item['productImg']);

                        $data['pic_url'] = str_replace('_430x430q90.jpg','',$picdata['realPicUrl']);




                        $data['price'] = $item['presentPrice'];
                        if($data['price'] == "")continue;

                        $data['coupon_start_time'] = strtotime($item['activityStartTime']);
                        $data['volume'] = $item['monthlySales'];

                        $data['coupon_end_time'] = strtotime($item['activityEndTime']);



                        $data['commission_rate'] = $item['commissionRate'];


                        $data['commission'] = $item['commission'];

                        $data['intro'] = $item['introduction'];


                        $data['quan'] = $item['couponAmount'];
                        $data['coupon_price'] = $data['price'] - $data['quan'];
                        $data['quan_surplus'] = $item['couponSurplus'];
                        $data['quan_receive'] = $item['couponReceive'];

                        $data['quanurl'] = $item['couponLink'];

                        $urldata = get_url_data($item['couponLink']);
                        if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
                        else $quan_id = $urldata['activity_id'];
                        if($urldata['seller_id'] == "")$data['sellerId'] = $urldata['sellerId'];
                        else $data['sellerId'] = $urldata['seller_id'];

                        $coupon_info = get_coupon_info($item['couponLink']);

                        if(empty($coupon_info['coupon_start_time'])) {

                            continue;
                        }
                        $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;
                        if($item['goodsType'] == '1' && $item['LogisticsCost'] == '0'){
        $data['cun'] = '1';

    }
    $data['source'] = 'taoyingke';
    $data['add_time'] = time();

    $dataoke_item = $dataoke_model->where(array("num_iid"=>$data['num_iid']))->find();
    if($dataoke_item){
        if($dataoke_item['source'] == '2690')continue;
        else{
            if($data['cun'] == '1')
            $dataoke_result =$dataoke_model->where(array("num_iid"=>$item['correspondId']))->save($data);
        }

    }

    //continue;
    else{
    	echo $data['quanurl'] . "\n";
    	$dataoke_result =$dataoke_model->add($data);
    }
        

}
                }

            }
            else break;
        }
//print_r($iids);

echo $ccc;
    }

    public function get_cun_haodanku(){

        set_time_limit(0);
        echo "get_haodanku start";
        $dataoke_model = M('CunItems');
        //$dataokes = $dataoke_model->field('num_iid')->select();
        //$curtime = date("Y-m-d H:i:s");
        //foreach($dataokes as $dataoke){
         //   $iids[] = $dataoke['num_iid'];
        //}
        $cunhao = M("CunHaoType")->select();


        for($p=1;$p<10;$p++){
            $str = "";
            $u = "http://www.haodanku.com/Index/index/nav/1/starttime/7/cuntao/" . $p .".html?json=true";
            echo $p;
            $str = file_get_contents($u);
            if($str == '[]'|| $str == '')break;
            $json = json_decode($str,true);
            if($json){
                foreach($json as $item){
                    unset($coupon_info);
                    $data = array();

                    foreach($cunhao as $cunval){
                        if($cunval['hao_tid'] == $item['fqcat']){
                            $data['cate_id'] = $cunval['num_tiid'];
                            break;
                        }
                    }
                    $data['num_iid'] = $item['itemid'];
                    if(is_cun($item['itemid'])){
						$data["is_cun"]=1;
					}else{
						$data["is_cun"]=2;
					}
                    if($item['activity_type']=="聚划算"){
                        $data['preferential_type']="jvhuasuan";
                    }else if($item['activity_type']=="淘抢购"){
                        $data['preferential_type']="taoqianggou";
                    }else{
                        $data['preferential_type']="no_type";
                    }
                    $data['title'] = $item['itemtitle'];
                    $data['dtitle'] = $item['itemshorttitle'];
                    $data['intro'] = $item['itemdesc'];
                    $data['pic_url'] = $item['itempic'];
                    $data['cate_id'] = 15;

                   // $data['sellerId'] = $item['seller_id'];

                    $data['price'] = $item['itemprice'];
                    $data['quan'] = $item['couponmoney'];
                    $data['coupon_price'] = $item['itemprice'] - $item['couponmoney'];

                    if($item['ctrates'] != '' && $item['ctrates'] != '0.00' &&$item['ctrates']>=20){
                        $data['cun'] = '1';
                        $data['commission_rate'] = $item['ctrates'];
                    }
                    else $data['commission_rate'] = $item['tkrates'];

                    //$data['quan_price'] = $item['couponmoney'];
                    
                    
                    //$data['coupon_start_time'] = $item['couponstarttime'];
                    //$data['coupon_end_time'] = $item['couponendtime'];

                    //$data['quan_surplus'] = $item['couponsurplus'];
                    //$data['quan_receive'] = $item['couponreceive'];
                    //$data['quan_condition'] = $item['couponexplain'];

                    $data['quanurl'] = $item['couponurl'];
                    $coupon_info = get_coupon_info($item['couponurl']);

                    if(empty($coupon_info['coupon_start_time'])) {
                    	
                    	continue;
                    	}

                    $urldata = get_url_data($item['couponurl']);
                    if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
                    else $quan_id = $urldata['activity_id'];
                    if($urldata['seller_id'] != "")$data['sellerId'] = $urldata['seller_id'];
                    else $data['sellerId'] = $urldata['sellerId'];

                    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;

                    $data['add_time'] = time();
                    $data['source'] = 'haodanku';
                    $data1 = array_merge($data,$coupon_info);
                    $dataoke_item = $dataoke_model->where(array("num_iid"=>$item['itemid']))->find();
                    if($dataoke_item){
                        if($dataoke_item['source'] != '2690')$dataoke_result =$dataoke_model->where(array("num_iid"=>$item['itemid']))->save($data1);
                        else continue;
                    }

                    //continue;
                    else {
                    	echo $data1['quanurl'] . "\n";
                        $dataoke_result =$dataoke_model->add($data1);

                    }
                }
            }

        }
    }

    //get_cun_haodanku测试
    public function get_cun_haodanku_test(){
    
    	
    	
    	set_time_limit(0);
    	echo "get_haodanku start";
    	$dataoke_model = M('CunItems');
        $cunhao = M("CunHaoType")->select();
    
    	
    	//删除好单库
    	//$rq = $dataoke_model->where("source = 'haodanku'")->delete();
    	//写删库日志
    	$file = fopen("handanku.txt","a");
    	fwrite($file,$dataoke_model->getLastSql());
    	fclose($file);
         $cunhao = M("CunHaoType")->select();
        foreach($cunhao as $cunval){
        	for($p=1;;$p++){
        		$str = "";
        		//$u = "http://www.haodanku.com/Index/index/nav/1/starttime/7/cuntao/" . $p .".html?json=true";
                $u = "http://www.haodanku.com/index/index/nav/6/cid/{$cunval['hao_tid']}/starttime/30/p/{$p}.html?json=true&api=list ";
                //echo $u;
        		echo $p;
        		$str = file_get_contents($u);
        		if($str == '[]'|| $str == '')break;
        		$json = json_decode($str,true);
        		if($json){
        			foreach($json as $item){
        				unset($coupon_info);
                        $dataoke_item = $dataoke_model->where(array("num_iid"=>$item['itemid']))->find();
                        if($dataoke_item){
                            echo 1;
                            continue;
                        }
        				$data = array();
                        $data['cate_id'] = $cunval['num_tiid'];
        
        				$data['num_iid'] = $item['itemid'];
        				if(is_cun($item['itemid'])){
    						$data["is_cun"]=1;
    					}else{
    						$data["is_cun"]=2;
    					}
                        if($item['activity_type']=="聚划算"){
                            $data['preferential_type']="jvhuasuan";
                        }else if($item['activity_type']=="淘抢购"){
                            $data['preferential_type']="taoqianggou";
                        }else{
                            $data['preferential_type']="no_type";
                        }
        				$data['title'] = $item['itemtitle'];
        				$data['dtitle'] = $item['itemshorttitle'];
        				$data['intro'] = $item['itemdesc'];
        				$data['pic_url'] = $item['itempic'];
        				//$data['cate_id'] = 15;
        
        				// $data['sellerId'] = $item['seller_id'];
        
        				$data['price'] = $item['itemprice'];
        				$data['quan'] = $item['couponmoney'];
        				$data['coupon_price'] = $item['itemprice'] - $item['couponmoney'];
        
        				if($item['ctrates'] != '' && $item['ctrates'] != '0.00' &&$item['ctrates']>=20){
        					$data['cun'] = NULL;
        					$data['commission_rate'] = $item['ctrates'];
        				}
        				else $data['commission_rate'] = $item['tkrates'];
        
        				//修改的地方
        				$data['quanurl'] = $item['couponurl'];
        				$data["coupon_start_time"] = $item["couponstarttime"];
        				$data["coupon_end_time"] = $item["couponendtime"];
        
        				$urldata = get_url_data($item['couponurl']);
        				if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
        				else $quan_id = $urldata['activity_id'];
        				if($urldata['seller_id'] != "")$data['sellerId'] = $urldata['seller_id'];
        				else $data['sellerId'] = $urldata['sellerId'];
        
        				$data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;
        
        				$data['add_time'] = time();
        				$data['source'] = 'haodanku';
        				
        				//修改的地方
        				//$data1 = array_merge($data,$coupon_info);
        				$data1 = $data;
        				

        
        				//continue;
    					echo $data1['quanurl'] . "\n";
                        if(is_null($data1['quanurl'])){
                            echo 2;
                            continue;
                        }
                        $dataoke_result =$dataoke_model->add($data1);
    					
        			}
        		}else{
                    echo 3;
                }
        
        	}
        }
        $this->run_unuse_quan(2);
        $dataoke_model->where("vol_day=2")->save(array("vol_day"=>NULL));
    }
    
    public function get_taoyingke_test(){
        set_time_limit(0);
        $dataoke_model = M('CunItemsCopy4');
$ccc = 0;
        for($p=130;$p<=150;$p++){
        	
            echo $p;
            $str = "";
            $u = "http://120.76.76.124/api.php/api/goods/queryGoodsInfoInner";
            $post['current_page'] = $p;
            $post['token'] = "1";
            $str = http_post_content($u,$post);

            $json = array();
            $json = json_decode($str,true);
            //print_r($json);
            if(count($json['data'])>0){

                foreach($json['data'] as $item){
                	
                	unset($coupon_info);
if($item['goodsStatus'] == '2'){
    $data = array();
//$iids[$item['GoodsID']]++;
    $data['orig_id'] = $item['goodsId'];
//    $data['num_iid'] = $item['correspondId'];
    $iid_data = get_url_data($item['purchaseLink']);
$data['num_iid'] = $iid_data['id'];
    if($data['num_iid'] == "")continue;
    $data['title'] = $item['goodsName'];



    $picdata = get_url_data($item['productImg']);

    $data['pic_url'] = str_replace('_430x430q90.jpg','',$picdata['realPicUrl']);




    $data['price'] = $item['presentPrice'];
    if($data['price'] == "")continue;

    $data['coupon_start_time'] = strtotime($item['activityStartTime']);
    $data['volume'] = $item['monthlySales'];

    $data['coupon_end_time'] = strtotime($item['activityEndTime']);



    $data['commission_rate'] = $item['commissionRate'];


    $data['commission'] = $item['commission'];

    $data['intro'] = $item['introduction'];


    $data['quan'] = $item['couponAmount'];
    $data['coupon_price'] = $data['price'] - $data['quan'];
    $data['quan_surplus'] = $item['couponSurplus'];
    $data['quan_receive'] = $item['couponReceive'];

    $data['quanurl'] = $item['couponLink'];

    $urldata = get_url_data($item['couponLink']);
    if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
    else $quan_id = $urldata['activity_id'];
    if($urldata['seller_id'] == "")$data['sellerId'] = $urldata['sellerId'];
    else $data['sellerId'] = $urldata['seller_id'];

$coupon_info = get_coupon_info($item['couponLink']);

if(empty($coupon_info['coupon_start_time'])) {
	
	continue;
	}
    $data['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=mm_120456532_20542124_69830211&itemId=" . $data['num_iid'] . "&src=cd_cdll" ;
    if($item['goodsType'] == '1' && $item['LogisticsCost'] == '-1'){
                            $data['cun'] = '1';

                        }
    $data['source'] = 'taoyingke';
    $data['add_time'] = time();

    $dataoke_item = $dataoke_model->where(array("num_iid"=>$data['num_iid']))->find();
    if($dataoke_item){
        if($dataoke_item['source'] == '2690')continue;
        else{
            if($data['cun'] == '1')
                $dataoke_result =$dataoke_model->where(array("num_iid"=>$item['correspondId']))->save($data);
        }

    }

    //continue;
    else{
        echo $data['quanurl'] . "\n";
        $dataoke_result =$dataoke_model->add($data);
    }
        

}
                }

            }
            //sleep(3);
        }
//print_r($iids);

        echo $ccc;
    }


    public function test_cun_quan(){
        $taoke_model = M('CunItems');
        $items = $taoke_model

            ->select();

        foreach($items as $item){
            echo $item['num_iid'] . "\n";
        }
        /*
                        $data = get_url_data(str_replace("&amp;","&",$item['quanurl']));
                        if($data['activity_id'] == "")$quan_id = $data['activityId'];
                        else $quan_id = $data['activity_id'];
                        if($data['seller_id'] != "")$sellerId = $data['seller_id'];
                        else $sellerId = $data['sellerId'];
                        $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $sellerId ."&activity_id=" . $quan_id;
                        echo $quan_link;
                        $iid = $item['num_iid'];
        
                        $coupon_info = get_coupon_info($quan_link);
        print_r($coupon_info);
        
                        if($coupon_info){
                            if(empty($coupon_info['coupon_start_time'])){
                                $taoke_model->where(array("num_iid"=>$iid))->delete();
                                echo $iid . "--" .$quan_link . "\n";
                                print_r($coupon_info);
                            }
        
                            else {
                                $coupon_info['quan_receive'] = $coupon_info['Quan_receive'];
                                $coupon_info['quan_surplus'] = $coupon_info['Quan_surplus'];
                                $coupon_info['quan_condition'] = $coupon_info['Quan_condition'];
                                unset($coupon_info['Quan_receive']);
                                unset($coupon_info['Quan_surplus']);
                                unset($coupon_info['Quan_condition']);
        
                                $taoke_model->where(array("num_iid"=>$iid))->save($coupon_info);
                            }
        
        
                        }
        else  {
            $taoke_model->where(array("num_iid"=>$iid))->delete();
            echo $quan_link . "\n";
            print_r($coupon_info);
        }
        
        
                    }
                    */
    }
//跑无用优惠卷
    public function update_cun_quan(){
        set_time_limit(0);
        $taoke_model = M('CunItems');
        $count = $taoke_model->count();
        echo $count;
        //$page = $this->page($count, 100);
        //$p = $count/100;

        //for($i=0;$i<=$p+1;$i++) {
        //    echo $i;
        //    $limit = "'" . $i * 100 . ",100'";
        //    echo $limit;
        $items = $taoke_model
            //    ->order('id')
            //        ->limit($limit)
            ->select();

        foreach($items as $item){
            unset($coupon_info);
            echo $item['num_iid'] . "\n";

            $data = get_url_data(str_replace("&amp;","&",$item['quanurl']));
            if($data['activity_id'] == "")$quan_id = $data['activityId'];
            else $quan_id = $data['activity_id'];
            if($data['seller_id'] != "")$sellerId = $data['seller_id'];
            else $sellerId = $data['sellerId'];
            $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $sellerId ."&activity_id=" . $quan_id;

            $iid = $item['num_iid'];

            $coupon_info = get_coupon_info($quan_link);

            if($coupon_info){
                if(empty($coupon_info['coupon_start_time'])){
                    $taoke_model->where(array("num_iid"=>$iid))->delete();
                    echo $iid . "--" .$quan_link . "\n";
                    //    print_r($coupon_info);
                }

                else {
                    $urldata = get_url_data($quan_link);
                    if($urldata['activity_id'] == "")$quan_id = $urldata['activityId'];
                    else $quan_id = $urldata['activity_id'];
                    if($item['source'] == '2690')$pid = "mm_120456532_21582792_72310921";
                    else $pid="mm_120456532_20542124_69830211";

                    $coupon_info['click_url'] = "https://uland.taobao.com/coupon/edetail?activityId=" .$quan_id ."&pid=" . $pid . "&itemId=" . $item['num_iid'] . "&src=cd_cdll" ;
                    echo $iid . "--" .$coupon_info['coupon_start_time'] . "\n";

                    $taoke_model->where(array("num_iid"=>$iid))->save($coupon_info);
                }


            }
            else  {
                $taoke_model->where(array("num_iid"=>$iid))->delete();
                echo $quan_link . "\n";
//    print_r($coupon_info);
            }

        }
        //  }


        //$taoke_model->where("from_unixtime(coupon_end_time)<now()")->delete();
        //$items = $taoke_model->select();



    }


public function get_item_volume(){
	$taoke_model = M('CunItems');
        $count = $taoke_model->count();
        echo $count;
        
        $items = $taoke_model
            ->select();

        foreach($items as $item){
        	$info = get_item_info($item['num_iid']);
        	if($info){
					
					$data['vol_day'] = $info->volume - $item['volume'];
					$data['volume'] = $info->volume;
					$taoke_model->where(array("num_iid"=>$item['num_iid']))->save($data);
        	}
        }
}

    public function update_dataoke_quan(){
        set_time_limit(0);
        $taoke_model = M('Items','cmf_','DB_DATAOKE');
        $count = $taoke_model->count();
        echo $count;
        //$page = $this->page($count, 100);
        $p = $count/100;

        for($i=0;$i<=$p+1;$i++) {
            echo $i;
            $limit = "'" . $i * 100 . ",100'";
            echo $limit;
            $items = $taoke_model
                ->limit($limit)
                ->select();

            foreach($items as $item){

                $data = get_url_data(str_replace("&amp;","&",$item['quanurl']));
                if($data['activity_id'] == "")$quan_id = $data['activityId'];
                else $quan_id = $data['activity_id'];

                $sellerId = $item['sellerid'];
                $quan_link = "http://shop.m.taobao.com/shop/coupon.htm?seller_id=" . $sellerId ."&activity_id=" . $quan_id;

                $iid = $item['num_iid'];

                $coupon_info = get_coupon_info($quan_link);

                if($coupon_info){
                    if(empty($coupon_info['coupon_start_time'])){
                        $taoke_model->where(array("num_iid"=>$iid))->delete();
                        echo $iid . "--" .$quan_link . "\n";
                        print_r($coupon_info);
                    }

                    else {
                        //$coupon_info['quan_receive'] = $coupon_info['Quan_receive'];
                        //$coupon_info['quan_surplus'] = $coupon_info['Quan_surplus'];
                        //$coupon_info['quan_condition'] = $coupon_info['Quan_condition'];
                        //unset($coupon_info['Quan_receive']);
                        //unset($coupon_info['Quan_surplus']);
                        //unset($coupon_info['Quan_condition']);
                        echo $item['num_iid'] . "\n";
                        //$taoke_model->where(array("num_iid"=>$iid))->save($coupon_info);
                    }


                }
                else  {
                    //$taoke_model->where(array("num_iid"=>$iid))->delete();
                    echo $quan_link . "\n";
                    print_r($coupon_info);
                }

            }

            sleep(1);
        }


        //$taoke_model->where("from_unixtime(coupon_end_time)<now()")->delete();
        //$items = $taoke_model->select();



    }
    //实惠猪导入
    public function get_cun_shz(){
    	//建立基本信息
    	$param = array(
				"APPID: 1704201628538164",
				"APPKEY: 2dd79739501eabd2c06b788d47f183bf",
		);
    	//网站地址
    	$endpoint = 'http://gateway.shihuizhu.net/open';
    	$model = 'CunItems';
    	//M($model)->where("source='shihuizhu'")->delete();
    	$this->get_items($param,$endpoint,$model);
    }
//获取商品
	public function get_items($param,$endpoint,$model){
		//获取2690与优惠猪类型对应列表
		$lei = M("CunZhuType")->order("zhu_tid")->select();
		foreach ($lei as $val){
			$this->gettl($val['zhu_tid'],$val['num_tiid'],$param,$endpoint,$model);
		}
		return $lei;

	}
	//获取每页商品
	public function gettl($lei,$num_tiid,$param,$endpoint,$model){
		$url = $endpoint.'/goods/'.$lei.'/1';
		echo $url."<url";

		$itemarr = $this->send_url($url,$param);
		
		$page = $itemarr['pagecount'];
		$this->add_item($itemarr,$num_tiid,$model);
		dump($page);
		for($i=2;$i<=$page;$i++){
			$url = $endpoint.'/goods/'.$lei.'/'.$i;
			$itemsarr = $this->send_url($url,$param);
			$this->add_item($itemsarr,$num_tiid,$model);
			echo 2;
		}
	}
	//商品插入数据库
	public function add_item($arr,$num_tiid,$model){
		foreach ($arr['result'] as $val){
			//查重
			$same = M($model)->where("num_iid={$val['gid']}")->find();
			if(empty($same)){
				$item['num_iid']=$val['gid'];
				if(is_cun($val['gid'])){
					$item["is_cun"]=1;
				}else{
					$item["is_cun"]=2;
				}
                if($val['activity']=="聚划算"){
                    $item['preferential_type']="jvhuasuan";
                }else if($val['activity']=="淘抢购"){
                    $item['preferential_type']="taoqianggou";
                }else{
                    $item['preferential_type']="no_type";
                }
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

                if(strstr($val['thumb'],"http",true)===false){
                    $item['pic_url']="http:".$val['thumb'];
                }else{
                    $item['pic_url']=$val['thumb'];
                }
				

				//获取sellerId
				//$info = get_item_info($val['gid']);
				$data = get_url_data($item['quanurl']);
				if($data['seller_id']!="")
				$item['sellerId'] = $data['seller_id'];
				else $item['sellerId'] = $data['sellerId'];

				//添加条目
				$result = M($model)->add($item);
			}
		}
	}
	//curl发送数据方法
	public function send_url($url,$param){
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
		curl_setopt($ch, CURLOPT_HTTPHEADER, $param); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		//返回php数据
		//dump($result);
		return json_decode($result,true);
	}
	
public function test_quan(){
		set_time_limit(0);
		
		$rs = M("CunItemsCopy1")->where("cun>=1")->select();
		
		foreach ($rs as $val){
			$active = $val['click_url'];
			$data = get_url_data($active);
			
			$url = "http://uland.taobao.com/cp/coupon?activityId={$data['activityId']}&itemId={$val['num_iid']}";
			$coupon = get_coupon_info_v1($url);
			if(!$coupon){
				echo $url . "\n";
				sleep(1);
				$coupon = get_coupon_info_v1($url);
				if(!$coupon){
					echo "2222222222222222222222\n";
					M("CunItems")->where(array("num_iid"=>$val['num_iid']))->save(array("vol_day"=>'1'));
				}
				
			}
						
			sleep(1);
		}
		
		
	} 

/**
*@author changeheart
*检查优惠券是否可用
*
*/
public function check_quan_url(){
        set_time_limit(0);
        $model = M("CunItemsCopy22");
        $rs = $model->where("cun>=1")->select();
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
            //$resule = $this->send_curl($url);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            
            curl_close($ch);
            $resule=json_decode($result,true);
            if($resule['result']['retStatus']!=0){
                $model->where("num_iid={$val['num_iid']}")->save(array("ordid"=>1));
                echo $model->getLastSql();
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
}

    /**
    *@author changheart
    *删除无用券
    **/
    public function check_unuse_quan(){
        set_time_limit(0);
        $item = M("CunItems");
        $operate = M("UserOperate");
        $status = 0;
        $num = 0;
        $rs = $item->where("cun>=1")->select();
        foreach ($rs as $val){
            if(!is_numeric($val['num_iid'])){
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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            
            curl_close($ch);
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
                }
            // }else{
            //     $f = fopen("datalog.txt","a");
            //     $datetime = date("Y-m-d H:i:sa",time());
            //     fwrite($f,"302重定向，过期券，{$datetime}\n");
            //     fclose($f);
            //     dump($result);
            //     echo "no json\n";
            //     return;
            // }
            sleep(0.5);
           
        }
        //$docu = serialize($feewrong);
        
        //$this->assign('fee',$feewrong);
        //$this->assign('ret',$retstatus);
        echo $num;
        $this->display(":Test/index");
    }
    public function test(){
        set_time_limit(0);
        $item = M("CunItems");
        $operate = M("UserOperate");
        $where = "vol_day is NULL";
        $count = $item->where($where)->count();
        $page = 1000;
        $pages = 10;
        $timecount=1;
        $pagenum = ceil($count/$page);
        $pagenow = 0;
        for(;$pagenow<$pagenum;$pagenow++){
            $pageli = $pagenow*$page;
             $rs = $item->field("num_iid,click_url")->where($where)->limit("{$pageli},{$page}")->order("id DESC")->select();

                foreach ($rs as $val){
                    if(!is_numeric($val['num_iid'])){
                        echo "1\n";
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
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $result = curl_exec($ch);
                    
                    curl_close($ch);
                    
                   // echo "haha";

                    $resule = json_decode($result,true);
                    dump(is_null($resule));
                    if(is_null($resule)){
                        echo "{$pageli}\n";
                        continue;
                    }
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
                        echo "^^{$val['num_iid']}^^\n";
                    }else{
                        $item->where("num_iid={$val['num_iid']}")->save(array("vol_day"=>2));
                        echo "<<{$val['num_iid']}-2>>\n";
                    }
                    if($timecount == $pages){
                        $timecount = 0;
                        sleep(1);
                    }
                    $timecount++;
                    
                }
        }
    }
    //写文本
    public function write_items(){
        $f = fopen("dataoke.txt","w");
        $rs = M("CunItems")->field("num_iid,title")->where("source='dataoke'")->select();
        foreach($rs as $val){
            $str = "{$val['title']}  https://detail.tmall.com/item.htm?id={$val['num_iid']}".PHP_EOL;
            fwrite($f,$str);
        }
        fclose($f);

    }
    //跑失效优惠券方法
    public function run_unuse_quan($order=1){
        set_time_limit(0);
        $item = M("CunItems");
        $operate = M("UserOperate");
        $where = "vol_day is NULL";
        $count = $item->where($where)->count();
        $pages = 10;
        $timecount=1;

        $page = 1000;
        $pagenum = ceil($count/$page);
        $pagenow = 0;
        for(;$pagenow<$pagenum;$pagenow++){
            $pageli = $pagenow*$page;
            if($order === 1){
                $rs = $item->field("num_iid,click_url")->where($where)->limit("{$pageli},{$page}")->select();
            }else{
                $rs = $item->field("num_iid,click_url")->where($where)->limit("{$pageli},{$page}")->order("id DESC")->select();
            }
            foreach ($rs as $val){
                if(!is_numeric($val['num_iid'])){
                    echo "1\n";
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
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                
                curl_close($ch);
                

                $resule = json_decode($result,true);
                dump(is_null($resule));
                if(is_null($resule)){
                    echo "{$pageli}\n";
                    continue;
                }
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
                    echo "^^{$val['num_iid']}^^\n";
                }else{
                    $item->where("num_iid={$val['num_iid']}")->save(array("vol_day"=>2));
                    echo "<<{$val['num_iid']}-2>>\n";
                }
                if($timecount == $pages){
                    $timecount = 0;
                    sleep(1);
                }
                $timecount++;
                
            }
        }
    }
    //获取大淘客
    public function get_cun_dataoke(){

        set_time_limit(0);
        echo "get_dataoke start";
        $dataoke_model = M("CunItems");
        //$dataoke_model->where("source='dataoke'")->delete();

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
                        echo "1";
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
                        echo 2;
                        continue;
                    }
                    
                }
            }else{
                echo 3;
            }

        }
    }
    /**
    *author changeheart
    *导出大淘客商品
    **/
    public function get_dataoke_quan(){
        $model = M("CunItems");
        $item = $model->where("source = 'dataoke'")->limit(500)->select();
        if($item){
            $f = fopen("data1.txt", "a+");
            foreach($item as $val){
                fwrite($f, "https://item.taobao.com/item.htm?id={$val['num_iid']}\r\n");
            }
            fclose($f);
            

        }
    }
}


