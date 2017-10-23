<?php
set_include_path('D:\wwwroot\chenjin\wwwroot');
include_once("inc/init.php");

$cases = get_case(1, 100, 0);
$smarty->assign('cases', $cases);

$page_title = '品牌案例 - ' . WEB_NAME;
$smarty->assign('page_title', $page_title);

$smarty->assign('menu', 'case');
$smarty->display('case/index.html');
