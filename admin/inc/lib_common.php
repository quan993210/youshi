<?php
    //设置排序
    function set_order($sort_col, $asc_or_desc, $smarty)
    {
	    $order = '';
	    
	    if ($sort_col == '' or $asc_or_desc == '')
		{
			$order = "order by id desc";
		}
		else
		{
			$order = "order by $sort_col $asc_or_desc";
		}
		
		if ($asc_or_desc == 'asc')
		{
			$smarty->assign('asc_or_desc','desc');
		}
		else
		{
			$smarty->assign('asc_or_desc','asc');
		}	
		
		return $order;
    }
    
    //设置搜索
    function set_con($search_cat, $keyword, $smarty)
    {
	    $con = '';
	    
    	if ($search_cat == 0)
		{
			$keyword = '';
		}
		else
		{
			check_null($keyword,'查询关键字');
			switch($search_cat)
			{
				case 1:
						$con = " where userid='$keyword' ";
						break;
				case 2:
						$con = " where truename='$keyword' ";
						break;
				case 3:
						$con = " where mobile='$keyword' ";
						break;
				case 4:
						$con = " where email='$keyword' ";
						break;						
			}
		
		}

		$smarty->assign('search_cat',$search_cat);
		$smarty->assign('keyword',$keyword);
    	
		return $con;
    }
?>