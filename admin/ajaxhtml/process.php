<?php
include_once("../../inc/init.php");
session_start();
if($_SESSION["admin"] == '')
{
	die("登录失效");
}
switch (crequest('action')) 
{
	case 'mod_col':
			mod_col();
			break;		  			  				  	
}

/*------------------------------------------------------ */
//-- 修改相关列的值
/*------------------------------------------------------ */	
function mod_col()
{	
	$id  = irequest('id');
	if ($id == 0)
	{
		echo '1';
		die;
	}
	
	global $db;
	$val = crequest('val');
	$tbl = crequest('tbl');
	$col = crequest('col');
	
	$sql = "UPDATE " . PREFIX . "{$tbl} SET {$col} = '{$val}' WHERE id='{$id}'";
	if ($db->query($sql) !== false)
		echo '0';
	else 
		echo '2';		
}
