<?php
/**
*
* NV advanced last topic titles [English]
*
* @package language
* @version $Id: info_acp_altt.php 1 2007-10-15 16:30 nickvergessen $
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'LEXIKON_TITLE'			=> 'LEXIKON titles',
	'NO_CAT_SELECTED'		=> 'noch keine Kategorie gewählt',
	'CAT_NAME_EMPTY'		=> 'Kategoriename ist Leer',
	'NEW_CAT_ADDED'			=> 'Kategorie erfolgreich hinzugefügt',
	'ENTRY_KEYWORD_EMPTY'		=> 'Keyword ist Leer',
	'NEW_ENTRY_ADDED'		=> 'Eintrag erfolgreich hinzugefügt',


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
    'BLOG_ACP_CONFIG_UPDATED'			=> 'Blog Configuration Updated Successfully';
    'BLOG_ACP_Click_return_Blogconfig'			=> 'Click %s here %s to return to the Blog Configuration';
    'BLOG_ACP_General_Blog_Config'			=> 'phpBBlog Configuration';
    'BLOG_ACP_Blog_Config_explain'			=> 'The form below will allow you to customize all the general phpBBlog options.';
    'BLOG_ACP_SETTINGS'			=> 'General Settings';
    'BLOG_ACP_activate_categories'			=> 'activate to manage the Blogs additional by categories';
    'BLOG_ACP_View_of_Smilies'			=> 'Number of the smile to view for columns and line';

    'BLOG_ACP_PERMID_MOD'			=> 'MOD-Berechtigung';
    'BLOG_ACP_PERMID_MOD_EXPLAIN'			=> 'Allow moderators to use and manage the blog';
    'BLOG_ACP_BLOGGER_GROUP'			=> 'Blogger Gruppen';
    'BLOG_ACP_BLOGGER_GROUP_EXPLAIN'			=> 'Erlaubt die festlegung einer oder mehrerer Blogger gruppen';
    'BLOG_ACP_BLOGGER_USER'			=> 'Blogger';
    'BLOG_ACP_BLOGGER_USER_EXPLAIN'			=> 'erlaubt die festlegung einer oder mehrerer Blogger';

    'BLOG_ACP_ONENTRY_SENDMAIL'			=> 'admin über Kommentare informieren ?';
    'BLOG_ACP_ONENTRY_SENDMAIL_EXPLAIN'			=> 'Send a email to the board admin on new comment';
    'BLOG_ACP_Allow_send_Blog'			=> 'Blog an Freunde Senden?';
    'BLOG_ACP_Allow_send_Blog_EXPLAIN'			=> 'Allow users to send a blogentry as an email to friends';
    'BLOG_ACP_ALLOW_GUEST_COM'			=> 'Gast Kommentare';
    'BLOG_ACP_ALLOW_GUEST_COM_EXPLAIN'			=> '...';
    'BLOG_ACP_GUEST_COM_CAPTCHA'			=> 'Gast Kommentare Captcha';
    'BLOG_ACP_GUEST_COM_CAPTCHA_EXPLAIN'			=> '...';
    'BLOG_ACP_ALLOW_CATS'			=> 'Kategorien Aktivieren?';
    'BLOG_ACP_ALLOW_CATS_EXPLAIN'			=> '...';
    'BLOG_ACP_NEW_ARCHIV_MENU'			=> 'Neue Archivmenü(java)';
    'BLOG_ACP_NEW_ARCHIV_MENU_EXPLAIN'			=> '...';
    'BLOG_ACP_CAL_ARCHIV_MENU'			=> 'Menü in Calenderdesign';
    'BLOG_ACP_CAL_ARCHIV_MENU_EXPLAIN'			=> '...';


));

?>