<?php
/**
*
* @author Tobi Sch�fer http://www.seo-phpbb.org/
*
* @package acp
* @version $Id: impressum.php V0.1.3 2007-08-30 23:54:18 tas2580 $
* @copyright (c) 2007 Star Trek Guide Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package module_install
*/
class acp_lexikon_info
{
	function module()
	{		
		return array(
			'filename'	=> 'acp_phpBBlog',
			'title'		=> 'PhpBBlog',
			'version'	=> '1.2.0',
			'modes'		=> array(
				'setup'	=> array(
											'title'		=> 'Generelle Optionen',
											'auth'		=> 'acl_a_board',
											'cat'		=> array('ACP_BOARD_CONFIGURATION'),
										),
				'select_cat'	=> array(
											'title'		=> 'Kategorie Übersicht',
											'auth'		=> 'acl_a_board',
											'cat'		=> array('ACP_BOARD_CONFIGURATION'),
										),
				'new_cat'	=> array(
											'title'		=> 'Neue Kategorien',
											'auth'		=> 'acl_a_board',
											'cat'		=> array('ACP_BOARD_CONFIGURATION'),
										),
				'new_entry'	=> array(
											'title'		=> 'Neuen Eintrag Erstellen',
											'auth'		=> 'acl_a_board',
											'cat'		=> array('ACP_BOARD_CONFIGURATION'),
										),
			),
		);
	}

	function install()
	{
		global $phpbb_root_path, $phpEx, $db, $user;
		
		// Setup $auth_admin class so we can add permission options
		include($phpbb_root_path . 'includes/acp/auth.' . $phpEx);
		$auth_admin = new auth_admin();
		
		// Add permission for manage cvsdb
		$auth_admin->acl_add_option(array(
			'local'		=> array(),
			'global'	=> array('a_add_board')
		));
		
		$module_data = $this->module();
		
		$module_basename = substr(strstr($module_data['filename'], '_'), 1);
		
		$result = $db->sql_query('SELECT module_id FROM ' . MODULES_TABLE . " WHERE module_basename = '$module_basename'");
		$module_id = $db->sql_fetchfield('module_id');
		$db->sql_freeresult($result);
		
		$sql = 'UPDATE ' . MODULES_TABLE . " SET module_auth = 'acl_a_add_board' WHERE module_id = $module_id";
		$db->sql_query($sql);
		
		set_config('add_user_version', $module_data['version'], false);
		
		trigger_error(sprintf($user->lang['ADD_USER_MOD_UPDATED'], $module_data['version']));
	}

	function uninstall()
	{
	}
	
	function update()
	{
	}
}

?>