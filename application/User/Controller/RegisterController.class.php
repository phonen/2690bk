<?php
/**
 * 会员注册
 */
namespace User\Controller;
use Common\Controller\HomebaseController;
class RegisterController extends HomebaseController {
	
	function index(){
	    if(sp_is_user_login()){ //已经登录时直接跳到首页
	        redirect(__ROOT__."/");
	    }else{
	        $this->display(":register");
	    }
	}
	
	function doregisteradm(){	
		$this->_do_admin_register();
	}
	
	function doregister(){
    	
    	if(isset($_POST['email'])){
    	
    	    
    	    //邮箱注册
    	   $this->_do_email_register();
    	    
    	}elseif(isset($_POST['mobile'])){
    	    
    	    //手机号注册
    	    $this->_do_mobile_register();
    	    
    	}else{
    	    $this->error("注册方式不存在！");
    	}
    	
	}
		//注册逻辑
	private function _do_admin_register(){	
		
		if(!sp_check_verify_code()){
            $this->error("验证码错误！");
        }       
        
        $rules = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('email', 'require', '邮箱不能为空！', 1 ),
            array('password','require','密码不能为空！',1),
            //array('password','number','密码长度最小5位,最大20位！',1,'5,20'),
            array('repassword', 'require', '重复密码不能为空！', 1 ),
            array('repassword','password','确认密码不正确',0,'confirm'),
            array('email','email','邮箱格式不正确！',1), // 验证email字段格式是否正确
            	
        );
	    
	     
	    $users_model=M("Users");
	     
	    if($users_model->validate($rules)->create()===false){
	        $this->error($users_model->getError());
	    }
	     
	    $password=$_POST['password'];
	    $email=$_POST['email'];
	    $user_login = $_POST['user_login'];
	    $mobile = $_POST['mobile'];
	    //$qq = $_POST['qq'];
	    //$weixin = $_POST['weixin'];
	    
	    $username=str_replace(array(".","@"), "_",$email);
	    //用户名需过滤的字符的正则
	    $stripChar = '?<*.>\'"';
	    if(preg_match('/['.$stripChar.']/is', $username)==1){
	        $this->error('用户名中包含'.$stripChar.'等非法字符！');
	    }
	     
// 	    $banned_usernames=explode(",", sp_get_cmf_settings("banned_usernames"));
	     
// 	    if(in_array($username, $banned_usernames)){
// 	        $this->error("此用户名禁止使用！");
// 	    }
	    
	    $where['user_login']=$user_login;
	    $where['user_email']=$email;
	    $where['_logic'] = 'OR';
	    
	    $ucenter_syn=C("UCENTER_ENABLED");
	    $uc_checkemail=1;
	    $uc_checkusername=1;
	    if($ucenter_syn){
	        include UC_CLIENT_ROOT."client.php";
	        $uc_checkemail=uc_user_checkemail($email);
	        $uc_checkusername=uc_user_checkname($username);
	    }
	     
