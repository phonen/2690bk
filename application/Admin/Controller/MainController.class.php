<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MainController extends AdminbaseController {

	

    public function index(){

    	

    	$mysql= M()->query("select VERSION() as version");

    	$mysql=$mysql[0]['version'];

    	$mysql=empty($mysql)?L('UNKNOWN'):$mysql;

    	

    	//server infomaions

    	$info = array(

    			L('OPERATING_SYSTEM') => PHP_OS,

    			L('OPERATING_ENVIRONMENT') => $_SERVER["SERVER_SOFTWARE"],

    			L('PHP_RUN_MODE') => php_sapi_name(),

    			L('MYSQL_VERSION') =>$mysql,

    			L('PROGRAM_VERSION') => SIMPLEWIND_CMF_VERSION . "&nbsp;&nbsp;&nbsp; [<a href='http://www.thinkcmf.com' target='_blank'>ThinkCMF</a>]",

    			L('UPLOAD_MAX_FILESIZE') => ini_get('upload_max_filesize'),

    			L('MAX_EXECUTION_TIME') => ini_get('max_execution_time') . "s",

    			L('DISK_FREE_SPACE') => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',

    	);

    	$this->assign('server_info', $info);
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