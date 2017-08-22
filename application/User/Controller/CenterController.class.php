<?php

/**
 * 会员中心
 */
namespace User\Controller;
use Common\Controller\MemberbaseController;
class CenterController extends MemberbaseController {
	
	function _initialize(){
		parent::_initialize();
	}
    //会员中心
	public function index() {
		$this->assign("user",$this->user);
		//dump($this->user);
		redirect('?g=portal&m=cuntao&a=personal',0,'页面跳转中...');
    }	
    //非法字符判断	
    public function check_char($char){		
    	//需过滤的字符的正则	    
    	$stripChar = '?<*.>\'"';	
    	if(preg_match('/['.$stripChar.']/is', $char)==1){	        
    		$this->error('输入中包含'.$stripChar.'等非法字符！',U(''));	   
    	}	
    	return true;
    }	
    //判断pid格式
    public function pid($char){
    	$str = '/^mm_+[0-9]+_+[0-9]+_+[0-9]+$/';
    	preg_match($str,$char,$ma);
    	if($ma){
    		return true;
    	}
    	$this->error('pid格式不正确!');
    	return false;
    }
    //判断是否添加过pid	
    public function check_pid(){		
    	//获取用户信息		
    	$id = sp_get_current_userid();	
    	//查询		
    	$where['id']=$id;		
    	$sublist_model=M("UsersMes");		
    	$result = $sublist_model->where($where)->count();	
    	echo $result;	
    	if($result >= 1){
    		return true;
    	}	
    	return false;
    }	
    //添加pid	
    public function add_pid(){	
    	//清除两端空格	
    	$pid = trim($_POST['pid']);
    	$name= $_POST['name'];		
    	//判断非法字符		
    	if(!$this->check_char($pid)){
    		//pid非法字符
    		return false;
    	}
    	if(!$this->pid($pid)){
    		//pig格式不正确
    		return false;
    	}	
    	if(!$this->check_char($name)){
    		//name非法字符
    		return false;
    	}
    	//获取当前用户id		
    	$id = sp_get_current_userid();		
    	$where = array(
    		'id' => $id,
    		'pid'=> $pid,
    		'pid_name'=>$name
    	);	
    	$sublist_model=M("UsersMes");	

        $rs = $sublist_model->where("id={$id}")->find();
        if($rs){
            $this->error("添加失败，已有pid");
        }

    	$result = $sublist_model->add($where);
    	if($result){
    		$this->success("添加成功！",U("portal/cuntao/personal_pid"),1);
    	}else{
    		$this->error("添加失败！",U("portal/cuntao/personal_pid"),2);
    	}
    }	
    //查找用户的pid
    public function search_pid(){
    	$id=sp_get_current_userid();
    	$users_model = M('UsersMes');
    	$where['id']=$id;
    	$rs = $users_model->where($where)->select();
    	var_dump($rs);
    }
    //修改pid
    public function alert_pid(){
    	$pid = $_POST['pid'];
    	$name = $_POST['name'];
    	//判断是否为null
    	if(is_null($pid)){
    		$this->send_ajax_mes("请填写pid",2);
    		return;
    	}
        $str = '/^mm_+[0-9]+_+[0-9]+_+[0-9]+$/';
        preg_match($str,$pid,$ma);
        if(!$ma){
            $this->send_ajax_mes('pid格式不正确!',2);
            return;  
        }
        $id=sp_get_current_userid();
        $users_model = M('UsersMes');
        $simi = $users_model->where("id={$id} and pid='{$pid}'")->find();
        if($simi){
            $this->send_ajax_mes("pid与原pid相同",2);
            return;
        }
        
    	if(is_null($name)){
    		$this->send_ajax_mes("请填写分组名",2);
    		return;
    	}
    	
    	$where = array(
            "id"=>$id,
            "pid"=>$pid,
            "pid_name"=>$name,
        );
    	
    	$rs = $users_model->save($where);
    	if ($rs){
            $mes = json_encode($where);
    		//修改成功
    		$this->send_ajax_mes($where,1);
    	}else{
    		//修改失败
    		$this->send_ajax_mes("修改失败",2);
    	}
    }
    //发送ajax
    public function send_ajax_mes($mes,$status){
        $data = array(
            "status"=>$status,
            "mes"=>$mes,
        );
        $mes = json_encode($data);
        echo $mes;
    }
    
