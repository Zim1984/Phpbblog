<?php
/***************************************************************************
 *                              admin_guestbook.php
 *                            -------------------
 *   begin                : Thursday, Jul 12, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   $Id: admin_board.php,v 1.51.2.9 2004/11/18 17:49:33 acydburn Exp $
 *
 *
 ***************************************************************************/

define('IN_PHPBB', 1);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Main']['Blog config'] = "$file";
	return;
}

//
// Let's set the root dir for phpBB
//
$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'includes/blog_class.'.$phpEx);



$blog_config = array();
$phpbblog_c = new phpbblog();
$blog_config = $phpbblog_c->blog_config();



	while (list($config_name, $config_value) = each ($blog_config))
	{
		$default_config[$config_name] = isset($HTTP_POST_VARS['submit']) ? str_replace("'", "\'", $config_value) : $config_value;

		$blog_config[$config_name] = ( isset($HTTP_POST_VARS[$config_name]) ) ? $HTTP_POST_VARS[$config_name] : $default_config[$config_name];

		if( isset($HTTP_POST_VARS['submit']) )
		{
            $phpbblog_c->update_blog($config_name, $blog_config[$config_name]);
		}
	}

	if( isset($HTTP_POST_VARS['submit']) )
	{
		$message = $lang['Blog_updated'] . "<br /><br />" . sprintf($lang['Click_return_Blogconfig'], "<a href=\"" . append_sid("admin_blog.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	



$permit_mod_yes = ( $blog_config['permit_mod'] ) ? "checked=\"checked\"" : "";
$permit_mod_no = ( !$blog_config['permit_mod'] ) ? "checked=\"checked\"" : "";

$onentry_sendmail_yes = ( $blog_config['onentry_sendmail'] ) ? "checked=\"checked\"" : "";
$onentry_sendmail_no = ( !$blog_config['onentry_sendmail'] ) ? "checked=\"checked\"" : "";

$allow_cat_yes = ( $blog_config['allow_cat'] ) ? "checked=\"checked\"" : "";
$allow_cat_no = ( !$blog_config['allow_cat'] ) ? "checked=\"checked\"" : "";
	
$template->set_filenames(array(
	"body" => "admin/blog_config_body.tpl")
);

$template->assign_vars(array(
	"S_CONFIG_ACTION" => append_sid("admin_blog.$phpEx"),

	"L_YES" => $lang['Yes'],
	"L_NO" => $lang['No'],
	"L_CONFIGURATION_TITLE" => $lang['General_Blog_Config'],
	"L_CONFIGURATION_EXPLAIN" => $lang['Blog_Config_explain'],
	"L_GENERAL_SETTINGS" => $lang['Blog_settings'],
  "L_VERSION" => $blog_config['version'],
	
	"L_ENABLED" => $lang['Enabled'],
	"L_DISABLED" => $lang['Disabled'],
	"L_PERMIT_MOD" => $lang['Permit_mod'],
	"L_ONENTRY_SENDMAIL" => $lang['Onentry_Sendmail'],
	"L_ALLOW_CAT" => $lang['Allow_Categories'],
	"L_VIEW_SMILE" => $lang['View_of_Smilies'],

	"L_SUBMIT" => $lang['Submit'],
	"L_RESET" => $lang['Reset'],
	

	"PERMIT_MOD_YES" => $permit_mod_yes,
	"PERMIT_MOD_NO" => $permit_mod_no,
	"ONENTRY_SENDMAIL_YES" => $onentry_sendmail_yes,
	"ONENTRY_SENDMAIL_NO" => $onentry_sendmail_no,
	"ALLOW_CAT_YES" => $allow_cat_yes,
	"ALLOW_CAT_NO" => $allow_cat_no,	
	"SMILIES_COLUMN" => $blog_config['smilies_column'],
	"SMILIES_ROW" => $blog_config['smilies_row'],

  )
);

$template->pparse("body");

include('./page_footer_admin.'.$phpEx);

?>
