<?php
/**
*
* yourpage [English]
*
* @package language
* @version $Id: admin.php,v 1.0 2007/02/23 15:54:35 juuldr Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
// Genereal
    'BLOG'                          	=> 'Blog',
    'BLOGS'                         	=> 'Blogs',

// Intro
    'BLOG_ACP_INTRO' 					=> 'Blogs',
    'BLOG_ACP_INTRO_FULL' 				=> 'Here you can configure your blogs completely and viewing if there are updates and if there are downloading them.',
    'BLOG_VERSION_CHECK' 				=> 'Version check',
    'BLOG_VERSION_UP_TO_DATE' 			=> 'Your blog-system is up-to-date, there aren\'t any updates for your blog-system',
    'BLOG_VERSION_NOT_UP_TO_DATE' 		=> 'Your blog-system isn\t up-to-date, at the bottom of this page you can read how to upgrade',
    'BLOG_CURRENT_VERSION' 				=> 'Current version',
    'BLOG_LATEST_VERSION' 				=> 'Latest version',
    'BLOG_UPDATE_INSTRUCTIONS'      	=> 'You can find an upgrade script at the next page',
    'BLOG_CLICK_UPGRADE'            	=> 'Click here to upgrade the blog system',
    'BLOG_OFFLINE'						=> 'The blog system is currently offline',

// Settings
    'BLOG_ACP_SETTINGS'             	=> 'Blog settings',
    'BLOG_ACP_SETTINGS_EXPLAIN'			=> 'Here you can set-up the general blog settings.',
    'BLOG_ACP_SETTINGS_UPDATED'			=> 'The blog settings are succesful updated',

    'BLOG_ACP_ONLINE'					=> 'Blogs online',
    'BLOG_ACP_ONLINE_EXPLAIN'			=> 'May the blogs be viewed. If you select no nobody can view the blogs.',
    'BLOG_ACP_REACTIONS'				=> 'Reactions',
    'BLOG_ACP_REACTIONS_EXPLAIN'		=> 'If you select no it isn\t possible for everyone to react at an blog item.',

// Permissions
    'BLOG_ACP_PERMISSIONS'         	 	=> 'Blog permissions',
    'BLOG_ACP_PERMISSIONS_FULL'			=> 'Here you can find information about setting up the right permissions for your blogs.',
    'LINK_TO'							=> 'Link to the',

    'BLOG_ACP_USER_PERMISSIONS'			=> 'User permissions',
    'BLOG_ACP_USER_PERMISSIONS_FULL'    => 'Here you can set-up the permissions for an group. This affects only the permissions for one user.',
    'BLOG_ACP_GROUP_PERMISSIONS'		=> 'Groups permissions',
    'BLOG_ACP_GROUP_PERMISSIONS_FULL'	=> 'Here you can set-up the permissions for an group. This affects the permissions from all members of an group.',
    'BLOG_ACP_ADMIN_PERMISSIONS'		=> 'Administrator permissions',
    'BLOG_ACP_ADMIN_PERMISSIONS_FULL'	=> 'Here can (only the root admin) setting-up the permissions for the admins who may see this module.'
$lang['Blog_updated'] = 'Blog Configuration Updated Successfully';
$lang['Click_return_Blogconfig'] = 'Click %s here %s to return to the Blog Configuration';
$lang['General_Blog_Config'] = 'phpBBlog Configuration';
$lang['Blog_Config_explain'] = 'The form below will allow you to customize all the general phpBBlog options.';
$lang['Blog_settings'] = 'General Settings';
$lang['activate_categories'] = 'activate to manage the Blogs additional by categories';
$lang['View_of_Smilies'] = 'Number of the smile to view for columns and line';
$lang['Permit_mod'] = 'Allow moderators to use and manage the blog';
$lang['Onentry_Sendmail'] = 'Send a email to the board admin on new comment';
$lang['Allow_send_Blog'] = 'Allow users to send a blogentry as an email to friends';

));

?>