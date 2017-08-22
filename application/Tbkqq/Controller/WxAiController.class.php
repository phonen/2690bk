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
class WxAiController extends HomebaseController {
		public function index(){
			echo "abc";
		}
    public function search_items_by_key(){
        if(IS_POST) {
            $key = $_POST['kw'];

                $url = "http://www.2690.cn";
                $dataoke_model = M('CunItems');
                $where = "title like '%" . $key . "%'";
                $item_count = $dataoke_model->where($where)->count();
                if($item_count>0){
                    echo "共为你找到" . $item_count . "个跟" . $key . "相关的产品，详情请点击：" . $url . '/?a=search&kw=' . urlencode(trim($key));
                }
                else echo "没找到跟" . $key . "相关的产品，请重新换一个关键词！";

        }
    }
}
