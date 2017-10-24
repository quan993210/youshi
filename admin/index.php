<?php
set_include_path(dirname(dirname(__FILE__)));
include_once("inc/init.php");

//每天登录次数限制
$limit_num = 5;

$action = crequest("action");
$action = $action == '' ? 'index' : $action;  

switch ($action) 
{	   
    case 'index':
			    display_index();
				break;
	case 'login':
			    login();
				break;
	case 'logout':
			    logout();
				break;
	case 'menu':
				menu();
				break;
	case 'top':
				top();
				break;
	case 'drag':
				drag();
				break;			
	case 'main':
				admin_main();
				break;		  
	case "mod_pwd":
                mod_pwd();
				break;
	case "do_mod_pwd":
                do_mod_pwd();
				break;				
	case "clear_cache":
                clear_cache();
				break;	
}

/*------------------------------------------------------ */
//-- 显示菜单页面
/*------------------------------------------------------ */
function menu()
{
	global $smarty;
	

	//$sub[]  = array('url' => 'info.php?cat=about', 		'name' => '关于我们');
	//$sub[]  = array('url' => 'info.php?cat=contact', 	'name' => '联系我们');
	//$sub[]  = array('url' => 'message.php', 			'name' => '留言管理');
	$sub[]  = array('url' => 'log.php', 				'name' => '日志管理');
	$sub[]  = array('url' => 'admin.php', 				'name' => '管理员管理');
	$sub[]  = array('url' => 'member.php', 				'name' => '会员列表');
//	$sub[]  = array('url' => 'news_category.php', 	'name' => '内容分类');
//	$sub[]  = array('url' => 'news.php', 			'name' => '内容列表');
//	$sub[]  = array('url' => 'dangfei.php', 			'name' => '党费列表');
//	$sub[]  = array('url' => 'metting.php', 			'name' => '会议列表');
//	$sub[]  = array('url' => 'carousel.php', 			'name' => '首页轮播图');
//
	$menu[] = array('name' => '网站管理', 				'sub' => $sub);
//
//	//unset($sub);
//
//	//$menu[] = array('name' => '内容管理', 				'sub' => $sub);
//
//	//unset($sub);
//	//$sub[]  = array('url' => 'product_category.php', 	'name' => '产品分类');
//	//$sub[]  = array('url' => 'product.php', 			'name' => '案例列表');
//	//$menu[] = array('name' => '案例管理', 				'sub' => $sub);
//
//	unset($sub);
//	$sub[]  = array('url' => 'timu_category.php', 		'name' => '题目分类');
//	$sub[]  = array('url' => 'timu.php', 				'name' => '题目列表');
//	$sub[]  = array('url' => 'test.php', 				'name' => '测试试卷');
//	$menu[] = array('name' => '题目管理', 				'sub' => $sub);

	//unset($sub);
	//$sub[]  = array('url' => 'merchant.php', 				'name' => '商户列表');
	//$menu[] = array('name' => '商户管理', 				'sub' => $sub);
	
	$smarty->assign('menu', $menu);
	$smarty->display('common/menu.htm');
}

/*------------------------------------------------------ */
//-- 显示缩放页面
/*------------------------------------------------------ */
function drag()
{
	global $smarty;
	$smarty->display('common/drag.htm');
}

/*------------------------------------------------------ */
//-- 显示顶部页面
/*------------------------------------------------------ */
function top()
{
	global $smarty;
	
	$admin = $_SESSION['admin'];
	$smarty->assign('admin', $admin);
	
	$smarty->display('common/top.htm');
}

/*------------------------------------------------------ */
//-- 显示后台页面
/*------------------------------------------------------ */
function admin_main()
{
	global $db, $smarty;
	
	/* 系统信息 */
	$sys_info['domain']     = $_SERVER['SERVER_NAME'];
	$sys_info['os']         = PHP_OS;
	$sys_info['ip']         = $_SERVER['SERVER_ADDR'];
	$sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
	$sys_info['server_port']= $_SERVER['SERVER_PORT'];
	$sys_info['php_ver']    = PHP_VERSION;
	$sys_info['safe_mode']  = (boolean) ini_get('safe_mode') ? '是' : '否';
	$sys_info['socket']     = function_exists('fsockopen') ?  '是' : '否';
	$sys_info['mysql']	    = $db->version(); 
	$sys_info['char_set']	= CHAR_SET; 
	$sys_info['gd']			= gd_version();
	
	$sys_info['copyright']  = WEB_NAME;
	$sys_info['version']   	= '1.0';
	$sys_info['support'] 	= '辰锦科技';
	$sys_info['service'] 	= '18070123163';
	$sys_info['qq']       	= '250686110';
	
	$smarty->assign('sys_info', $sys_info);
	$smarty->display('common/main.htm');
}

/*------------------------------------------------------ */
//-- 显示登录页面
/*------------------------------------------------------ */
function display_index()
{
	global $smarty;
	
	if (empty($_SESSION["admin_id"]))
		$smarty->display('common/login.htm');
	else
		$smarty->display('index.htm');
}

