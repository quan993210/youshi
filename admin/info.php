<?php
set_include_path(dirname(dirname(__FILE__)));
include_once("inc/init.php");
require("inc/lib_common.php");

$action = crequest("action");
$action = $action == '' ? 'mod_info' : $action; 

switch ($action) 
{
	   	case "mod_info":
                      mod_info();
					  break;
		case "do_mod_info":
                      do_mod_info();
					  break;				  					  	
}

/*------------------------------------------------------ */
//-- 修改产品
/*------------------------------------------------------ */	
function mod_info()
{
	global $db, $smarty;
	
	$cat = crequest('cat');
	$sql = "SELECT * FROM info WHERE cat = '{$cat}'";
	$row = $db->get_row($sql);
	$smarty->assign('info', $row);
	
	$temp = 'info/info.htm';	
	switch ($cat)
	{
		case 'about';
				$page_title = '公司简介';
				break;	
		case 'contact';
				$page_title = '联系我们';
				break;					
	}
	
    $smarty->assign('page_title', $page_title);
	$smarty->assign('action', 'do_mod_info');
	$smarty->display($temp);
}

/*------------------------------------------------------ */
//-- 修改产品
/*------------------------------------------------------ */	
function do_mod_info()
{
	global $db;
		
	$cat = crequest('cat');
	$content = $_REQUEST['content'];
	
	
	$update_col = "content = '{$content}'";
	$cat = crequest('cat');
	$sql = "UPDATE info SET {$update_col} WHERE cat = '{$cat}'";
	$db->query($sql);
	
	switch ($cat)
	{
		case 'about';
				$page_title = '公司简介';
				break;	
		case 'contact';
				$page_title = '联系我们';
				break;					
	}
	
	$text = '修改' . $page_title;
	
	$aid  = $_SESSION['admin_id'];
	operate_log($aid, $cat, 2, $text);

	$now_page = irequest('now_page');
	$url_to = "info.php?cat=$cat";
	url_locate($url_to, '修改成功');	
}