	    $users_model=M("Users");
	    $result = $users_model->where($where)->count();
	    if($result || $uc_checkemail<0 || $uc_checkusername<0){
	        $this->error("用户名或者该邮箱已经存在！");
	    }else{
	        $uc_register=true;
	        if($ucenter_syn){
	             
	            $uc_uid=uc_user_register($username,$password,$email);
	            //exit($uc_uid);
	            if($uc_uid<0){
	                $uc_register=false;
	            }
	        }
	        if($uc_register){
	            $need_email_active=C("SP_MEMBER_EMAIL_ACTIVE");
	            $data=array(
	                'user_login' => $user_login,
	                'user_email' => $email,
	                'user_nicename' =>$username,
	                'mobile' => $mobile,
	                //'qq' => $qq,
	                //'weixin' => $weixin,
	                'user_pass' => sp_password($password),
	                'last_login_ip' => get_client_ip(0,true),
	                'create_time' => date("Y-m-d H:i:s"),
	                'last_login_time' => date("Y-m-d H:i:s"),
	                'user_status' => $need_email_active?2:1,
	                "user_type"=>2,//会员
	            );
	            $rst = $users_model->add($data);
	            if($rst){
	                //注册成功页面跳转
	                $data['id']=$rst;
	                $_SESSION['user']=$data;	                	                //暂时不发送激活邮件，直接跳转主页	                $this->success("注册成功，激活后才能使用！",U("cuntao/weixin"));
	                	
	                //发送激活邮件
	                if($need_email_active){
	                    $this->_send_to_active();
	                    unset($_SESSION['user']);
	                    $this->success("注册成功，激活后才能使用！",U("user/login/index"));
	                }else {
	                    $this->success("注册成功，进入个人放单中心！",U("portal/cuntao/personal_put"));
	                }
	                	
	            }else{
	                $this->error("注册失败！",U("cuntao/login_reg"));
	            }
	             
	        }else{
	            $this->error("注册失败！",U("cuntao/login_reg"));
	        }
	         
	    }
	}
	

	private function _do_mobile_register(){
	     
	    if(!sp_check_mobile_verify_code()){
	            $this->error("手机验证码错误！");
        }
        $rules = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('mobile', 'require', '手机号不能为空！', 1 ),
            array('password','require','密码不能为空！',1),
	    array('password','number','密码长度最小5位,最大20位！',1,'5,20'),
        );
        	
	    $users_model=M("Users");
	     
	    if($users_model->validate($rules)->create()===false){
	        $this->error($users_model->getError());
	    }
	     
	    $password=$_POST['password'];
	    $mobile=$_POST['mobile'];
	     
	    
	    $where['mobile']=$mobile;
	     
	    $users_model=M("Users");
	    $result = $users_model->where($where)->count();
	    if($result){
	        $this->error("手机号已被注册！");
	    }else{

	        $data=array(
	            'user_login' => '',
	            'user_email' => '',
	            'mobile' =>$_POST['mobile'],
	            'user_nicename' =>'',
	            'user_pass' => sp_password($password),
	            'last_login_ip' => get_client_ip(0,true),
	            'create_time' => date("Y-m-d H:i:s"),
	            'last_login_time' => date("Y-m-d H:i:s"),
	            'user_status' => 1,
	            "user_type"=>2,//会员
	        );
	        $rst = $users_model->add($data);
	        if($rst){
	            //注册成功后页面跳转
	            $data['id']=$rst;
	            $_SESSION['user']=$data;
	            $this->success("注册成功！",__ROOT__."/");
	        
	        }else{
	            $this->error("注册失败！",U("user/register/index"));
	        }
	         
	    }
	}
	
	private function _do_email_register(){
	   
        if(!sp_check_verify_code()){
            $this->error("验证码错误！");
        }
        
        $rules = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('email', 'require', '邮箱不能为空！', 1 ),
            array('password','require','密码不能为空！',1),
			array('password','number','密码长度最小5位,最大20位！',1,'5,20'),
            array('repassword', 'require', '重复密码不能为空！', 1 ),
            array('repassword','password','确认密码不正确',0,'confirm'),
            array('email','email','邮箱格式不正确！',1), // 验证email字段格式是否正确
            	
        );
	    
	     
	    $users_model=M("Users");
	     
	    if($users_model->validate($rules)->create()===false){
	        $this->error($users_model->getError());
	    }
	     
	    $password=$_POST['password'];
	    $email=$_POST['email'];
	    $username=str_replace(array(".","@"), "_",$email);
	    //用户名需过滤的字符的正则
	    $stripChar = '?<*.>\'"';
	    if(preg_match('/['.$stripChar.']/is', $username)==1){
	        $this->error('用户名中包含'.$stripChar.'等非法字符！');
	    }
	     
// 	    $banned_usernames=explode(",", sp_get_cmf_settings("banned_usernames"));
	     
