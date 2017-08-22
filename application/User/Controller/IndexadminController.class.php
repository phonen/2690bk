<?php

/**
 * 会员
 */
namespace User\Controller;
use Common\Controller\AdminbaseController;
class IndexadminController extends AdminbaseController {
    function index(){
    	$users_model=M("Users");
    	$count=$users_model->where(array("user_type"=>2))->count();
    	$page = $this->page($count, 20);
    	$lists = $users_model
    	->where(array("user_type"=>2))
    	->order("create_time DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display(":index");
    }
    
    function ban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_status','0');
    		if ($rst) {
    			$this->success("会员拉黑成功！", U("indexadmin/index"));
    		} else {
    			$this->error('会员拉黑失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }   
    //审核用户成为淘客    
    public function taoke(){        	
    	//获取目标id    	
    	$id=intval(I("get.id"));    	
    	//允许用户进入后台    	
    	if ($id) {        		
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_type','1');    		
    		//给用户默认淘客身份    		
    		$role_model = M("RoleUser");    		
    		//清除原先的用户身份    		
    		$role_model->where(array("user_id"=>$id))->delete();    		
    		//插入淘客新身份    		
    		$rs = $role_model->add(array('user_id'=>$id,'role_id'=>15));    		
    		if($rs){    			
    			$this->success("淘客启用成功！");    		
    		}else {    			
    			$this->error('淘客启用失败！');    		
    		}    		         	
    	} else {        		
    		$this->error('数据传入失败！');        	
    	}    
    }
	//审核用户成为小二    
    public function xiaoer(){        	
    	//获取目标id    	
    	$id=intval(I("get.id"));    	
    	//不允许用户进入后台    	
    	if ($id) {        		
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_type','1');    		
    		//给用户默认小二身份    		
    		$role_model = M("RoleUser");    		
    		//清除原先的用户身份    		
    		$role_model->where(array("user_id"=>$id))->delete();    		
    		//插入淘客新身份    		
    		$rs = $role_model->add(array('user_id'=>$id,'role_id'=>16));    		
    		if($rs){    			
    			$this->success("小二启用成功！");    		
    		}else {    			
    			$this->error('小二启用失败！');    		
    		}    		         	
    	} else {        		
    		$this->error('数据传入失败！');        	
    	}    
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = M("Users")->where(array("id"=>$id,"user_type"=>2))->setField('user_status','1');
    		if ($rst) {
    			$this->success("会员启用成功！", U("indexadmin/index"));
    		} else {
    			$this->error('会员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
	//模糊查询用户
	function search(){
    	$users_model=M("Users");
		
		$key = I("get.kw","");
		$data = "user_login like '%{$key}%' and user_type=2";
		
    	$count=$users_model->where($data)->count();
    	$page = $this->page($count, 20);
    	$lists = $users_model
    	->where($data)
    	->order("create_time DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display(":index");
    }
}
