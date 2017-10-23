<?php
//会员类
class cls_member 
{
	var $userid    		= '';
    var $password   	= '';
    var $truename    	= '';
    var $mobile	 		= '';
    var $email		    = '';
    var $address	    = '';
    var $zipcode	    = '';
    var $reg_ip		    = '';
    var $reg_time       = '';
    var $member			= array();
    
	function __construct($member)
    {
        $this->member = $member;
    }
    
    function reg($db)
    {
    	extract($this->member);
    	
    	$sql = "INSERT INTO ".PREFIX."member(userid, password, truename, mobile, email, address, zipcode, reg_ip, reg_time)".
    		   " VALUES('$userid', '$password', '$truename', '$mobile', '$email', '$address', '$zipcode', '$reg_ip', '$reg_time')";
    	
    	if ($db->query($sql))
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}	   	
    }
    
	function login($db)
    {
    	extract($this->member);
    	
    	$sql = "SELECT id FROM ".PREFIX."member WHERE userid='$userid' AND password='$password'";
    	$id = $db->get_one($sql);
    	
    	if ( $id != '' && $id !== false)
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}	   	
    }
    
	function mod_pwd($db)
    {
    	$sql = "UPDATE ".PREFIX."member ".
    	       " password = '$password' ".		
    		   " WHERE userid='$userid'";
    	
    	if ($db->query($sql))
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}	   	
    }
    
	function mod($db)
    {
    	$sql = "UPDATE ".PREFIX."member ".
    	       " truename = '$truename' ".
    		   " mobile   = '$mobile' ".
    		   " email    = '$email' ".
    		   " address  = '$address' ".			
    		   " WHERE userid='$userid'";
    	
    	if ($db->query($sql))
    	{
    		return true;
    	}
    	else 
    	{
    		return false;
    	}	   	
    }
    
	function get_pwd($db)
    {
    	$sql = "SELECT id FROM ".PREFIX."member WHERE userid='$userid' AND email='$email'";
    	$id = $db->get_one($sql);
    	
    	if ( $id!= '' && $id !== false)
    	{
    		send_mail();
    		return true;
    	}
    	else 
    	{
    		return false;
    	}	   	
    }
    
	function logout()
    {
    	unset($_SESSION['userid']);
    	return true;	   	
    }

}
  
?>
