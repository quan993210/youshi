<?php
//抽奖类
class award
{
	public $start_time;			//开始时间
	public $end_time;			//结束时间
	public $rand_max;			//随机数的最大数
	public $percent;			//中奖概率,只能为0-rand_max之间的整数
	public $total_num;			//奖品总数
	public $today_num;			//每天可抽取的数量
	public $db;
	
	//参数初始化
	function __construct($para)
	{	
		$this->start_time = isset($para['start_time']) ? $para['start_time'] : date('Y-m-d');
		$this->end_time   = isset($para['end_time'])   ? $para['end_time']   : date('Y-m-d');
		$this->rand_max   = isset($para['rand_max'])   ? $para['rand_max']   : 100;
		$this->percent 	  = isset($para['percent'])    ? $para['percent']    : 0;
		$this->total_num  = isset($para['total_num'])  ? $para['total_num']  : 0;
		$this->today_num  = isset($para['today_num'])  ? $para['today_num']  : 0;
		
		global $db;
		$this->db = $db;
	}
	
	//抽奖
	function lottery()
	{
		if ($this->limit())return false;
		
		//获取随机数，判断是否中奖
		$award_num = rand(0, $this->rand_max);	
		echo $award_num;
		die;
		if ($award_num >= 0 && $award_num <= $this->percent)
			return true;
		else
			return false;
	}
	
	//抽奖条件判断
	function limit()
	{
		if ($this->limit_time())return true;
		//if ($this->limit_uid())return true;
		//if ($this->limit_toady())return true;
		//if ($this->limit_total())return true;
		//if ($this->forbidden_ip())return true;
		
		return false;
	}
	
	//抽奖时间判断
	function limit_time()
	{
		$today = date('Y-m-d');
		
		if (strtotime($today) < strtotime($this->start_time) || strtotime($today) > strtotime($this->end_time))
			return true;
		else
			return false;
	}
	
	//同用户不允许重复中奖
	function limit_uid()
	{
		$ip  = real_ip();
		$sql = "SELECT id FROM lottery WHERE ip = '{$ip}'"; 
		
		return $this->db->get_one($sql);
	}
	
	//当天抽奖数量是否已超出
	function limit_toady()
	{
		$today = date('Y-m-d');
		$sql   = "SELECT count(id) FROM lottery WHERE today = '{$today}' AND type = 1"; 
		$total = $this->db->get_one($sql);
		
		if ($total >= $this->today_num)
			return true;
		else
			return false;
	}
	
	//抽奖数量是否已超出总数
	function limit_total()
	{
		$sql   = "SELECT count(id) FROM lottery WHERE type = 1"; 
		$total = $this->db->get_one($sql);
		
		if ($total >= $this->total_num)
			return true;
		else
			return false;
	}
	
	//禁止抽奖的ip
	function forbidden_ip()
	{
		$ip = real_ip();
		$ip_arr = array('255.255.255.255');	
		
		if (in_array($ip, $ip_arr))
			return true;
		else
			return false;
	}
}

?>