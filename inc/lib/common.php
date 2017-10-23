<?php
//通用函数
if(!IN_APP) {
	exit('Access Denied');
}

/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = '')
{
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, CHAR_SET);
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length, CHAR_SET);
    }
    else
    {
        //$newstr = trim_right(substr($str, 0, $length));
        $newstr = substr($str, 0, $length);
    }

    if ($append && $str != $newstr)
    {
        $newstr .= $append;
    }

    return $newstr;
}

/**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function real_ip()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}

/**
 * 计算字符串的长度（汉字按照两个字符计算）
 *
 * @param   string      $str        字符串
 *
 * @return  int
 */
function str_len($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

    if ($length)
    {
        return strlen($str) - $length + intval($length / 3) * 2;
    }
    else
    {
        return strlen($str);
    }
}

/**
 * 获得用户操作系统的换行符
 *
 * @access  public
 * @return  string
 */
function get_crlf()
{
/* LF (Line Feed, 0x0A, \N) 和 CR(Carriage Return, 0x0D, \R) */
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win'))
    {
        $the_crlf = '\r\n';
    }
    elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac'))
    {
        $the_crlf = '\r'; // for old MAC OS
    }
    else
    {
        $the_crlf = '\n';
    }

    return $the_crlf;
}

/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder)
{
    $reval = false;

    if (!file_exists($folder))
    {
        /* 如果目录不存在则尝试创建该目录 */
        @umask(0);

        /* 将目录路径拆分成数组 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

        /* 如果第一个字符为/则当作物理路径处理 */
        $base = ($atmp[0][0] == '/') ? '/' : '';

        /* 遍历包含路径信息的数组 */
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;

                if ('..' == $val || '.' == $val)
                {
                    /* 如果目录为.或者..则直接补/继续下一个循环 */
                    $base .= '/';

                    continue;
                }
            }
            else
            {
                continue;
            }

            $base .= '/';

            if (!file_exists($base))
            {
                /* 尝试创建目录，如果创建失败则继续循环 */
                if (@mkdir(rtrim($base, '/'), 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        /* 路径已经存在。返回该路径是不是一个目录 */
        $reval = is_dir($folder);
    }

    clearstatcache();

    return $reval;
}

/**
 * 获得系统是否启用了 gzip
 *
 * @access  public
 *
 * @return  boolean
 */
function gzip_enabled()
{
    static $enabled_gzip = NULL;

    if ($enabled_gzip === NULL)
    {
        $enabled_gzip = ($GLOBALS['_CFG']['enable_gzip'] && function_exists('ob_gzhandler'));
    }

    return $enabled_gzip;
}

/**
 *  将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
 *
 * @access  public
 * @param   string       $str         待转换字串
 *
 * @return  string       $str         处理后字串
 */
function make_semiangle($str)
{
    $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                 '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
                 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
                 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
                 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
                 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
                 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
                 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
                 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
                 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
                 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
                 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
                 'ｙ' => 'y', 'ｚ' => 'z',
                 '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
                 '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
                 '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
                 '》' => '>',
                 '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
                 '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
                 '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
                 '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
                 '　' => ' ');

    return strtr($str, $arr);
}

/**
 * 获取服务器的ip
 *
 * @access      public
 *
 * @return string
 **/
function real_server_ip()
{
    static $serverip = NULL;

    if ($serverip !== NULL)
    {
        return $serverip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['SERVER_ADDR']))
        {
            $serverip = $_SERVER['SERVER_ADDR'];
        }
        else
        {
            $serverip = '0.0.0.0';
        }
    }
    else
    {
        $serverip = getenv('SERVER_ADDR');
    }

    return $serverip;
}

/**
 * 获取文件后缀名,并判断是否合法
 *
 * @param string $file_name
 * @param array $allow_type
 * @return blob
 */
