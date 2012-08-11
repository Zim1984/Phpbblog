<?php
/***************************************************************************
 *                             blog-class.php
 *                            -------------------
 *   begin                : Skippy, 27 August, 2006
 *   copyright            : (C) 2006 by Skippy
 *   email                : 
 *
 *   $Id: blog_class.php,v 1.0.9 2006/08/30 20:21:15 nardi Exp $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/



// Table names

define('BLOG_TABLE', $table_prefix.'blog');
define('BLOG_COM_TABLE', $table_prefix.'blog_com');
define('BLOG_CAT_TABLE', $table_prefix.'blog_cat');
define('BLOG_CONFIG_TABLE', $table_prefix.'blog_config');

// Version & other acp constants
define('BLOG_VERSION', '1.2.0.WIP');
define('BLOG_INSTALLED', true);
define ('PHPBBLOG_AUTHOR', 'phpBBlog');
define ('PHPBBLOG_SITE_AUTHOR', 'http://www.tochange.it/');
define ('PHPBBLOG_CHECK_NEW_VERSION', 'http://skippys.sk.ohost.de/blog.php');




class phpbblog
{
    var $phpbblog_config = array();

  function phpbblog()
  {
    global $db;
    
    $sql = "SELECT * FROM " . BLOG_CONFIG_TABLE;
    if( !($result = $db->sql_query($sql)) )
      {
	trigger_error("Could not query config information from phbblog");
	    //message_die(CRITICAL_ERROR, "Could not query config information from phbblog", "", __LINE__, __FILE__, $sql);
      }

     while ( $row = $db->sql_fetchrow($result) )
      {
        $this->phpbblog_config[$row['config_name']] = $row['config_value'];
      }
      
      $db->sql_freeresult($result);
  }
  
  function blog_config()
  {
     return $this->phpbblog_config;
  }

  function update_blog($config_name, $config_value)
  {
    global $db;
    
    $sql = "UPDATE " . BLOG_CONFIG_TABLE . " SET
		    config_value = '" . str_replace("\'", "''", $config_value) . "'
		    WHERE config_name = '$config_name'";
			
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update general configuration for phpbblog", "", __LINE__, __FILE__, $sql);
			}

     $this->phpbblog_config[$config_name] = $config_value;
  }
 
}
?>
