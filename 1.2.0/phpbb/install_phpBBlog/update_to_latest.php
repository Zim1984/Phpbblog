<?php
/** 
*
* \install_phpBBlog\install.php
*
* @package
* @version $Id: update_to_latest.php 1 2007-07-30 13:25:14Z stoffel04 $
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

define ('BLOG_TABLE', $table_prefix.'blog');
define ('BLOG_TRACKBACKS_TABLE', $table_prefix.'blog_trackbacks');
define ('BLOG_COM_TABLE', $table_prefix.'blog_com');
define ('BLOG_CAT_TABLE', $table_prefix.'blog_cat');
define ('BLOG_CONFIG_TABLE', $table_prefix.'blog_config');
define ('BLOG_CONFIRM_TABLE', $table_prefix.'blog_confirm');
$updates_to_version = '1.2.1';
$updates_from_version = '';

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


$sql = "SELECT config_value FROM " . BLOG_CONFIG_TABLE . " WHERE config_name = 'version'";
if (!($result = $db->sql_query($sql)))
{
  $error = $db->sql_error();
	die("Couldn't obtain version info <br /><br />" . $error['message'] . "<br />");
}

$row = $db->sql_fetchrow($result);

$updates_from_version = $row['config_value'];

if ($submit == 'continue') 
{

$message2 = '';

$sql = array();
switch ($row['config_value'])
{
	case '1.1.0':
		$message2 .= 'You have the latest Version Successfull Installed';
		$sql[] = "ALTER TABLE " . BLOG_TABLE . " ADD permit_com int(1) NOT NULL default '1'";
		$sql[] = "ALTER TABLE " . BLOG_TABLE . " ADD category_id int(2) NOT NULL default '0'";
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('allow_cat', '1')";
    $sql[] = "UPDATE " . BLOG_CONFIG_TABLE . " SET config_value = '$updates_to_version' WHERE config_name = 'version'";
		break;
	case '1.2.0':
		$message2 .= 'You have the latest Version Successfull Installed';
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('blog_online', '1')";
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('blogger_group', '')";
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('blogger_user', '')";
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('guest_com_captcha', '1')";
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('cal_archiv_menu', '1')";
    $sql[] = "INSERT INTO " . BLOG_CONFIG_TABLE . " VALUES ('blog_wordcloud', '1')";
    $sql[] = "ALTER TABLE " . BLOG_COM_TABLE . " ADD user_name varchar(25) NOT NULL default '' AFTER user_id";
    $sql[] = "ALTER TABLE " . BLOG_COM_TABLE . " ADD user_email varchar(255) default NULL AFTER user_name";
    $sql[] = "ALTER TABLE " . BLOG_COM_TABLE . " ADD protect_email smallint(1) NOT NULL default '0' AFTER user_email";
    $sql[] = "ALTER TABLE " . BLOG_COM_TABLE . " ADD email_advice smallint(1) NOT NULL default '0' AFTER protect_email";
    $sql[] = "UPDATE " . BLOG_CONFIG_TABLE . " SET config_value = '$updates_to_version' WHERE config_name = 'version'";
    $sql[] = "CREATE TABLE " . BLOG_CONFIRM_TABLE . " (
  confirm_id char(32) NOT NULL default '',
  session_id char(32) NOT NULL default '',
  code char(6) NOT NULL default '',
  PRIMARY KEY  (session_id,confirm_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`";
		break;
	case '1.2.1':
    $sql[] = "CREATE TABLE IF NOT EXISTS " . BLOG_TRACKBACKS_TABLE . " (
 tb_ID bigint(20) unsigned NOT NULL auto_increment,
 tb_entry_ID mediumint(8) NOT NULL default '0',
 tb_title varchar(255) NOT NULL default '',
 tb_blog_name varchar(255) NOT NULL default '',
 tb_blog_url varchar(200) NOT NULL default '',
 tb_blog_IP varchar(100) NOT NULL default '',
 tb_date int(11) NOT NULL default '0',
 tb_excerpt text NOT NULL,
 tb_approved enum('0','1','spam') NOT NULL default '1',
 tb_agent varchar(255) NOT NULL default '',
 PRIMARY KEY (tb_ID),
 KEY tb_approved (tb_approved),
 KEY tb_entry_ID (tb_entry_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";	
		break;	
	default:
		die("The info have not a correct value" . $row['config_value'] . "<br />");
		break;
}
	
	// Tadaa! Fill all data in ;-)	
  for( $i = 0; $i < count($sql); $i++ )
   {
    if( !$result = $db->sql_query ($sql[$i]) )
      {
        $error = $db->sql_error();
        $this->p_master->db_error($error['message'], $sql, __LINE__, __FILE__);
      }
    else
      {
        $message2 .= '<li>' . $sql[$i] . '<br /> +++ <font color="#00AA00"><b>Successfull</b></font></li><br />';
      }
   } 	
	
	unset($sql);
	
	//Destroy the old permission chache again to enable the new set :-)
	$cache->purge();

	$message = '<span style="color:green; font-weight: bold;font-size: 1.5em;">phpBBlog for phpBB3 successfully installed.</span><br />
				To finish installing this mod, edit all files according to the install.xml, then open up templates/prosilver.xml and follow those instructions.<br />
				When you are finished, go to the ACP and purge the cache.<br />
				<br />
				<span style="color:green; font-weight: bold;font-size: 1.5em;">phpBBlog für phpBB3 erfolgreich installiert.</span><br />
				Um die Installation abzuschliessen befolge alle Anweisungen in der Install.xml, danach die templates/prosilver.xml und language/de.xml .<br />
				Wenn Du damit fertig bist, gehe in das ACP und leere den Cache.';
$message = $message2.$message;

	trigger_error($message);
} 
else 
{
	$message = '<span style="color:green; font-weight: bold;font-size: 1.5em;">phpBBlog v1.2.0 (beta)</span><br />
				<br />
				English:<br />
				Script for automated phpBBlog table updates.<br />
				<br />
				<span style="color:red; font-weight: bold;">This Script will Update youre phpBlog Database from '.$updates_from_version.' to '.$updates_from_version.' !</span><br />
				Are you sure you want to continue? If so, then click on "Continue / Weiter"<br />
				<br />
				<br />
				German:<br />
				Script fürs Datenbank Update .<br />
				<br />
				<span style="color:red; font-weight: bold;">Dieses Script wird dein phpBBlog von '.$updates_from_version.' auf Version '.$updates_from_version.' updaten!</span><br />
				Bist Du Dir absolut sicher ? Dann klicke auf "Continue / Weiter"<br />
				<br />
				';
	$message .= '%sContinue / Weiter%s ----- %sCancel / Abbrechen%s';
	$message  = sprintf($message, '<a href="'.append_sid("update_to_latest.$phpEx?install=continue").'" class="gen">', '</a>', '<a href="'.append_sid( $phpbb_root_path . "index.$phpEx").'" class="gen">', '</a>');
	
	trigger_error( $message);
}
?>
</body>
</html>