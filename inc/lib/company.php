<?php
//通用函数
if(!IN_APP) {
	exit('Access Denied');
}	

/*------------------------------------------------------ */
//-- 分页
/*------------------------------------------------------ */	
function pages($para)
{
	global $db;
	
	if ($para['my'] == 1)
	{
		$cat 	   = $para['cat'];
		$con	   = $cat > 0 ? "WHERE question_cat = '{$cat}'" : '';
		
		$uid       = (int)$para['uid'];
		if ($uid > 0)
		{
			$con	   .= $con == '' ? "WHERE uid = '{$uid}'" : " AND uid = '{$uid}'";
		}
		else
		{
			$active    = $para['active'];
			$con	   .= $con == '' ? "WHERE active = '{$active}'" : " AND active = '{$active}'";
		}
	}
	
	if ($para['my'] == 2 || $para['my'] == 3)
	{
		$ids = $para['ids'];
		$con = $ids != '' ? "WHERE id in ({$ids})" : "WHERE id = '-1'";
	}
	
	$page_size = $para['page_size'];
	
	$sql 	= "SELECT COUNT(id) FROM question {$con}";
	$total 	= $db->get_one($sql);
	$para   = array('total'=>$total, 'page_size'=>$page_size, 'is_ajax' => 1, 'ajax_action' => 'goto_page', 'page' => $para['page']);
	$page   = new page($para);
	
	return $page->show(3);
}

function time_format($add_time)
{
	$year = substr($add_time, 5, 5);
	$year = str_replace('-', '月', $year) . '日';
	$time = substr($add_time, 11, 5);
	$add_time = $year . '，' . $time;
	
	return $add_time;
}

/*------------------------------------------------------ */
//-- 删除图片
/*------------------------------------------------------ */	
function del_img($img)
{
	$img =  $_SERVER['DOCUMENT_ROOT'] . $img;
	if (file_exists($img))
	{
		@unlink($img);
	}
		
}

/*------------------------------------------------------ */
//-- 获得广告
/*------------------------------------------------------ */	
function get_ads($pos, $num = 5)
{
	global $db;
	
	$limit = $num > 0 ? "LIMIT $num" : '';
	$sql = "SELECT * FROM ads WHERE cid = '{$pos}' ORDER BY order_num DESC {$limit}";
	$res = $db->get_all($sql);
	/*$num  = count($res);
	
	for ($i = 0; $i < $num; $i++)
	{
		$pic = $res[$i]['pic'];
		$pic = $pic == '' ? '' : URL_PATH . $pic;
		$res[$i]['pic'] = $pic;
	}*/
	
	return $res;
}

/*------------------------------------------------------ */
//-- 自动登录
/*------------------------------------------------------ */	
function auto_login()
{
	global $db;
	
	if (!empty($_SESSION['member']['id']))return;
	
	$userid = $_COOKIE['lexue_login_userid'];
	if (empty($userid))return;
	
	$sql = "SELECT id, userid, password, email, user_type FROM member WHERE userid = '{$userid}'";
	$row = $db->get_row($sql);
	
	$password = $_COOKIE['lexue_login_password'];
	$ck = '2sdf23s';
	
	if (substr(md5($userid. $row['password']. $ck), 3, 20) == $password)
	{
		unset($row['password']);
		$_SESSION['member'] = $row;
		more_login_info();
	}
	else
	{
		setcookie ('lexue_login_userid', '', time() - 3600);
		setcookie ('lexue_login_password', '', time() - 3600);
	}
	
	return;
}

/*------------------------------------------------------ */
//-- 登录后获取更多信息
/*------------------------------------------------------ */	
function more_login_info()
{
		
	global $db;
	
	return;
}

/*------------------------------------------------------ */
//-- 获得文章列表
/*------------------------------------------------------ */	
function get_article($cid = 0, $page = 1, $page_size = 20, $sort = 0)
{
	global $db;
	
	$sort = $sort == 0 ? 'ORDER BY add_time DESC' : 'ORDER BY id';
	
	$con =(strpos($cid, ',') !== false) ? "WHERE cid in ({$cid})" : "WHERE cid = '{$cid}'";
	$start  = ($page - 1) * $page_size;	
	
	$sql = "SELECT * FROM article {$con} {$sort} LIMIT {$start}, {$page_size}";
	$res = $db->get_all($sql);
	/*$num  = count($res);
	
	for ($i = 0; $i < $num; $i++)
	{
		$pic = $res[$i]['pic'];
		$pic = $pic == '' ? '' : URL_PATH . $pic;
		$res[$i]['pic'] = $pic;
		
		
	}*/
	
	return $res;
}

/*------------------------------------------------------ */
//-- 获得文章详细
/*------------------------------------------------------ */	
function get_article_detail()
{
	global $db;
	
	$id  = irequest('id');	
	$sql = "SELECT * FROM article WHERE id = '{$id}'";
	$res = $db->get_row($sql);
	
	if (empty($res['id']))
	{
		$result = array('status' => 0, 'msg' => '文章不存在', 'data' => array());
		echo json_encode($result);
		die;		
	}
	
	/*$content = $res['content'];
	$content = $res['cid'] == 12? strip_tags($content): str_replace('/web/tongrentang/upload/editor/', URL_PATH . '/upload/editor/', $content);
	$res['content'] = $content;
	
	$res['pic'] = $res['pic'] == '' ? '' : URL_PATH . $res['pic'];*/
	
	return $res;
}

/*------------------------------------------------------ */
//-- 获得子分类
/*------------------------------------------------------ */	
function get_sub_cid($cid, $cid_str)
{	
	global $db;
	
	$sql = "SELECT id FROM article_category WHERE parent_id = {$cid}";
	$res = $db->get_all($sql);
	$num = count($res);

	if ($num < 1)return $cid_str;
	
	for ($i = 0; $i < $num; $i++)
	{
		$cid_str .= ',' . $res[$i]['id'];
		$cid_str = get_sub_cid($res[$i]['id'], $cid_str);
	} 

	return $cid_str;
}

