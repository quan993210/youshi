<?php
set_include_path(dirname(dirname(__FILE__)));
include_once("inc/init.php");

$action = crequest("action");
$action = $action == '' ? 'list' : $action; 

switch ($action) 
{
		case "list":
                      log_list();
					  break;
		case "del_log":
                      del_log();
					  break;
	   	case "del_sel_log":
                      del_sel_log();
					  break;			  
}

/*------------------------------------------------------ */
//-- 日志列表
/*------------------------------------------------------ */	
function log_list()
{
	global $db, $smarty;
	
	//搜索条件
	$search_cat = irequest('search_cat');
	$keyword 	= crequest('keyword');	
	$con 		= '';    
	
	//排序字段
	$sort_col 	 = crequest('sort_col');	
	$asc_or_desc = crequest('asc_or_desc');
	$order 	 	 = 'ORDER BY o.add_time DESC';        
	
	//列表信息
	$now_page 	= irequest('page');
	$now_page 	= $now_page == 0 ? 1 : $now_page;	
	$page_size 	= 20;
	$start    	= ($now_page - 1) * $page_size;	
	$sql 		= "SELECT o.*, a.userid AS admin FROM operate_log AS o LEFT JOIN admin AS a ON o.aid = a.id {$con} {$order} LIMIT {$start}, {$page_size}";
	$arr 		= $db->get_all($sql);
	
	$sql 		= "SELECT COUNT(id) FROM operate_log {$con}";
	$total 		= $db->get_one($sql);
	$page     	= new page(array('total'=>$total, 'page_size'=>$page_size));
	
	$smarty->assign('log_list' ,   $arr);
	$smarty->assign('pageshow'    ,   $page->show(6));
	$smarty->assign('now_page'    ,   $page->now_page);
	
	//表信息
	$tbl = array('tbl' => 'operate_log');			
	$smarty->assign('tbl', $tbl);
	
    $smarty->assign('page_title', '日志列表');
	$smarty->display('system/log_list.htm');	
}

/*------------------------------------------------------ */
//-- 删除日志
/*------------------------------------------------------ */	
function del_log()
{
	global $db;
	
	$id = irequest('id');
	$sql = "DELETE FROM operate_log WHERE id = '{$id}'";
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '删除日志，删除日志ID：' . $id;
	operate_log($aid, 90, 3, $text);
	
	$now_page = irequest('now_page');
	$url_to = "log.php?action=list&page=$now_page";
	href_locate($url_to);	
}

/*------------------------------------------------------ */
//-- 批量删除日志
/*------------------------------------------------------ */	
function del_sel_log()
{
	global $db;
	
	$id = crequest('checkboxes');
	
	if ($id == '')
	{
		alert_back('请选中需要删除的选项');
	}
	
	$sql = "DELETE FROM operate_log WHERE id IN ({$id})";
	$db->query($sql);
	
	$aid  = $_SESSION['admin_id'];
	$text = '删除日志，删除日志ID：' . $id;
	operate_log($aid, 90, 4, $text);
	
	$now_page = irequest('now_page');
	$url_to = "log.php?action=list&page=$now_page";
	href_locate($url_to);	
}
