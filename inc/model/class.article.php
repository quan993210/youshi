<?php
//文章类
class article 
{
	public $title    	= '';
    public $content   	= '';
    public $cat_id    	= '';
    
	function __construct($param)
    {
        $this->article($param);
    }
    
    function article($param)
    {
		global $db;
		$this->db = $db;
    }
    
	function add()
    {
    	$article  = 	$_POST;
		$cat_id   = $article['cat_id'];
		$title    = $article['title'];
		$content  = $article['content'];
		$add_time = now_time();
		
		$sql = "INSERT INTO " . PREFIX . "article (cat_id, title, content, add_time) VALUES ('{$cat_id}', '{$title}', '{$content}', '{$add_time}')";
		return $this->db->insert($sql);
    }
    
	function mod($db)
    {
		$article = 	$_POST;
		$id  	 = $article['id'];
		$cat_id  = $article['cat_id'];
		$title   = $article['title'];
		$content = $article['content'];
		
    	$sql = "UPDATE " . PREFIX . "article SET ".
    	       "cat_id = '{$cat_id}' ".
			   "title = '{$title}' ".
			   "content = '{$content}' ".
    		   "WHERE id = '{$id}'";
    	
    	return $db->query($sql);	   	
    }
    
	function del($db)
    {
		$id  = $_GET[['id'];
    	$sql = "DELETE FROM " . PREFIX . "article WHERE id='{$id'}";
    	
    	return $db->query($sql);	   	
    }
    
	function detail($db)
    {
		$id  = $_GET[['id'];
    	$sql = "SELECT * FROM " . PREFIX . "article WHERE id='{$id'}";
		
    	return $db->get_row($sql);   	
    }

}
  
?>