// 	    if(in_array($username, $banned_usernames)){
// 	        $this->error("此用户名禁止使用！");
// 	    }
	    
	    $where['user_login']=$username;
	    $where['user_email']=$email;
	    $where['_logic'] = 'OR';
	    
	    $ucenter_syn=C("UCENTER_ENABLED");
	    $uc_checkemail=1;
	    $uc_checkusername=1;
	    if($ucenter_syn){
	        include UC_CLIENT_ROOT."client.php";
	        $uc_checkemail=uc_user_checkemail($email);
	        $uc_checkusername=uc_user_checkname($username);
	    }
	     
	    $users_model=M("Users");
	    $result = $users_model->where($where)->count();
	    if($result || $uc_checkemail<0 || $uc_checkusername<0){
	        $this->error("用户名或者该邮箱已经存在！");
	    }else{
	        $uc_register=true;
	        if($ucenter_syn){
	             
	            $uc_uid=uc_user_register($username,$password,$email);
	            //exit($uc_uid);
	            if($uc_uid<0){
	                $uc_register=false;
	            }
	        }
	        if($uc_register){
	            $need_email_active=C("SP_MEMBER_EMAIL_ACTIVE");
	            $data=array(
	                'user_login' => $username,
	                'user_email' => $email,
	                'user_nicename' =>$username,
	                'user_pass' => sp_password($password),
	                'last_login_ip' => get_client_ip(0,true),
	                'create_time' => date("Y-m-d H:i:s"),
	                'last_login_time' => date("Y-m-d H:i:s"),
	                'user_status' => $need_email_active?2:1,
	                "user_type"=>2,//会员
	            );
	            $rst = $users_model->add($data);
	            if($rst){
	                //注册成功页面跳转
	                $data['id']=$rst;
	                $_SESSION['user']=$data;
	                	
	                //发送激活邮件
	                if($need_email_active){
	                    $this->_send_to_active();
	                    unset($_SESSION['user']);
	                    $this->success("注册成功，激活后才能使用！",U("user/login/index"),2);
	                }else {
	                    $this->success("注册成功！",__ROOT__."/");
	                }
	                	
	            }else{
	                $this->error("注册失败！",U("user/register/index"));
	            }
	             
	        }else{
	            $this->error("注册失败！",U("user/register/index"));
	        }
	         
	    }
	}
	
	function active(){
		$hash=I("get.hash","");
		if(empty($hash)){
			$this->error("激活码不存在");
		}
		
		$users_model=M("Users");
		$find_user=$users_model->where(array("user_activation_key"=>$hash))->find();
		
		if($find_user){
			$result=$users_model->where(array("user_activation_key"=>$hash))->save(array("user_activation_key"=>"","user_status"=>1));
			
			if($result){
				$find_user['user_status']=1;
				$_SESSION['user']=$find_user;
				$this->success("用户激活成功，正在登录中...",__ROOT__."/");
			}else{
				$this->error("用户激活失败!",U("user/login/index"));
			}
		}else{
			$this->error("用户激活失败，激活码无效！",U("user/login/index"));
		}
		
		
	}

	/**
	*@author changeheart
	*@ ajax返回错误信息
	*@param string错误信息
	*/
	private function send_mes($mes){
		$data = array(
        	'status'=>0,
        	'mes'=>$mes,
        );
         $mes = json_encode($data);
         echo $mes;
	}
	/**
	*@author changeheart
	*@ ajax返回正确
	*
	*/
	public function ajax_do_admin_register(){	
		
		if(!sp_check_verify_code()){
            //$this->error("验证码错误！");
            $this->send_mes("验证码错误！");
	        return;
        }   
        if(!sp_check_mobile_verify_code()){
           // $this->error("验证码错误！");
           $this->send_mes("验证码错误！");
	        return;
        }     
        
        $rules = array(
            //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
            array('email', 'require', '邮箱不能为空！', 1 ),
            array('password','require','密码不能为空！',1),
            //array('password','number','密码长度最小5位,最大20位！',1,'5,20'),
            array('repassword', 'require', '重复密码不能为空！', 1 ),
            array('repassword','password','确认密码不正确',0,'confirm'),
            array('email','email','邮箱格式不正确！',1), // 验证email字段格式是否正确
            	
        );
	    
	     
	    $users_model=M("Users");
	     
	    if($users_model->validate($rules)->create()===false){
	        //$this->error($users_model->getError());
	        $this->send_mes($users_model->getError());
	        return;
	    }
	     
	    $password=$_POST['password'];
	    $email=$_POST['email'];
	    $user_login = $_POST['user_login'];
	    $mobile = $_POST['mobile'];
	    //$qq = $_POST['qq'];
	    //$weixin = $_POST['weixin'];
	    
	    $username=str_replace(array(".","@"), "_",$email);
	    //用户名需过滤的字符的正则
	    $stripChar = '?<*.>\'"';
	    if(preg_match('/['.$stripChar.']/is', $username)==1){
	        //$this->error('用户名中包含'.$stripChar.'等非法字符！');
	        $this->send_mes('用户名中包含'.$stripChar.'等非法字符！');
	        return;
	    }
	     
// 	    $banned_usernames=explode(",", sp_get_cmf_settings("banned_usernames"));
	     
// 	    if(in_array($username, $banned_usernames)){
// 	        $this->error("此用户名禁止使用！");
// 	    }
	    
	    // $where['user_login']=$user_login;
	    // $where['user_email']=$email;
	    // $where['_logic'] = 'OR';
	    $where1['user_login']=$user_login;
	    $where2['user_email']=$email;
	    
	    $ucenter_syn=C("UCENTER_ENABLED");
	    $uc_checkemail=1;
	    $uc_checkusername=1;
	    if($ucenter_syn){
	        include UC_CLIENT_ROOT."client.php";
	        $uc_checkemail=uc_user_checkemail($email);
	        $uc_checkusername=uc_user_checkname($username);
	    }
	     
	    $users_model=M("Users");
	    $result1 = $users_model->where($where1)->count();
	    $result2 = $users_model->where($where2)->count();
	    if($result2 || $uc_checkemail<0){
	        //$this->error("用户名或者该邮箱已经存在！");
	         $this->send_mes('该邮箱已经存在！');
	        return;
	    }else if($result1 || $uc_checkusername<0){
	    	 $this->send_mes('用户名已经存在！');
	        return;
	    }else{
	        $uc_register=true;
	        if($ucenter_syn){
	             
	            $uc_uid=uc_user_register($username,$password,$email);
	            //exit($uc_uid);
	            if($uc_uid<0){
	                $uc_register=false;
	            }
	        }
	        if($uc_register){
	            $need_email_active=C("SP_MEMBER_EMAIL_ACTIVE");
	            $data=array(
	                'user_login' => $user_login,
	                'user_email' => $email,
	                'user_nicename' =>$username,
	                'mobile' => $mobile,
	                //'qq' => $qq,
	                //'weixin' => $weixin,
	                'user_pass' => sp_password($password),
	                'last_login_ip' => get_client_ip(0,true),
	                'create_time' => date("Y-m-d H:i:s"),
	                'last_login_time' => date("Y-m-d H:i:s"),
	                'user_status' => $need_email_active?2:1,
	                "user_type"=>2,//会员
	            );
	            $rst = $users_model->add($data);
	            if($rst){
	                //注册成功页面跳转
	                $data['id']=$rst;
	                $_SESSION['user']=$data;	                	                //暂时不发送激活邮件，直接跳转主页	                $this->success("注册成功，激活后才能使用！",U("cuntao/weixin"));
	                	
	                //发送激活邮件
	                if($need_email_active){
	                    $this->_send_to_active();
	                    unset($_SESSION['user']);
	                    $data = array(
	                    	'status'=>1,
	                    	'url'=>'?g=user&m=login=&a=index',
	                    );
	                   
	                    //$this->success("注册成功，激活后才能使用！",U("user/login/index"));
	                }else {
	                	 $data = array(
	                    	'status'=>1,
	                    	'url'=>'?g=portal&m=cuntao=&a=personal_put',
	                    );
	                   // $this->success("注册成功，进入个人放单中心！",U("portal/cuntao/personal_put"));
	                }
	                 $mes = json_encode($data);
	                echo $mes;
	                	
	            }else{
	               // $this->error("注册失败！",U("cuntao/login_reg"));
	                 $this->send_mes('注册失败！');
	        return;
	            }
	             
	        }else{
	            //$this->error("注册失败！",U("cuntao/login_reg"));
	            $this->send_mes('注册失败！');
	       		 return;
	        }
	         
	    }
	}
	
	
}
