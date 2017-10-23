<?php
set_include_path('D:\wwwroot\chenjin\wwwroot');
include_once("inc/init.php");

$article = get_news_list();
$smarty->assign('article', $article);

$action  = crequest('action');
if ($action == 'ajax_get_data')
{
	$smarty->display('article/list.html');
	die;
}

function get_news_list()
{
	global $db;

	$now_page 	= irequest('page');
	$now_page 	= $now_page == 0 ? 1 : $now_page;	
	
	$page_size  = 5;
	$start    	= ($now_page - 1) * $page_size;	
	
	$sql = "SELECT * FROM article WHERE cid = '16' ORDER BY add_time DESC LIMIT {$start}, {$page_size}";
	$res = $db->get_all($sql);
	
	return $res;
	
}

$cid = 16;
$sql = "SELECT * FROM article_category WHERE id = '{$cid}'";
$cat_info = $db->get_row($sql);
$smarty->assign('cat_info', $cat_info);


//广告
$pos = 31;
$ad = get_ads($pos, 1);
$smarty->assign('ad', $ad);
	
/*$sql 		= "SELECT COUNT(id) FROM article WHERE cid = '{$cid}'";
$total 		= $db->get_one($sql);
$page     	= new page(array('total'=>$total, 'page_size'=>$page_size));
	
$smarty->assign('pageshow'  ,   $page->show(3));*/

$page_title = '最新动态 - ' . WEB_NAME;
$smarty->assign('page_title', $page_title);

$smarty->assign('menu', 'article');
$smarty->display('article/index.html');
