<?php	
	session_start();
	
	//设置时区
	@ini_set('date.timezone', 'Asia/Shanghai');
	
	//确定文件编码
	header('Content-type: text/html; charset=utf-8' );	

    //获得根目录路径
	define('ROOT_PATH', str_replace('inc/init.php', '', str_replace('\\', '/', __FILE__)));
	
	//获得当前文件路径
	define('CURRENT_PATH', $_SERVER['PHP_SELF']);
		
	//公共配置文件
	$environment = 'dev';
	if ($environment == 'dev')
		require(ROOT_PATH . 'inc/conf/dev.php');
	else
		require(ROOT_PATH . 'inc/conf/pro.php');
	

	//公共函数文件
	require(ROOT_PATH . 'inc/lib/common.php');
	

	//类函数文件
	require(ROOT_PATH . 'inc/model/class.page.php');
	require(ROOT_PATH . 'inc/model/class.sina.php');
	require(ROOT_PATH . 'inc/model/class.image.php');
	require(ROOT_PATH . 'inc/model/class.member.php');
	require(ROOT_PATH . 'inc/model/class.award.php');
	require(ROOT_PATH . 'inc/model/class.mysqli.php');
	require(ROOT_PATH . 'inc/model/class.phpmailer.php');
	
    //插件
	if (TEMPLATE == 'smarty')
		require(ROOT_PATH . 'inc/plugin/smartylibs/Smarty.class.php');
	else
		require(ROOT_PATH . 'inc/plugin/templatelite/class.template.php');
	
	require(ROOT_PATH . 'inc/plugin/PHPExcel/PHPExcel.php');
	
	
	//smarty配置
	if (TEMPLATE == 'smarty')
    	$smarty = new Smarty;
    else
    	$smarty = new Template_Lite;

	if (strpos(CURRENT_PATH, ADMIN_DIR) === false)
	{
		$smarty->template_dir 	= ROOT_PATH . TEMP_PAGE;
	}
	else 
	{		
		$smarty->template_dir 	= ROOT_PATH . TEMP_ADMIN;
		
		//后台判断是否登录
		check_admin_login();
	}
	
    $smarty->cache_dir		= ROOT_PATH . TEMP_CACHE;
    $smarty->compile_dir	= ROOT_PATH . TEMP_COMPILE;
	$smarty->cache_lifetime = CACHE_TIME; 
    
    if (DEBUG_MODE)
    {
		$smarty->caching = false;
        $smarty->force_compile = true;        
    }
    else
    {
        $smarty->caching = true;
		$smarty->force_compile = false;
    }
	
	$smarty->assign('sys_name', SYS_NAME);
	$smarty->assign('url_path', URL_PATH);
	$smarty->assign('admin_temp_path', ADMIN_TEMP_PATH);
	
	//初始化数据库连接
	$db = new cls_mysql(DB_HOST, DB_USER, DB_PASS, DB_NAME, CHAR_SET, 0, 1);
	
	//require(ROOT_PATH . 'inc/conf/cache.php');
	require(ROOT_PATH . 'inc/lib/company.php');
?>