/*------------------------------------------------------ */
//-- 管理员登陆
/*------------------------------------------------------ */
function login()
{	
	global $db, $smarty;
	
	$userid = crequest('userid');
	$pwd 	= md5(crequest('pwd'));
	
	check_null($userid, '用户名');
	check_null($pwd, '密码');
	
	//check_code();			//验证验证码
	
	$sql = "select id, password from admin where userid = '{$userid}' AND is_del = 0";
	$row = $db->get_row($sql);
	
	if(!empty($row['id']))
	{
		$password = $row['password'];
		$admin_id = $row['id'];
		$ip 	  = real_ip();
		$now_time = now_time();
		
		check_login_times($admin_id);    //判断是否已经超过登录次数
		
		if($password == $pwd )
		{
			$sql = "UPDATE admin SET last_login_time = '{$now_time}', last_login_ip = '{$ip}', login_num = login_num + 1 WHERE userid = '{$userid}'";
			$db->query($sql);
			
			login_log($admin_id, 1);
				
			// 声明一个名为 admin 的变量，并赋空值。
			$_SESSION["admin_id"] = $admin_id;
			$_SESSION["admin"]    = $userid;

			//$smarty->display('welcome.htm');
			href_locate('index.php');
		}
		else
		{
			login_log($admin_id, 2);

		}

	}
	else
	{
		alert_back("用户名或密码错误！");

	}
}

function check_code()
{
	$safecode = crequest('safecode');
	check_null($safecode, '验证码');
	
	if (md5($safecode) != $_SESSION['safecode'])
	{
		alert_back('验证码不正确');
		die;
	}
	
	return;
}

/*------------------------------------------------------ */
//-- 登录记录
/*------------------------------------------------------ */
function login_log($aid, $status)
{
	global $db, $limit_num;
	
	$text = $status == 1 ? '登录成功' : '登录失败';
	operate_log($aid, 0, $status, $text);
	
	if ($status == 2)
	{
		$num = get_fail_num();;			
		$last_num  = $limit_num - $num;
		alert_back("密码错误，您今天还{$last_num}次登陆机会!");
	}
	
	return;
}

/*------------------------------------------------------ */
//-- 判断是否已经超过登录次数
/*------------------------------------------------------ */
function check_login_times($aid)
{
	global $db, $limit_num;
	
	$num = get_fail_num();
	
	if ($num >= $limit_num)
	{
		alert_back('已超过登录次数，今天不允许登录');
		exit;
	}
	
	return;
}

function get_fail_num()
{
	global $db;
	
	$today = date('Ymd');
	$sql = "SELECT COUNT(id) FROM operate_log WHERE aid = '{$aid}' AND today = '{$today}' AND status = '2' AND type = 0";
	$num = $db->get_one($sql);
	
	return $num;
}

/*------------------------------------------------------ */
//-- 修改密码
/*------------------------------------------------------ */	
function mod_pwd()
{
	global $smarty;
	
	$page_title = '修改密码';
    $smarty->assign('page_title', $page_title);
	
	$smarty->assign('action', 'do_mod_pwd');
	$smarty->display('common/mod_pwd.htm');
}

/*------------------------------------------------------ */
//-- 修改密码
/*------------------------------------------------------ */	
function do_mod_pwd()
{
    global $db;
	
	$old_pwd = md5(crequest('old_pwd'));
    $new_pwd = md5(crequest('new_pwd'));
	$cfm_pwd = md5(crequest('cfm_pwd'));

	check_null($old_pwd, '旧密码');
	check_null($new_pwd, '新密码');
	check_null($cfm_pwd, '确认新密码');
	check_pwd_same($new_pwd, $cfm_pwd);

	$admin_id = $_SESSION["admin_id"];
	$sql = "SELECT password FROM admin WHERE id = '{$admin_id}'";
	$password = $db->get_one($sql);
	
	if ($old_pwd != $password)
	{
		alert_back('旧密码输入错误');
	}
	
	$sql = "UPDATE admin set password = '{$new_pwd}' WHERE id = '{$admin_id}'";
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '修改密码';
	operate_log($aid, 0, 3, $text);

	$url_to = "index.php?action=mod_pwd";
	url_locate($url_to, '修改成功');	
}

/*------------------------------------------------------ */
//-- 退出登陆
/*------------------------------------------------------ */
function logout()
{
	unset($_SESSION["admin"]);
	unset($_SESSION["admin_id"]);
	
	echo "<SCRIPT LANGUAGE='JavaScript'>";
	echo "parent.location.href='index.php';";
	echo "</SCRIPT>";
}

function clear_cache()
{
	$dir = dir("../temp/compiled/");
	
	//列出 images 目录中的文件
	while (($file = $dir->read()) !== false)
	{
		if ($file != '.' && $file != '..')
			@unlink("../temp/compiled/" . $file);
		//echo "filename: " . $file . "<br />";
	}
	
	$dir->close();
	
	echo '清除成功';
}
?>
