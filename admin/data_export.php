<?php	
    set_include_path('D:\wwwroot\chenjin\wwwroot');
include_once("inc/init.php");
	
    $smarty->assign('page_title', '导出数据');
    $smarty->display('data_export.htm');
?>
