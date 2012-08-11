<?php
/**
*
* yourpage [English]
*
* @package language
* @version $Id: common.php,v 1.0 2007/02/23 21:02:03 juuldr Exp $
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
    'BLOG'				=> 'Blog',
    'BLOG_NAME'				=> 'Blog name',
    'BLOG_OWNER'			=> 'Blog owner',
    'CHECK_NEW_VERSION' 		=> 'Check for a new version',
    'BLOG_ENTRY_TITEL' 			=> 'headline',
    'BLOG_ARCHIVES'			=> 'Blog archives',
    'BLOG_CATEGORIES'			=> 'Categories',
    'BLOG_MODIFY'			=> 'modify',
    'BLOG_DELETE'			=> 'delete',
    'BLOG_COMMENTS'			=> 'comments',
    'BLOG_COMMENTS_DEACTIVATED'		=> 'comments are deactivated',
    'BLOG_COM_COMMENTS'			=> 'Comment',
    'BLOG_COM_CHARS_REM'		=> 'characters remaining',
    'BLOG_NOT_AUTHORIZED'		=> 'You are not authorized',
    'BLOG_CLICK_RETURN_BLOG'		=> 'Click %sHere%s to return to the Blog',
    'BLOG_CLICK_RETURN_COM'		=> 'Click %sHere%s to return to the Blog comments',
    'BLOG_ADD_TEXT'			=> 'Add text for the Blog',
    'BLOG_CONFIRM_DELETE' 		=> 'Are you sure you want to delete this?',
    'BLOG_ADDED'		=> 'The event was added in to the Blog',
    'MAIL_SUBJECT'		=> 'A new Blog comment',
    'MAIL_CONTENT'		=> 'It gives a new comment in your Blog',
    'MAIL_SUBJECT2'		=> 'Infos about Blog',
    'MAIL_CONTENT2'		=> 'A User of this Board sends you an email with infos about a Blog',
    'SEND_AS_MAIL'		=> 'Send as an email',
    'BLOG_ENTER_MAILADDY'	=> 'Enter the email of your friend',
    'ADD_BLOG'	  		=> 'Add a Blog',
    'MODIFY_BLOG'	  		=> 'Edit a Blog',
    'PERMIT_COM'	  		=> 'allow Comments?',
    'ALLOW_CATEGORIE'  => 'Show the Categories',
    'MORE_SMILIES'		=> 'View more smilies',
  	'SMILIES'					=> 'Smilies',

    'DEFAULT'  => 'miscellaneous', 
    'NEWS'  => 'news',
    'PRIVATE'  => 'private',
    'AROUND_THE_BOARD'  => 'around the board'
));

?>