<?php
set_include_path('D:\wwwroot\chenjin\wwwroot');
include_once("inc/init.php");

//详情
$id  = irequest('id');
$sql = "SELECT * FROM product WHERE id = '{$id}'";
$res = $db->get_row($sql);
$res['pic_list'] = explode('|', $res['pics']);
$smarty->assign('case', $res);

if (empty($res['id']))
{
	url_locate('/case/', '您访问的内容不存在');
} 

//浏览次数增1
$sql = "UPDATE product SET view_num = view_num + 1 WHERE id = '{$id}'";
$db->query($sql);

$page_title = $res['name'] . ' - 品牌案例 - ' . WEB_NAME;
$smarty->assign('page_title', $page_title);

$temp = $res['is_pub'] ? 'case/view.html' : 'case/forw.html';
$smarty->assign('menu', 'case');
$smarty->display($temp);
