<?php
set_include_path('D:\wwwroot\chenjin\wwwroot');
include_once("inc/init.php");
session_start();

$action = crequest("action");
$action = $action == '' ? 'list' : $action; 

switch ($action) 
{
		case "list":
                      message_list();
					  break;	  			  
	   	case "view_message":
                      view_message();
					  break;
		case "del_message":
                      del_message();
					  break;
	   	case "del_sel_message":
                      del_sel_message();
					  break;				  					  	
}

/*------------------------------------------------------ */
//-- 留言列表
/*------------------------------------------------------ */	
function message_list()
{
	global $db, $smarty;
	
	//搜索条件
	$search_cat = irequest('search_cat');
	$keyword 	= crequest('keyword');	
	$con 		= "WHERE title like '%{$keyword}%'";    
	
	//排序字段
	$order 	 	= 'ORDER BY add_time DESC';        
	
	//列表信息
	$now_page 	= irequest('page');
	$now_page 	= $now_page == 0 ? 1 : $now_page;	
	$page_size 	= 30;
	$start    	= ($now_page - 1) * $page_size;	
	$sql 		= "SELECT * FROM " . PREFIX . "message {$con} {$order} LIMIT {$start}, {$page_size}";
	$arr 		= $db->get_all($sql);
	
	$sql 		= "SELECT COUNT(id) FROM " . PREFIX . "message {$con}";
	$total 		= $db->get_one($sql);
	$page     	= new page(array('total'=>$total, 'page_size'=>$page_size));
	
	$smarty->assign('message_list'   ,   $arr);
	$smarty->assign('pageshow'    ,   $page->show(6));
	$smarty->assign('now_page'    ,   $page->now_page);
	
	//表信息
	$tbl = array('tbl' => 'message');			
	$smarty->assign('tbl', $tbl);
	
    $smarty->assign('page_title', '留言列表');
	$smarty->display('message/message_list.htm');	
}

/*------------------------------------------------------ */
//-- 添加留言
/*------------------------------------------------------ */	
function view_message()
{
	global $db, $smarty;
	
	$id = irequest('id');
	$sql = "SELECT * FROM " . PREFIX . "message WHERE id = '{$id}'";
	$row = $db->get_row($sql);
	
	$smarty->assign('message', $row);
	$smarty->assign('page_title', '留言查看');
	$smarty->display('message/message.htm');
}

/*------------------------------------------------------ */
//-- 删除留言
/*------------------------------------------------------ */	
function del_message()
{
	global $db;
	
	$id = irequest('id');
	$sql = "DELETE FROM " . PREFIX . "message WHERE id = '{$id}'";
	$db->query($sql);
	
	$now_page = irequest('now_page');
	$url_to = "message.php?action=list&page={$now_page}";
	url_locate($url_to, '删除成功');	
}

/*------------------------------------------------------ */
//-- 批量删除留言
/*------------------------------------------------------ */	
function del_sel_message()
{
	global $db;
	
	$id = crequest('checkboxes');
	if (empty($id))alert_back('请选中需要删除的选项');
	
	$sql = "DELETE FROM " . PREFIX . "message WHERE id IN ({$id})";
	$db->query($sql);
	
	$now_page = irequest('now_page');
	$url_to = "message.php?action=list&page={$now_page}";
	url_locate($url_to, '删除成功');	
}
?>