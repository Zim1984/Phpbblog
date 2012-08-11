<?php
/**
*
* yourpage [German]
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
    'BLOG'			=> 'Blog',
    'BLOG_NAME'			=> 'Blog name',
    'BLOG_OWNER'		=> 'Blog owner',
    'CHECK_NEW_VERSION' => 'Nach neuen Updates suchen',
    'BLOG_ENTRY_TITEL'  => 'Überschrift',
    'BLOG_ARCHIVES'		=> 'Blog Archiv',
    'BLOG_CATEGORIES' => 'Kategorien',
    'BLOG_MODIFY'     => 'ändern',
    'BLOG_QUICKEDIT'  => 'quickedit',
    'BLOG_DELETE'     => 'löschen',
    'BLOG_COM_TITLE'  => 'Blog Kommentare',
    'BLOG_COMMENTS'		=> 'Kommentare',
    'BLOG_COMMENTS_DEACTIVATED'	=> 'Kommentarfunktion Deaktiviert',
    'BLOG_COMMENTS_DEACTIVATED2'	=> 'Für diesen Beitrag ist die Kommentarfunktion geschlossen',
    'BLOG_COM_COMMENT'		=> 'Kommentar',
    'BLOG_COM_CHARS_REM'	=> 'Zeichen verfügbar',
    'BLOG_NOT_AUTHORIZED'	=> 'Du bist nicht berechtigt hier zu Posten',
    'BLOG_CLICK_RETURN_BLOG'	=> 'Klicke %s hier %s um zum Blog zurückzukehren',
    'BLOG_CLICK_RETURN_COM'	=> 'Klicke %s hier %s um zu den Blog Kommentaren zurückzukehren',
    'BLOG_ADD_TEXT'		      => 'Füge einen Text zum Blog hinzu',
    'BLOG_CONFIRM_DELETE' 	=> 'Möchtest Du wirklich den Eintrag löschen ?',
    'BLOG_ADDED'		  => 'Der Eintrag wurde zum Blog hinzugefügt',
    'BLOG_PINGBACK'	=> 'Pingback',
    'BLOG_PINGBACKS'	=> 'Pingbacks',
    'BLOG_TRACKBACK'	=> 'Trackback',
    'BLOG_TRACKBACKS'	=> 'Trackbacks',
    'BLOG_SOURCE'		  => 'Quelle :',
    'BLOG_TB_URL'		  => 'Trackback URL :',
    'BLOG_RSS_URL'		  => 'RSS-Feed URL :',
    'LEAVE_COMMENTS'		=> 'Kommentar schreiben',
    'LAST_COMMENTS'		=> 'letzte Kommentare',
    'MAIL_SUBJECT'		=> 'Ein Neuer Eintrag, in deinem Blog',
    'MAIL_CONTENT'		=> 'Es gibt einen neuen Kommentar zu einem Blog',
    'MAIL_SUBJECT2'		=> 'Information über einen Blog',
    'MAIL_CONTENT2'		=> 'Ein User dieses Boards hat dir eine Email über einen Blogeintrag geschickt',
    'MAIL_SUBJECT3'		=> 'Ein Beitrag benötigt deine Aufmerksamkeit',
    'MAIL_CONTENT3'		=> 'Es gibt einen neuen Kommentar zu einem Blog',
    'MAIL_SUBJECT4'		=> 'Ein Neuer Track/Pingback, in deinem Blog',
    'MAIL_CONTENT4'		=> 'Es gibt einen neuen Kommentar zu einem Blog',
    'SEND_AS_MAIL'		=> 'Als Email versenden',
    'BLOG_ENTER_MAILADDY'	=> 'Gebe hier deine MailAdresse deines Empfängers ein',
    'ADD_BLOG'	  		=> 'Einen Blog erstellen',
    'MODIFY_BLOG'	  	=> 'Einen Blog editieren',
    'PERMIT_COM'	  	=> 'Kommentare erlauben?',
    'PERMIT_TB'	  	=> 'Track/Pingbacks erlauben?',
    'PERMIT_GUESTS'	  	=> 'Eintrag auch für Gäste zugänglich?',
    'SET_PASSWORD'	  	=> 'Eintrag und Kommantar durch Passwort geschützt?',
    'ALLOW_CATEGORIE' => 'Zeige Kategorien',
    'MORE_SMILIES'		=> 'View more smilies',
  	'SMILIES'					=> 'Smilies',
    'TOO_MANY_COMMENTS'  	=> 'Du hast für diese Session zu oft den falschen Bestätigungscode eingegeben bzw. dir das Gästebuch angeschaut. Bitte versuche es später nochmal.',


    'DEFAULT'  			 => 'Verschiedenes',
    'NEWS'  			   => 'News',
    'PRIVATE'  	     => 'Privates',
    'AROUND THE BOARD'  	=> 'Rund ums Board'
));

?>