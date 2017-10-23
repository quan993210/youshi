<?php
//mysqli数据库操作类
class cls_mysql 
{
	var $link_id    	= NULL;
    var $settings   	= array();
    var $version    	= '';
    var $queryCount 	= 0;
    var $error_message  = array();
    var $starttime      = 0;
    var $total_rows	    = 0;
    
	function __construct($dbhost, $dbuser, $dbpw, $dbname = '', $charset = '')
    {
        $this->cls_mysql($dbhost, $dbuser, $dbpw, $dbname, $charset);
    }
    
	function cls_mysql($dbhost, $dbuser, $dbpw, $dbname = '', $charset = '')
    {
        if (defined('CHAR_SET'))
        {
            $charset = strtolower(str_replace('-', '', CHAR_SET));
        }
		else
		{
			$charset = 'utf8';
		}

        $this->settings = array(
                                    'dbhost'   => $dbhost,
                                    'dbuser'   => $dbuser,
                                    'dbpw'     => $dbpw,
                                    'dbname'   => $dbname,
                                    'charset'  => $charset
                               );
		
		$this->connect($dbhost, $dbuser, $dbpw, $dbname, $charset);
    }

    function connect($dbhost, $dbuser, $dbpw, $dbname = '', $charset = '')
    {
        $this->link_id = mysqli_connect($dbhost, $dbuser, $dbpw, $dbname);
		
		/* check connection */
		if (mysqli_connect_errno()) 
		{
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		
		/* set charset */
		mysqli_query($this->link_id, "SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary");
			
        return true;
    }

    function query($sql)
    {
        if ($this->link_id === NULL)
        {
            $this->connect($this->settings['dbhost'], $this->settings['dbuser'], $this->settings['dbpw'], $this->settings['dbname'], $this->settings['charset']);
            $this->settings = array();
        }
		
        return mysqli_query($this->link_id, $sql, MYSQLI_USE_RESULT);
    }

    function close()
    {
        return mysqli_close($this->link_id);
    }
    
	function get_one($sql, $limited = false)
    {
    	if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
		
        if ($res !== false)
        {
            $row = mysqli_fetch_row($res);

            if ($row !== false)
            {
                return $row[0];
            }
            else
            {
                return '';
            }
        }
        else
        {
            return false;
        }
    }
    
	function get_all($sql)
    {
        $res = $this->query($sql);
		
        if ($res !== false)
        {
            $arr = array();
            while ($row = @mysqli_fetch_assoc($res))
            {
                $arr[] = $row;
            }
            return $arr;
        }
        else
        {
            return false;
        }
    }
    
	function get_row($sql, $limited = false)
    {
        if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
		
        if ($res !== false)
        {
            return mysqli_fetch_assoc($res);
        }
        else
        {
            return false;
        }
    }
    
	function get_col($sql)
    {
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysqli_fetch_row($res))
            {
                $arr[] = $row[0];
            }

            return $arr;
        }
        else
        {
            return false;
        }
    }
    function version()
    {
        return $this->version;
    }
}
  
?>