/*------------------------------------------------------ */
//-- 获得产品分类
/*------------------------------------------------------ */	
function get_product_category()
{
	global $db;
	
	$sql = "SELECT * FROM product_category WHERE parent_id = 0 ORDER BY order_num";
	$res = $db->get_all($sql);
	$num = count($res);
	
	for ($i = 0; $i < $num; $i++)
	{
		$pid = $res[$i]['id'];
		$sql = "SELECT * FROM product_category WHERE parent_id = '{$pid}' ORDER BY order_num";
		$res[$i]['sub'] = $db->get_all($sql);
	}
	
	return $res;
}

/*------------------------------------------------------ */
//-- 获得产品分类信息
/*------------------------------------------------------ */	
function get_product_cat_info($id)
{
	global $db;
	
	$sql = "SELECT * FROM product_category WHERE id = '{$id}'";
	$res = $db->get_row($sql);
	
	return $res;
}

/*------------------------------------------------------ */
//-- 获得案例列表
/*------------------------------------------------------ */	
function get_case($page = 1, $page_size = 4, $is_index = 0)
{
	global $db;
	
	//$con   = 'WHERE is_pub = 1';
	$con   = $is_index != 0 ? ' WHERE is_index = 1 ' : '';
	$start = ($page - 1) * $page_size;	
	
	$sql = "SELECT * FROM product {$con} ORDER BY order_num DESC, add_time DESC LIMIT {$start}, {$page_size}";
	$res = $db->get_all($sql);
	/*$num  = count($res);
	
	for ($i = 0; $i < $num; $i++)
	{
		$pic = $res[$i]['pic'];
		$pic = $pic == '' ? '' : URL_PATH . $pic;
		$res[$i]['pic'] = $pic;	
	}*/
	
	return $res;
}

/*------------------------------------------------------ */
//-- 获得信息
/*------------------------------------------------------ */	
function get_info($cat)
{
		
	global $db;
	
	$sql = "SELECT * FROM info WHERE cat = '{$cat}'";
	$res = $db->get_row($sql);
	
	return $res;
}

/*------------------------------------------------------ */
//-- 批量上传相册图片
/*------------------------------------------------------ */	
function upload_batch_photo()
{
	$upload_name = crequest('upload_name');
	$dir_type    = crequest('dir_type');
	$targetDir   = $_SERVER['DOCUMENT_ROOT'] . '/ChildrenDay/upload/' . $dir_type . '/' . date('ym') . '/';
	
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds
	
	// Create target dir
	if (!file_exists($targetDir)) {
		make_dir($targetDir);
	}
		
	$pos = strrpos($_FILES[$upload_name]["name"], '.');
	if ($pos !== false)
	{
		$file_type = substr($_FILES[$upload_name]["name"], $pos);
	}
	
	$fileName = date('YmdHis') . rand(1000, 9999) . $file_type;		
	$filePath = $targetDir . $fileName;
	$pic_path = '/ChildrenDay/upload/' . $dir_type . '/' . date('ym') . '/' . $fileName;
	
	// Chunking might be enabled
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
	
	
	// Remove old temp files	
	if ($cleanupTargetDir) {
		if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		}
	
		while (($file = readdir($dir)) !== false) {
			$tmpfilePath = $targetDir . $file;
	
			// If temp file is current file proceed to the next
			if ($tmpfilePath == "{$filePath}.part") {
				continue;
			}
	
			// Remove temp file if it is older than the max age and is not the current file
			if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
				@unlink($tmpfilePath);
			}
		}
		closedir($dir);
	}	
	
	
	// Open temp file
	if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}
	
	if (!empty($_FILES)) {
		if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		}
	
		// Read binary input stream and append it to temp file
		if (!$in = @fopen($_FILES[$upload_name]["tmp_name"], "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	} else {	
		if (!$in = @fopen("php://input", "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	}
	
	while ($buff = fread($in, 4096)) {
		fwrite($out, $buff);
	}
	
	@fclose($out);
	@fclose($in);
	
	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off 
		rename("{$filePath}.part", $filePath);
	}
	
	// Return Success JSON-RPC response
	//die('{"jsonrpc" : "2.0", "result" : null, "pic_path" : "' . '/upload/photo/' . date('ym') . '/' . $fileName . '"}');
	$session_name = $dir_type . '_' . $upload_name . '_img';
	$_SESSION[$session_name] = $pic_path;
	$res = array("jsonrpc" => "2.0", "result" => "", "pic_path" => $pic_path); 
	echo json_encode($res);
	die;
}

$smarty->assign('url_path', URL_PATH);
$smarty->assign('admin_temp_path', ADMIN_TEMP_PATH);


$link = get_ads(30, 0);
$smarty->assign('link', $link);

//自动登录
//auto_login();
//print_r($_SESSION['member']);
//$smarty->assign('member', $_SESSION['member']);

/**
 * 输出成功
 * @param $data
 * @param $msg
 */
function showapisuccess($data = array(),$msg = 'success') {

	if (!is_array($data)){
		$data = array();
	}
	$error = array(
			'errcode' => '0',
			'errmsg' => $msg,
			'data' => $data
	);
	$json = json_encode($error);
	echo $json;
	exit;
}

/**
 * 输出错误
 * @param string $msg 提示信息
 * @param string $errcode
 */
function showapierror($msg = 'error',$errcode = -1) {
	$error = array(
			'errcode' => $errcode,
			'errmsg' => $msg
	);
	$json = json_encode($error);
	echo $json;
	exit;
}




