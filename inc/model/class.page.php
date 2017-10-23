<?php
//分页类
class page
{
	 var $url			= '';			//url地址头
	 var $page_name		= 'page';		//page标签，用来控制url页。比如说xxx.php?PB_page=2中的PB_page	 
	 var $first			= '首页';		//首页
	 var $last			= '尾页';		//尾页
	 var $next			= '下一页';		//下一页
	 var $pre			= '上一页';		//上一页
	 var $pre_bar		= '<<';			//上一分页条
	 var $next_bar		= '>>';			//下一分页条	  
	 var $total			= 0;			//总记录数
	 var $page_size		= 1;			//每页记录数
	 var $bar_num		= 10;			//控制记录条的个数。
	 var $total_page	= 0;			//总页数
	 var $now_page		= 1;			//当前页 	 
	 var $next_page 	= 0;			//下一页
	 var $pre_page  	= 0;			//上一页
	 var $first_page 	= 0;			//首页
	 var $last_page		= 0;			//尾页
	 var $offset		= 0;
	 var $mark			= 'a';			//超链标签
	 var $select_id		= '';			//超链标签
	 
	 var $is_ajax		= false;		//是否支持AJAX分页模式	 
	 var $ajax_action	= '';			//AJAX动作名
 
	function __construct($param)
	{
		$this->page($param);
	}
		
	function page($param)
	{
		foreach ($param as $k => $v)
		{
			if ($k == 'url'   		|| 
				$k == 'page_name'   || 
				$k == 'next'   		|| 
				$k == 'pre'    		|| 
				$k == 'first'  		|| 
				$k == 'last'   		|| 
				$k == 'next_bar'    || 
				$k == 'pre_bar'     || 
				$k == 'mark'     	|| 
				$k == 'select_id'   || 
				$k == 'is_ajax' 	|| 
				$k == 'ajax_action')
				$this->$k = $v;
			else
				$this->$k = (int)$v;
		}
		
		$this->set_total_page();
		$this->set_now_page();
		$this->set_next_page();
		$this->set_pre_page();
		$this->set_url();
	}
	
	//设置总页数
	function set_total_page()
	{
		$this->total_page = ceil($this->total/$this->page_size);
		
	}
	
	//设置当前页
	function set_now_page()
	{
		$this->now_page = isset($_GET[$this->page_name]) ? $_GET[$this->page_name] : 1;
					
		if ($this->now_page < 1)
			$this->now_page = 1;
				
		if ($this->now_page > $this->total_page)
			$this->now_page = $this->total_page;	
	}
	
	//设置下一页
	function set_next_page()
	{
		if ($this->now_page < $this->total_page)
			$this->next_page = $this->now_page + 1;
		else
			$this->next_page = $this->total_page;
	}
	
	//设置上一页
	function set_pre_page()
	{
		if ($this->now_page > 1)
			$this->pre_page = $this->now_page - 1;
		else
			$this->pre_page = 1;
	}
	
	//首页样式
	function first_page($style = '')
	{
		$page_url = (1 == $this->now_page) ? 'javascript:void(0);' : $this->get_url(1);
		return $this->get_link($this->first, $page_url, $style);	
	}
	
	//尾页样式
	function last_page($style = '')
	{
		$page_url = ($this->total_page == $this->now_page) ? 'javascript:void(0);' : $this->get_url($this->total_page);
		return $this->get_link($this->last, $page_url, $style);
	}
	
	//下一页样式
	function next_page($style = '')
	{
		$page_url = ($this->total_page == $this->now_page) ? 'javascript:void(0);' : $this->get_url($this->next_page);
		return $this->get_link($this->next, $page_url, $style);
	}
	
	//上一页样式
	function pre_page($style = '')
	{
		$page_url = (1 == $this->now_page) ? 'javascript:void(0);' : $this->get_url($this->pre_page);
		return $this->get_link($this->pre, $page_url, $style);
	}
	
	//数字翻页列表
	function page_bar($style = '',$now_style = '')
	{
	  	$plus = ceil($this->bar_num/2);
		$tmp_var = $this->bar_num - $plus + $this->now_page;
		
	  	if($tmp_var > $this->total_page)
		{
			$plus = $this->bar_num - $this->total_page + $this->now_page;
		}
		
	  	$begin = $this->now_page - $plus + 1;
	  	$begin = ($begin >= 1) ? $begin : 1;
		$end = $begin + $this->bar_num;
	  	$return = '';
		
	  	for($i = $begin; $i < $end; $i++)
	  	{
	   		if($i <= $this->total_page)
			{
				$page_style = ($i == $this->now_page) ? $now_style : $style;	
				$page_url = ($i == $this->now_page) ? 'javascript:void(0);' : $this->get_url($i);
				$return .= $this->get_link($i, $page_url, $page_style);
	   		}
			else
			{
				break;
	   		}
	  	}
			
	  	unset($begin);
	  	return $return;
	}
	
