<?php
set_include_path('D:\wwwroot\chenjin\wwwroot');
include_once("inc/init.php");

//详情
$id  = irequest('id');
$sql = "SELECT * FROM article WHERE id = '{$id}'";
$res = $db->get_row($sql);
$smarty->assign('article', $res);

if (empty($res['id']))
{
	url_locate('/article/', '您访问的内容不存在');
} 

//浏览次数增1
$sql = "UPDATE article SET view_num = view_num + 1 WHERE id = '{$id}'";
$db->query($sql);

$page_title = $res['title'] . ' - 最新动态 - ' . WEB_NAME;
$smarty->assign('page_title', $page_title);

$smarty->assign('menu', 'article');
$smarty->display('article/view.html');
