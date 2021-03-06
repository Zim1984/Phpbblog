<?php
/** 
*
* \install_phpBBlog\install.php
*
* @package
* @version $Id: install.php 1 2007-07-30 13:25:14Z stoffel04 $
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!$user->data['is_registered'])
{
    if ($user->data['is_bot'])
    {
        redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
    }
    login_box('', $user->lang['LOGIN_INFO']);
}
else if ($user->data['user_type'] != USER_FOUNDER)
{
	$message = "<span style=\"color:red;\">You must be logged in as a founder to run this script.</span><br />
				<span style=\"color:red;\">Du musst Gründer Rechte besitzen um dieses Script ausführen zu können.</span><br />
				";
	trigger_error($message);
}

$submit = request_var('install', '');

/**
* split_sql_file will split an uploaded sql file into single sql statements.
* Note: expects trim() to have already been run on $sql.
*/
function split_sql_file($sql, $delimiter)
{
	$sql = str_replace("\r" , '', $sql);
	$data = preg_split('/' . preg_quote($delimiter, '/') . '$/m', $sql);

	$data = array_map('trim', $data);

	// The empty case
	$end_data = end($data);

	if (empty($end_data))
	{
		unset($data[key($data)]);
	}

	return $data;
}

if ($submit == 'continue') 
{
	// What sql_layer should we use?
	switch ($db->sql_layer)
	{
		case 'mysql':
			$db_schema = 'mysql_40';
			$delimiter = ';';
		break;

		case 'mysql4':
			if (version_compare($db->mysql_version, '4.1.3', '>='))
			{
				$db_schema = 'mysql_41';
			}
			else
			{
				$db_schema = 'mysql_40';
			}
			$delimiter = ';';
		break;

		case 'mysqli':
			$db_schema = 'mysql_41';
			$delimiter = ';';
		break;
/*
		case 'mssql':
			$db_schema = 'mssql';
			$delimiter = 'GO';
		break;
		
		case 'postgres':
			$db_schema = 'postgres';
			$delimiter = ';';
		break;
		
		case 'sqlite':
			$db_schema = 'sqlite';
			$delimiter = ';';
		break;
		
		case 'firebird':
			$db_schema = 'firebird';
			$delimiter = ';;';
		break;
		
		case 'oracle':
			$db_schema = 'oracle';
			$delimiter = '/';
		break;*/

		default:
			trigger_error('Sorry, unsupportet Databases found.');
		break;
	}
	
	// Drop the bug_status  table if existing
	$sql = 'DROP TABLE IF EXISTS '.$table_prefix.'blog';
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);

	// Drop the bug_status  table if existing
	$sql = 'DROP TABLE IF EXISTS '.$table_prefix.'blog_trackbacks';
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);

	// Drop the bug_categories table if existing
	$sql = 'DROP TABLE IF EXISTS '.$table_prefix.'blog_com';
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);
	
	// Drop the bug_reports table if existing
	$sql = 'DROP TABLE IF EXISTS '.$table_prefix.'blog_cat';
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);

	// Drop the bug_posts table if existing
	$sql = 'DROP TABLE IF EXISTS '.$table_prefix.'blog_config';
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);
	
	
	
	// locate the schema files
	$dbms_schema = 'schemas/_' . $db_schema . '_schema.sql';
	
	// Get the schema file
	$sql_query = @file_get_contents($dbms_schema);
	
	// Replace the default prefix phpbb_ to the actual used prefix
	$sql_query = preg_replace('#phpbb_#i', $table_prefix, $sql_query);
	
	// Remove all remarks ( # )
	$sql_query = preg_replace('/\n{2,}/', "\n", preg_replace('/^#.*$/m', "\n", $sql_query));
	
	// Splitt all SQL Statements into an array
	$sql_query = split_sql_file($sql_query, $delimiter);

	// Create all needed tables now
	foreach ($sql_query as $sql)
	{
		//$sql = trim(str_replace('|', ';', $sql));
		if (!$db->sql_query($sql))
		{
			$error = $db->sql_error();
			$this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
		}
	}
	unset($sql_query);

	// Ok tables have been built, let's fill in the basic information
	$sql_query = file_get_contents('schemas/_schema_data.sql');
	
	// Deal with any special comments
	switch ($db->sql_layer)
	{
		case 'mssql':
		case 'mssql_odbc':
			$sql_query = preg_replace('#\# MSSQL IDENTITY (phpbb_[a-z_]+) (ON|OFF) \##s', 'SET IDENTITY_INSERT \1 \2;', $sql_query);
		break;

		case 'postgres':
			$sql_query = preg_replace('#\# POSTGRES (BEGIN|COMMIT) \##s', '\1; ', $sql_query);
		break;
	}
	
	// Change prefix
	$sql_query = preg_replace('#phpbb_#i', $table_prefix, $sql_query);

	// Remove all remarks ( # )
	$sql_query = preg_replace('/\n{2,}/', "\n", preg_replace('/^#.*$/m', "\n", $sql_query));
	
	// Splitt all SQL Statements into an array
	$sql_query = split_sql_file($sql_query, ';');
	
	// Tadaa! Fill all data in ;-)
	foreach ($sql_query as $sql)
	{
		//$sql = trim(str_replace('|', ';', $sql));
		if (!$db->sql_query($sql))
		{
			$error = $db->sql_error();
			$this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
		}
	}
	unset($sql_query);
	
	//Destroy the old permission chache again to enable the new set :-)
	$cache->purge();

	$message = '<span style="color:green; font-weight: bold;font-size: 1.5em;">phpBBlog for phpBB3 successfully installed.</span><br />
				To finish installing this mod, edit all files according to the install.xml, then open up templates/prosilver.xml and follow those instructions.<br />
				When you are finished, go to the ACP and purge the cache.<br />
				<br />
				<span style="color:green; font-weight: bold;font-size: 1.5em;">phpBBlog für phpBB3 erfolgreich installiert.</span><br />
				Um die Installation abzuschliessen befolge alle Anweisungen in der Install.xml, danach die templates/prosilver.xml und language/de.xml .<br />
				Wenn Du damit fertig bist, gehe in das ACP und leere den Cache.';
	trigger_error($message);
} 
else 
{
	$message = '<span style="color:green; font-weight: bold;font-size: 1.5em;">phpBBlog v1.2.0 (beta)</span><br />
				<br />
				English:<br />
				Script for automated phpBBlog for phpBB3 table generation.<br />
				<br />
				<span style="color:red; font-weight: bold;">This procedure will erase all settings, drivers, teams, races and usertipps of any previous phpBBlog installations!</span><br />
				Are you sure you want to continue? If so, then click on "Continue / Weiter"<br />
				<br />
				<br />
				German:<br />
				Script für die automatische phpBBlog für phpBB3 Tabellen Erstellung.<br />
				<br />
				<span style="color:red; font-weight: bold;">Diese Script wird alle phpBBlog Einstellungen, Einträge und Kaegroien von vorherigen Installationen löschen!</span><br />
				Bist Du Dir absolut sicher ? Dann klicke auf "Continue / Weiter"<br />
				<br />
				';
	$message .= '%sContinue / Weiter%s ----- %sCancel / Abbrechen%s';
	$message  = sprintf($message, '<a href="'.append_sid("install.$phpEx?install=continue").'" class="gen">', '</a>', '<a href="'.append_sid( $phpbb_root_path . "index.$phpEx").'" class="gen">', '</a>');
	
	trigger_error( $message);
}
?>
</body>
</html>