	//获得链接样式
	function get_link($page, $url, $style = '')
	{
	  	switch ($this->mark)
		{
			case 'a':
						$return = $style == '' ? '<a href ="' . $url . '">' . $page . '</a> ' : '<a href ="' . $url . '" '. $style .'>' . $page . '</a> ';
						break;
			case 'span':
						$return = $style == '' ? '<span onclick ="location=\'' . $url . '\'">' . $page . '</span> ' : '<span onclick ="location=\'' . $url . '\'" class="'. $style .'">' . $page . '</span> ';
						break;
			default:
						$return = $style == '' ? '<a href ="' . $url . '">' . $page . '</a> ' : '<a href ="' . $url . '" class="'. $style .'">' . $page . '</a> ';
						break;	
		}
		
		return $return;
	}
	
	//获得链接URL
	function get_url($page)
	{
		if ($this->is_ajax)
		{
			$return = 'javascript:' . $this->ajax_action . '(' . $page . ')';
			return $return;
		}
		
		$return = $this->url;
		
		if(strpos($return, '?') === false)
		{
			$return .= '?' . $this->page_name . '=' . $page;
		}
		else
		{	
			$return .= '&' . $this->page_name . '=' . $page;
		}
		
		return $return;
	}
	
	//设置链接URL
	function set_url()
	{
  		if(empty($this->url))
		{
      		//设置当前页面url
   			$url = explode('?', $_SERVER['REQUEST_URI']);
			$this->url = $url[0]; 
  		}
		
      	//设置页面参数
   		if(!empty($_SERVER['QUERY_STRING']))
		{
       		$arr = explode('&', $_SERVER['QUERY_STRING']);
			$str = '';
			$num = count($arr);
			
			for ($i = 0; $i < $num; $i++)
			{
				if(strpos($arr[$i], $this->page_name . '=') === false)
					$str .= $str == '' ? $arr[$i] : '&' . $arr[$i];
			}
			
			if ($str != '')$this->url .= '?' . $str;
		}
	}
	
	//select样式翻页
	function select()
	{
		$url = $this->url;
		$url .= (strpos($url, '?') === false) ?  '?' : '&';
		$url .= $this->page_name . '=';
		
		if ($this->is_ajax)
			$action = $return = 'javascript:' . $this->ajax_action . '(this.value)';
		else
			$action = 'location.href=\'' . $url . '\'+this.value';
		
		$return = '<select id="' . $this->select_id . '" onchange="' . $action . '">';
		
		for ($i = 1; $i <= $this->total_page; $i++)
		{
			$is_select = ($i == $this->now_page) ? 'selected' : '';
			$return .= '<option value="' . $i. '" ' . $is_select . '>' . $i . '</option>';
		}
		
		$return .= '</select>';
		return $return;
	}
	
	function show($mode)
	{
		switch($mode)
		{
			case 1:
					$return = $this->first_page() . $this->pre_page() . $this->page_bar() . $this->next_page() . $this->last_page() . $this->select();
					break;
			case 2:
					$return = $this->first_page() . $this->pre_page() . $this->page_bar() . $this->next_page() . $this->last_page();
					break;
			case 3:
					$return = $this->pre_page('btn') . $this->page_bar('', 'class="current"') . $this->next_page('btn');
					break;
			case 6:
					$return = '总数：' . $this->total . '&nbsp;&nbsp;&nbsp;';
					$return .= '每页：' . $this->page_size . '&nbsp;&nbsp;&nbsp;';
					$return .= $this->total == 0 ? '&nbsp;&nbsp;&nbsp;' : $this->pre_page() . '&nbsp;' . $this->page_bar('', 'style="font-weight:bold;color:red;"') . '&nbsp;' . $this->next_page() . '&nbsp;&nbsp;&nbsp;';
					break;
			default:
					$return = $this->first_page() . $this->pre_page() . $this->page_bar() . $this->next_page() . $this->last_page() . $this->select();
					break;
		}
		
		return $return;
	}
}

?>