function get_file_suffix($file_name, $allow_type = array())
{
    $file_suffix = strtolower(array_pop(explode('.', $file_name)));
    if (empty($allow_type))
    {
        return $file_suffix;
    }
    else
    {
        if (in_array($file_suffix, $allow_type))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
    {
        if (preg_match($chars, $user_email))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

/**
 * 检查是否为一个合法的时间格式
 *
 * @access  public
 * @param   string  $time
 * @return  void
 */
function is_time($time)
{
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

    return preg_match($pattern, $time);
}
/**
 * 防止sq注入
 *
 * @access  public
 * @param   string  $str
 * @return  string
 */
function crequest($str)
{
	$quest_str = isset($_REQUEST[$str]) ? $_REQUEST[$str] : '';
	
	if (is_array($quest_str))
		$quest_str = implode(',', $quest_str);		

	if (!get_magic_quotes_gpc())
		$quest_str = addslashes($quest_str);
		
	$quest_str = htmlspecialchars($quest_str);
		
	return $quest_str;
}
/**
 * 防止sq注入
 *
 * @access  public
 * @param   string  $str
 * @return  String or number
 */
function irequest($str)
{
	$quest_str = isset($_REQUEST[$str]) ? (int)$_REQUEST[$str] : 0;
	return $quest_str;	
}

/**
 * 会员中心判断是否登录
 *
 * @access  public
 * @param   string  $adminID 
 * @param   string  $sCode 
 * @return  boolean
 */
function check_member_login()
{
	if($_SESSION['member']['id'] == '')
	{
		//href_locate('../index.php');
		url_locate('/member/login.php', '请先登录');
		die;
	}
}

/**
 * 后台判断是否登录
 *
 * @access  public
 * @param   string  $adminID 
 * @param   string  $sCode 
 * @return  boolean
 */
function check_admin_login()
{
	if (strpos(CURRENT_PATH, 'index.php') !== false)return;
	
	if($_SESSION["admin"] == '')
	{
		href_locate('index.php');
		die;
	}	
}

/**
 * 检查不能为空的字符串
 *
 * @access  public
 * @param   string  $str , $msg 
 * @return  null
 */
function check_null($str,$msg)
{

	if (empty($str)){      

	     echo "<SCRIPT LANGUAGE='JavaScript'>";

	     echo "alert('".$msg."不能为空');";

	     echo "history.back();";

	     echo "</SCRIPT>";	

		 die;
	}	 
	return ;
}
/**
 * 检查密码格式是否正确
 *
 * @access  public
 * @param   string  $pwd,$pwd_confirm 
 * @return  null
 */
function check_pwd($pwd)
{
	if (!check_length($pwd, 6, 20))alert_back('密码长度必须为6-20'); //宽度检测
	if (!preg_match("/^[a-zA-Z0-9]*$/", $pwd))alert_back('密码必须为数字字母'); //特殊字符检测
	
	return true;
}

function check_length($str, $min = 0, $max = 100)
{
	$str = trim($str);
	if (strlen($str) < $min) return false;
	if (strlen($str) > $max) return false;
	return true;
}

/**
 * 检查两次密码是否一致
 *
 * @access  public
 * @param   string  $pwd,$pwd_confirm 
 * @return  null
 */
function check_pwd_same($pwd ,$pwd_confirm)
{

	if ($pwd!=$pwd_confirm)
	{        

	     echo "<SCRIPT LANGUAGE='JavaScript'>";

	     echo "alert('两次输入的密码不一致');";

	     echo "history.back();";

	     echo "</SCRIPT>";	

		 die;
	}	 
	return ;
}

/**
 * 检查是否邮箱
 *
 * @access  public
 * @param   string  $pwd,$pwd_confirm 
 * @return  null
 */
function check_email($email)
{

	if (!is_email($email))
	{        

	     echo "<SCRIPT LANGUAGE='JavaScript'>";

	     echo "alert('邮箱格式不正确');";

	     echo "history.back();";

	     echo "</SCRIPT>";	

		 die;
	}	 
	return ;
}

/**
 * 检查是否邮箱
 *
 * @access  public
 * @param   string  $pwd,$pwd_confirm 
 * @return  null
 */
function check_agree($is_agree)
{

	if (!$is_agree)
	{        

	     echo "<SCRIPT LANGUAGE='JavaScript'>";

	     echo "alert('请认真阅读并同意《精英点评网协议》');";

	     echo "history.back();";

	     echo "</SCRIPT>";	

		 die;
	}	 
	return ;
}
/**
 * 有提示页面连接跳转
 *
 * @access  public
 * @param   string  $url,$str 
 * @return  null
 */
function url_locate($url, $str){

	echo "<SCRIPT LANGUAGE='JavaScript'>";

	echo "alert('$str');";

	echo "location.href='$url'";

	echo "</SCRIPT>";	

	die ;
}
/**
 * 无提示页面连接跳转
 *
 * @access  public
 * @param   string  $url,$str 
 * @return  null
 */
function href_locate($url){

	echo "<SCRIPT LANGUAGE='JavaScript'>";

	echo "location.href='$url'";

	echo "</SCRIPT>";	

	die ;
}

/**
 * 提示并返回
 *
 * @access  public
 * @param   string   $msg 
 * @return  null
 */
function alert_back($msg){

	     echo "<SCRIPT LANGUAGE='JavaScript' charset='UTF-8'>";

	     echo "alert('".$msg."');";

	     echo "history.back();";

	     echo "</SCRIPT>";	

		 die;

		 return ;
}

/**
 * 获得某表某列的值，不存在返回空
 *
 * @access  public
 *
 * @return  string
 */
function get_one_value($tbl, $col, $con)
{
    if(empty($tbl))
	{
		echo "表不能为空";
		die;
	}
	if(empty($col))
	{
		echo "字段不能为空";
		die;
	}
	if(empty($con))
	{
		echo "条件不能为空";
		die;
	}
	$sql_get_one = "select ".$col." from ".$tbl." where ".$con;
	
	$dbQ_get_one=&dbConn();						  			  
	$dbQ_get_one->query($sql_get_one);
	if($dbQ_get_one->next_record())
	{
		$value = $dbQ_get_one->f($col);	
	}
	destroyDB($dbQ_get_one);
    return $value;
}
/**
 * 获得总记录数
 *
 * @access  public
 *
 * @return  string
 */
function get_total_count($tbl, $col, $con)
{
    if(empty($tbl))
	{
		echo "表不能为空";
		die;
	}
	if(empty($col))
	{
		echo "字段不能为空";
		die;
	}

	$sql_get_total = "select count(".$col.") as num from " . $tbl . $con;
	
	$dbQ_get_total=&dbConn();						  			  
	$dbQ_get_total->query($sql_get_total);
	if($dbQ_get_total->next_record())
	{
		$value = $dbQ_get_total->f('num');	
	}
	destroyDB($dbQ_get_total);
    return $value;
}

/**
 * 邮件发送
 *
 * @param: $email['name']        接收人姓名
 * @param: $email['email']       接收人邮件地址
 * @param: $email['subject']     邮件标题
 * @param: $email['content']     邮件内容
 *
 * @return boolean
 */
function send_mail($email)
{
	$mail = new PHPMailer(); 			// 建立邮件发送类
	$mail->IsSMTP(); 					// 使用SMTP方式发送
	$mail->Host 	= SMTP_HOST;		// 您的企业邮局域名
	$mail->SMTPAuth = true; 			// 启用SMTP验证功能
	$mail->Username = SMTP_ADDR; 		// 邮局用户名(请填写完整的email地址)
	$mail->Password = SMTP_PWD; 		// 邮局密码	
	$mail->From 	= SEND_MAIL; 		// 邮件发送者email地址
	$mail->FromName = SEND_NAME;		// 发件人
	
	//$name		= $email['name'];		// 发件人
	$address 	= $email['email'];		// 收件人地址
	$subject	= $email['subject'];	// 邮件标题
	$content	= $email['content'];	// 邮件内容
	
	if (is_array($address))
	{
		for ($i=0;$i<count($address);$i++)
		{
			$mail->AddAddress($address[$i], '');	// 格式是AddAddress("收件人email","收件人姓名")
		}
	}
	else
	{
		$mail->AddAddress($address, '');  			// 格式是AddAddress("收件人email","收件人姓名")
	}
	
	//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
	$mail->IsHTML(true); 							// 是否使用HTML格式
	
	$mail->Subject 	= $subject; 					// 邮件标题
	$mail->Body 	= $content; 					// 邮件内容
	
	if($mail->Send())
	{
		return true;
	}
	else
	{
		//return false;
		print_r($mail->ErrorInfo);
	}
	
}

/**
 * 防止sq注入
 *
 * @access  public
 * @param   string  $time
 * @return  string
 */
function safe_do($value)
{
	$value = strip_tags($value);
	$value = str_replace(' ', '', $value);
	
	if (!get_magic_quotes_gpc()) 
	{
		$value = addslashes($value);
	}
	
	return $value;
	
}

/**
 * 图片上传
 *
 * @access  public
 * @param   string  $file
 * @return  string
 */
function upload($img, $path, $para)
{
	$img = new cls_image($img);
	$img->images_dir = $path;
	$img_name = $img->upload();
	
	if ($img_name === false)die($img->error_msg());
	
	//生存缩略图
	if (is_array($para) && $para[0] > 0)$img->make_thumb($para[0], $para[1]);
	
	return $img_name;
}

/**
 * 当前时间
 *
 * @access  public
 * @return  datetime
 */
function now_time()
{
	return date('Y-m-d H:i:s');
}

if (!function_exists('file_get_contents'))
{
    /**
     * 如果系统不存在file_get_contents函数则声明该函数
     *
     * @access  public
     * @param   string  $file
     * @return  mix
     */
    function file_get_contents($file)
    {
        if (($fp = @fopen($file, 'rb')) === false)
        {
            return false;
        }
        else
        {
            $fsize = @filesize($file);
            if ($fsize)
            {
                $contents = fread($fp, $fsize);
            }
            else
            {
                $contents = '';
            }
            fclose($fp);

            return $contents;
        }
    }
}

if (!function_exists('file_put_contents'))
{
    define('FILE_APPEND', 'FILE_APPEND');

    /**
     * 如果系统不存在file_put_contents函数则声明该函数
     *
     * @access  public
     * @param   string  $file
     * @param   mix     $data
     * @return  int
     */
    function file_put_contents($file, $data, $flags = '')
    {
        $contents = (is_array($data)) ? implode('', $data) : $data;

        if ($flags == 'FILE_APPEND')
        {
            $mode = 'ab+';
        }
        else
        {
            $mode = 'wb';
        }

        if (($fp = @fopen($file, $mode)) === false)
        {
            return false;
        }
        else
        {
            $bytes = fwrite($fp, $contents);
            fclose($fp);

            return $bytes;
        }
    }
}

 function gd_version()
{
	$gd = cls_image::gd_version();
	if ($gd == 0)
	{
		$gd_info = 'N/A';
	}
	else
	{
		if ($gd == 1)
		{
			$gd_info = 'GD1';
		}
		else
		{
			$gd_info = 'GD2';
		}
		
		$gd_info .= ' (';
		
		/* 检查系统支持的图片类型 */
		if ($gd && (imagetypes() & IMG_JPG) > 0)
		{
			$gd_info .= ' JPEG';
		}
		
		if ($gd && (imagetypes() & IMG_GIF) > 0)
		{
			$gd_info .= ' GIF';
		}
		
		if ($gd && (imagetypes() & IMG_PNG) > 0)
		{
			$gd_info .= ' PNG';
		}
		
		$gd_info .= ')';
	}
	
	return $gd_info;
}

/*
 *$type 代表栏目，分别对应左边的菜单栏，如category代表分类管理
 *$status代表某个具体操作，对应到函数中
 *
*/
function operate_log($aid, $type, $status, $text = '')
{
	global $db;
	
	//$aid	  = $_SESSION['admin_id'];
	$ip 	  = real_ip();
	$today    = date('Ymd');
	$now_time = now_time();
	
	$sql = "INSERT INTO operate_log(aid, type, status, content, ip, today, add_time) VALUES ('{$aid}', '{$type}', '{$status}', '{$text}', '{$ip}', '{$today}', '{$now_time}')";
	$db->query($sql);
	
	return;
}



