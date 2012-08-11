<?php
/***************************************************************************
 *                             blog-class.php
 *                            -------------------
 *   begin                : Skippy, 27 August, 2006
 *   copyright            : (C) 2006 by Skippy
 *   email                : 
 *
 *   $Id: blog_class.php,v 1.2.0 2008/02/14 21:07:15 skippy Exp $
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

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

// Table names

define('BLOG_TABLE', $table_prefix.'blog');
define('BLOG_COM_TABLE', $table_prefix.'blog_com');
define('BLOG_CAT_TABLE', $table_prefix.'blog_cat');
define('BLOG_CONFIG_TABLE', $table_prefix.'blog_config');
define('BLOG_CONFIRM_TABLE', $table_prefix.'blog_confirm');
define('BLOG_TRACKBACKS_TABLE', $table_prefix.'blog_trackbacks');

// Version & other acp constants
define('BLOG_VERSION', '1.2.0.WIP2');
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
  
 /* 
 // Get portal config
function obtain_portal_config()
{
	global $db, $cache;

	if (($portal_config = $cache->get('portal_config')) !== true)
	{
		$portal_config = $cached_portal_config = array();

		$sql = 'SELECT config_name, config_value
			FROM ' . PORTAL_CONFIG_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$cached_portal_config[$row['config_name']] = $row['config_value'];
			$portal_config[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);

		$cache->put('portal_config', $cached_portal_config);
	}

	return $portal_config;
}*/ 
  
  
  
  function blog_config()
  {
     return $this->phpbblog_config;
  }

 /**
* Set config value. Creates missing config entry.
*/
  function update_blog($config_name, $config_value)
  {
    global $db;
    
    $sql = "UPDATE " . BLOG_CONFIG_TABLE . " SET
		    config_value = '" . $db->sql_escape($config_value) . "'
		    WHERE config_name = '" . $db->sql_escape($config_name) . "'";
	  $db->sql_query($sql);

	if (!$db->sql_affectedrows() && !isset($this->phpbblog_config[$config_name]))
	{
		$sql = 'INSERT INTO ' . BLOG_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'config_name'	=> $config_name,
			'config_value'	=> $config_value));
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update general configuration for phpbblog", "", __LINE__, __FILE__, $sql);
			}
	}
			
     $this->phpbblog_config[$config_name] = $config_value;
  }
 
 

 
 
 
}
?>
