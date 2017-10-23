<?php
set_include_path(dirname(dirname(__FILE__)));
include_once("inc/init.php");
require("inc/lib_common.php");

$action = crequest("action");
$action = $action == '' ? 'list' : $action; 

switch ($action) 
{
		case "list":
                      admin_list();
					  break;			  
	   	case "add_admin":
                      add_admin();
					  break;
		case "do_add_admin":
                      do_add_admin();
					  break;			  
	   	case "mod_admin":
                      mod_admin();
					  break;				  
	   	case "do_mod_admin":
                      do_mod_admin();
					  break;
		case "del_admin":
                      del_admin();
					  break;
	   	case "del_sel_admin":
                      del_sel_admin();
					  break;				  					  	
}

/*------------------------------------------------------ */
//-- 管理员列表
/*------------------------------------------------------ */	
function admin_list()
{
	global $db, $smarty;
	
	//搜索条件
	$search_cat = irequest('search_cat');
	$keyword 	= crequest('keyword');	
	$con 		= '';    
	
	//排序字段
	$sort_col 	 = crequest('sort_col');	
	$asc_or_desc = crequest('asc_or_desc');
	//$order 	 	 = 'ORDER BY id';        
	
	//列表信息
	$now_page 	= irequest('page');
	$now_page 	= $now_page == 0 ? 1 : $now_page;	
	$page_size 	= 20;
	$start    	= ($now_page - 1) * $page_size;	
	//$sql 		= "SELECT a.*, COUNT(m.id) AS total FROM admin AS a LEFT JOIN member AS m ON a.id = m.admin_id {$con} {$order} GROUP BY m.admin_id LIMIT {$start}, {$page_size}";
	$sql 		= "SELECT * FROM admin {$con} {$order} LIMIT {$start}, {$page_size}";
	$arr 		= $db->get_all($sql);
	
	$sql 		= "SELECT COUNT(id) FROM admin {$con}";
	$total 		= $db->get_one($sql);
	$page     	= new page(array('total'=>$total, 'page_size'=>$page_size));
	
	$smarty->assign('admin_list'  ,   $arr);
	$smarty->assign('pageshow'    ,   $page->show(6));
	$smarty->assign('now_page'    ,   $page->now_page);
	
	//表信息
	$tbl = array('tbl' => 'admin', 'col1' => 'userid');			
	$smarty->assign('tbl', $tbl);
	
    $smarty->assign('page_title', '管理员列表');
	$smarty->display('admin/admin_list.htm');	
}

/*------------------------------------------------------ */
//-- 添加管理员
/*------------------------------------------------------ */	
function add_admin()
{
	global $db, $smarty;
	
	$page_title = '添加管理员';
    $smarty->assign('page_title', $page_title);
	
	$smarty->assign('action', 'do_add_admin');
	$smarty->display('admin/admin.htm');
}

/*------------------------------------------------------ */
//-- 添加管理员
/*------------------------------------------------------ */	
function do_add_admin()
{
	global $db;
	
    $userid   = crequest('username');
    $pwd 	  = crequest('pwd');
    $cfm_pwd  = crequest('cfm_pwd');
	$email    = crequest('email');
	$now_time = now_time();

	check_null($userid  ,   '用户名');
	check_null($pwd     ,   '密码');
	check_pwd_same($pwd ,   $cfm_pwd);
	
	$sql = "SELECT id FROM admin WHERE userid = '{$userid}'";
	$is_exist = $db->get_one($sql);
	
	if ($is_exist)
	{
		alert_back('此用户名已存在，请重新输入');
	}
	
	$code = '';
	$admin_group = '';
	$pwd = md5($pwd);
	$sql = "INSERT INTO admin (userid, password, email, add_time, is_del) ".
		   "VALUES ('{$userid}', '{$pwd}', '{$email}', '{$now_time}', '0')";
	//echo $sql;die;	   
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '添加管理员，添加管理员ID：' . $db->insert_id();
	operate_log($aid, 1, 1, $text);

	$url_to = "admin.php?action=list";
	url_locate($url_to, '添加成功');	
}

/*------------------------------------------------------ */
//-- 修改管理员权限
/*------------------------------------------------------ */	
function mod_admin()
{	
	global $db, $smarty;
	
	$id  = irequest('id');
	$sql = "SELECT * FROM admin WHERE id = '{$id}'";
	$res = $db->get_row($sql);
	$smarty->assign('admin', $res);
	
    $page_title = '管理员信息修改';
    $smarty->assign('page_title', $page_title);
	
	$smarty->assign('now_page' , irequest('now_page'));
	$smarty->assign('action'   , 'do_mod_admin');
	$smarty->display('admin/admin.htm');	
}

/*------------------------------------------------------ */
//-- 修改管理员权限
/*------------------------------------------------------ */	
function do_mod_admin()
{
	global $db;
	
    $id 	  = irequest('id');
	$userid   = crequest('username');
    $pwd 	  = crequest('pwd');
	$email    = crequest('email');
	
	$admin_group = '';
	$code 		 = '';
	
	$sql = "UPDATE admin set userid = '{$userid}', email = '{$email}' ";
	$sql .= $pwd == '' ? "WHERE id = '{$id}'" : ", password = '" . md5($pwd) . "' WHERE id = '{$id}'";
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '修改管理员信息，修改管理员ID：' . $id;
	operate_log($aid, 1, 2, $text);

	$now_page = irequest('now_page');
	$url_to = "admin.php?action=list&page=$now_page";
	url_locate($url_to, '修改成功');		
}

/*------------------------------------------------------ */
//-- 删除管理员
/*------------------------------------------------------ */	
function del_admin()
{
	global $db;
	
	$id = irequest('id');
	$sql = "DELETE FROM admin WHERE id = '{$id}'";
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '删除管理员，删除管理员ID：' . $id;
	operate_log($aid, 1, 3, $text);
	
	$now_page = irequest('now_page');
	$url_to = "admin.php?action=list&page=$now_page";
	href_locate($url_to);
}

/*------------------------------------------------------ */
//-- 批量删除管理员
/*------------------------------------------------------ */	
function del_sel_admin()
{
	global $db;
	
	$id = crequest('checkboxes');
	
	if ($id == '')
	{
		alert_back('请选中需要删除的选项');
	}
	
	$sql = "DELETE FROM admin WHERE id IN ({$id})";
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '删除管理员，删除管理员ID：' . $id;
	operate_log($aid, 1, 4, $text);
	
	$now_page = irequest('now_page');
	$url_to = "admin.php?action=list&page=$now_page";
	href_locate($url_to);	
}