    //删除pid栏目
    public function delete_pid(){
    	$pid = $_GET['pid'];
    	$id  = sp_get_current_userid();
    	$where="id='{$id}' and pid='{$pid}'";
    	$user_model = M('UsersMes');
    	$rs = $user_model->where($where)->delete();
    	if ($rs){
    		//删除成功
    		$this->success("删除成功",U("portal/cuntao/personal_pid"));
    	}else{
    		//删除失败
    		$this->error("删除失败",U("portal/cuntao/personal_pid"));
    	}
    }
    //添加用户信息
    public function add_mes(){
    	//获取数据
    	$earn = $_POST['earn'];
    	$team = $_POST['team'];
    	$qq	  = $_POST['qq'];
    	$work = $_POST['work'];
    	//过滤字符集
    	$stripChar = '?<*.>\'"';
    	if(preg_match('/['.$stripChar.']/is', $qq)==1){
    		$this->error('输入信息中包含'.$stripChar.'等非法字符！');
    	}
    	//判断是否为null
    	if(!is_null($earn)){
    		$where['earn']	=	$earn;
    	}
  		if(!is_null($team)){
    		$where['team_type']	=	$team;
    	}
    	if(!is_null($qq)){
    		$where['qq']	=	$qq;
    	}
    	if(!is_null($work)){
    		$where['work_type']	=	$work;
    	}
    	//判断￥where是否为空
    	if(empty($where)){
    		$this->error("添加失败！没有传输数据",U("portal/cuntao/personal_data"),2);
    	}
    	//获取用户信息
    	$id = sp_get_current_userid();
    	$where['id'] = $id;
    	$users_model  = M('Users');
    	$rs = $users_model->save($where);
    	if($rs){
    		//修改成功
    		$this->success("修改成功",U("portal/cuntao/personal"));
    	}else {
    		//修改失败
    		$this->error("修改失败",U("portal/cuntao/personal_data"));
    	}
    }
    //修改密码
    public function alert_pwd(){
    	//获取修改密码信息
    	$o_pwd = $_POST['opwd'];
    	$n_pwd = $_POST['npwd'];
        $user_model = M("Users");
    	
    	
    	//判断非法字符
    	if(!$this->check_char($o_pwd)){
    		//旧密码非法字符
    		return false;
    	}
    	if(!$this->check_char($n_pwd)){
    		//新密码非法字符
    		return false;
    	}
    	
    	//判断原密码是否正确
    	$user = sp_get_current_userid();
        $data = array(
            "id"=>$user,
            "user_pass"=>sp_password($o_pwd),
        );
        $rq = $user_model->field("id")->where($data)->find();
    	if(!$rq){
    		//旧密码错误，跳回
    		$this->error("旧密码错误",U("portal/cuntao/personal_password"));
    		//return false;
    	}
    	//写入新密码
    	$where = array(
    		'id' => $user,
    		'user_pass' => sp_password($n_pwd)
    	);
    	$rs = $user_model->save($where);
    	//echo $rs->getLastSql();
    	if($rs){
    		//修改成功
    		$this->success("修改成功",U("portal/cuntao/personal"),2);
    	}else {
    		//修改失败
    		$this->error("修改失败",U("portal/cuntao/personal_password"),2);
    	}
    	
    }	
    //test	
    public function testa(){
    	$a = array();		
    	$this->assign("a",$a);
    	$this->display('demo');
    }	
    public function test(){		
    	$this->error('错误',__ROOT__."/",10);
    				
    }
}
