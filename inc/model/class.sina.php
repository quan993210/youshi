<?php
//新浪开发接口相关操作
class cls_sina 
{
    protected $_client;
	
	protected $_db;
	
	protected $_last_key;
	
	function __construct()
    {
		include_once('sina/config.php' );
		include_once( 'sina/saetv2.ex.class.php' );		
		
		if (!empty($_SESSION['wbToken']['access_token']))
		{	
			//setcookie('weibojs_' . $weibo->client_id, http_build_query($token));
			$this->_client = new SaeTClientV2(WB_AKEY, WB_SKEY, $_SESSION['wbToken']['access_token']);
		}	
		
		//初始化数据库对象
		global $db;
        $this->_db = $db;	
		
    }
	
	//登录
    function login()
    {
		//初始化微博接口
		$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['wbToken']['access_token'] );
		$ms  = $c->home_timeline(); // done
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$me = $c->show_user_by_id($uid);					//根据ID获取用户等基本信息
		
		$nick_name 			= $me['name'];					//新浪微博昵称
		//$user_id 			= $me['idstr'];					//新浪微博用户id	
		$avatar 			= $me['profile_image_url'];		//新浪微博头像
		$avatar_arr 		= explode('/', $avatar);
		$user_id 			= $avatar_arr[3];
		$followers_count 	= $me['followers_count'];		//粉丝数
		$friends_count 		= $me['friends_count'];			//关注数
		$bi_followers_count = $me['bi_followers_count'];	//互粉数
		$location		 	= $me['location'];				//位置
		$now_time 			= date('Y-m-d H:i:s');			//当前时间
		$ip 				= real_ip();					//用户ip
		$user_type			= 1;		
		
		if (empty($nick_name))
		{
			echo '登录失败<br/>';
			print_r($me);die;
		}
		
		$sql = "SELECT id, nick_name FROM member WHERE user_id='{$user_id}' AND user_type='{$user_type}'";
		$row = $this->_db->get_row($sql);
		//print_r($row);die;
		if ($row['id'] == '')
		{
			$sql = "INSERT INTO member(user_id, nick_name, avatar, reg_ip, reg_time, last_ip, last_time, followers_count, friends_count, user_type, location) VALUES ('{$user_id}', '{$nick_name}', '{$avatar}', '{$ip}', '{$now_time}', '{$ip}', '{$now_time}', {$followers_count}, {$friends_count}, '{$user_type}', '{$location}')";	
			//echo $sql;die;
			$this->_db->query($sql);
			$uid = $this->_db->insert_id();
		}
		else
		{
			$uid = $row['id'];
			$sql = "UPDATE member SET last_time = '{$now_time}', reg_ip= '{$ip}', followers_count= '{$followers_count}', friends_count= '{$friends_count}' WHERE id = '{$uid}'";
			
			$this->_db->query($sql);
		}
			
		$_SESSION['uid']       = $uid;
		$_SESSION['user_id']   = $user_id;
		$_SESSION['nick_name'] = $nick_name;
		$_SESSION['avatar']    = $avatar;
		$_SESSION['user_type'] = $user_type;
		
		return $me;   	
    }
	
	//好友总数
	function friends_count()
    {
/*		$user_id = $_SESSION['user_id'];
		$me = $this->_client->show_user($user_id); 
		$friends_count = $me['friends_count'];*/
		$uid = $_SESSION['uid'];
		$sql = "SELECT friends_count FROM member WHERE id = '{$uid}'";
		
		return $this->_db->get_one($sql);
	}
	
	//好友列表
	function friends($cursor, $count)
    {
		$user_id = $_SESSION['user_id'];
		return $this->_client->friends_by_id($user_id, $cursor, $count);
	}
	
	//关注
	function follow($user_id)
    {
		return $this->_client->follow_by_id($user_id);
	}
	
	//发微博
	function pub_statuses($text, $img_url)
    {
		if ($img_url == '')
			return $this->_client->update($text);
		else
			return $this->_client->upload($text, $img_url);
	}
	
	//转发微博
	function repost($sid)
    {
		return $this->_client->repost( $sid , NULL, 0);
	}
	
	//是否关注
	function is_followed($target_id)
    {
		return $this->_client->is_followed_by_id($target_id);
	}
	
	//批量获取指定微博的转发数评论数
	function status_count($ids)
    {
		return $this->_client->status_count($ids);
	}
	
	//用户信息
	function show($uid)
    {
		return $this->_client->show_user_by_id( $uid );
	}
}
  
?>
