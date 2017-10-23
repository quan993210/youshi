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

$is_weixin = 1;
$useragent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($useragent, 'MicroMessenger') === false)$is_weixin = 0;
$smarty->assign('is_weixin', $is_weixin);

$smarty->assign('device_type', get_device_type());

$smarty->display('case/down.html');

function get_device_type()
{
	//全部变成小写字母
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$type  = 'other';
	
	//分别进行判断
	if(strpos($agent, 'iphone') || strpos($agent, 'ipad'))
	{
		$type = 'ios';
	} 
	 
	if(strpos($agent, 'android'))
	{
		$type = 'android';
	}
	
	return $type;
}
