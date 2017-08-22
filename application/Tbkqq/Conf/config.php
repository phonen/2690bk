<?php
$configs = array(
    'TAGLIB_BUILD_IN' => THINKCMF_CORE_TAGLIBS . ',Portal\Lib\Taglib\Portal',
    'TMPL_TEMPLATE_SUFFIX' => '.html', // 默认模板文件后缀
    'TMPL_FILE_DEPR' => '/', // 模板文件MODULE_NAME与ACTION_NAME之间的分割符
    'SITE_APPNAME' => 'yhg',
    'BASE_DOMAIN' => '2690.cn',
    'ROBOT_USERNAME' => '张峰2009',
    'SITE_USERNAME'	=> '张峰2009',
    'TOKEN_APPKEY' =>	'23604248',
    'TOKEN_SECRETKEY' => '8b653d644b292995157be68d24a5d215',
    'YONGJIN_RATE'	=> 0.3,
    'TOKEN' => array (
      array(
      	'TOKEN_APPKEY' =>	'23604248',
    		'TOKEN_SECRETKEY' => '8b653d644b292995157be68d24a5d215',
      ),
      
		  array(
			  'TOKEN_SECRETKEY' => 'cb2f299dd17222ac22a9919401cc61b6',
				'TOKEN_APPKEY' =>	'23573257',
		  ),
		  ),
);

return array_merge($configs);
