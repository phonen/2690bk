<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class UserController extends AdminbaseController{
	protected $users_model,$role_model;
	
	function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->role_model = D("Common/Role");
	}
	function index(){
		$count=$this->users_model->where(array("user_type"=>1))->count();
		$page = $this->page($count, 20);
		$users = $this->users_model
		->alias("a")
		->join(C('DB_PREFIX')."user_pid_status b ON a.id = b.user_id","left")
		->where(array("user_type"=>1))
		->order("create_time DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
		
		$roles_src=$this->role_model->select();
		$roles=array();
		foreach ($roles_src as $r){
			$roleid=$r['id'];
			$roles["$roleid"]=$r;
		}
		$this->assign("page", $page->show('Admin'));
		$this->assign("roles",$roles);
		$this->assign("users",$users);
		$this->display();
	}
	
	
	function add(){
		$roles=$this->role_model->where("status=1")->order("id desc")->select();
		$this->assign("roles",$roles);
		$this->display();
	}
	
	function add_post(){
		if(IS_POST){
			if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
				$role_ids=$_POST['role_id'];
				unset($_POST['role_id']);
				if ($this->users_model->create()) {
					$result=$this->users_model->add();
					if ($result!==false) {
						$role_user_model=M("RoleUser");
						foreach ($role_ids as $role_id){
							$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$result));
						}
						$this->success("添加成功！", U("user/index"));
					} else {
						$this->error("添加失败！");
					}
				} else {
					$this->error($this->users_model->getError());
				}
			}else{
				$this->error("请为此用户指定角色！");
			}
			
		}
	}
	
	
	function edit(){
		$id= intval(I("get.id"));
		$roles=$this->role_model->where("status=1")->order("id desc")->select();
		$this->assign("roles",$roles);
		$role_user_model=M("RoleUser");
		$role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
		$this->assign("role_ids",$role_ids);
			
		$user=$this->users_model->where(array("id"=>$id))->find();
		$this->assign($user);
		$this->display();
	}
	
	function edit_post(){
		if (IS_POST) {
			if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
				if(empty($_POST['user_pass'])){
					unset($_POST['user_pass']);
				}
				$role_ids=$_POST['role_id'];
				unset($_POST['role_id']);
				if ($this->users_model->create()) {
					$result=$this->users_model->save();
					if ($result!==false) {
						$uid=intval($_POST['id']);
						$role_user_model=M("RoleUser");
						$role_user_model->where(array("user_id"=>$uid))->delete();
						foreach ($role_ids as $role_id){
							$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$uid));
						}
						$this->success("保存成功！");
					} else {
						$this->error("保存失败！");
					}
				} else {
					$this->error($this->users_model->getError());
				}
			}else{
				$this->error("请为此用户指定角色！");
			}
			
		}
	}
	
	/**
	 *  删除
	 */
	function delete(){
		$id = intval(I("get.id"));
		if($id==1){
			$this->error("最高管理员不能删除！");
		}
		
		if ($this->users_model->where("id=$id")->delete()!==false) {
			M("RoleUser")->where(array("user_id"=>$id))->delete();
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
	
	function userinfo(){
		$id=get_current_admin_id();
		$user=$this->users_model->where(array("id"=>$id))->find();
		$this->assign($user);
		$this->display();
	}
	
	function userinfo_post(){
		if (IS_POST) {
			$_POST['id']=get_current_admin_id();
			$create_result=$this->users_model
			->field("user_login,user_email,last_login_ip,last_login_time,create_time,user_activation_key,user_status,role_id,score,user_type",true)//排除相关字段
			->create();
			if ($create_result) {
				if ($this->users_model->save()!==false) {
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->users_model->getError());
			}
		}
	}
	
	    function ban(){
        $id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','0');
    		if ($rst) {
    			$this->success("管理员停用成功！", U("user/index"));
    		} else {
    			$this->error('管理员停用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
    function cancelban(){
    	$id=intval($_GET['id']);
    	if ($id) {
    		$rst = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','1');
    		if ($rst) {
    			$this->success("管理员启用成功！", U("user/index"));
    		} else {
    			$this->error('管理员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
	
	//pid权限开放	
	public function openpid(){		
		$uid = I("get.uid");		
		$sta = M("UserPidStatus")->where("user_id={$uid}")->find();		
		$data = array(				
			"user_id"=>$uid,				
			"status"=>1			
		);		
		if(empty($sta)){			
			M("UserPidStatus")->add($data);	
		}else{			
			M("UserPidStatus")->where("user_id={$uid}")->save($data);
		}		
		//记录操作		
		$rs = $this->operate_record(291,$uid);
		if($rs){		
			$this->success("pid权限给与成功");	
		}else{			
			$this->error("pid权限给予失败");
		}	
	}	
	//pid权限关闭
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
		//记录操作	
		$rs = $this->operate_record(292,$uid);	
		if($rs){		
			$this->success("pid权限关闭成功");	
		}else{			
			$this->error("pid权限关闭失败");
		}	
	}
	//记录操作历史	
	public function operate_record($op,$ud){	
		$uip = get_client_ip();	
		$uid = sp_get_current_userid();	
		$ab = array(			
			"uid"=>$uid,		
			"operate"=>$op,			
			"time"=>array("exp"=>"NOW()"),	
			"ip"=>$uip,
			"explain"=>$ud
		);		
		//记录操作	
		return M("UserOperate")->add($ab);	
	}
	//用户搜索
	function search(){
		$key = I("get.kw","");
		$data = "user_type=1 and user_login like '%{$key}%'";
		$count=$this->users_model->where($data)->count();
		$page = $this->page($count, 20);
		$users = $this->users_model
		->alias("a")
		->join(C('DB_PREFIX')."user_pid_status b ON a.id = b.user_id","left")
		->where($data)
		->order("create_time DESC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();
		
		$roles_src=$this->role_model->select();
		$roles=array();
		foreach ($roles_src as $r){
			$roleid=$r['id'];
			$roles["$roleid"]=$r;
		}
		$this->assign("page", $page->show('Admin'));
		$this->assign("roles",$roles);
		$this->assign("users",$users);
		$this->display("index");
	}
	/**
	 * @changeheart
	 *显示用户主页
	 */
	public function user_home(){
		$this->get_user_mes();
		$this->display();
	}
	/**
	 * @changeheart
	 *获取用户信息
	 */
	public function get_user_mes(){
		$id = sp_get_current_admin_id();
		$rs = M("Users")->where("id={$id}")->field("id,user_login,last_login_time,last_login_ip,mobile,qq,create_time")->find();
		if($rs){
			$model = M("CunItems");
			$count = $model->where("uid={$id}")->count();
			$count1 = $model->where("uid={$id} and status='0'")->count();
			$this->assign("user",$rs);
			$this->assign("fabu",$count);
			$this->assign("shenhe",$count1);
		}
	}
	
}