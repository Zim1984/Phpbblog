<?php

/*
*
* @package phpBB3
* @version $Id: rss.php,v 1.2.1 2008/02/26 22:29:16 Skippy Exp $
* @copyright (C) 2008 Sebastian Schmidt - Skippy
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/



/***************************************************************************

*
*   PHPBB 3.0 RDF CONTENT SYNDICATOR
*   Shows last active topics RDF XML form
*

***************************************************************************

*
*   Put this file in your phpBB3 directory.
*   You can call this script without parameters, what will
*   result in an RSS_FEED with the x latest posts from all your forums.
*   x is specified in $CFG['default_topics']
*
*   You can specify the number of posts via the url parameter count:
*
*   http://www.domain.com/phpBB3/rss.php?count=50
*
*   This will result in an RSS_FEED with the latest 50 posts from all your forums.
*
*   You can also specify to look only at a specified forum using the fid parameter:
*
*   http://www.domain.com/phpBB3/rss.php?catid=9
*
*   This will result in an RSS_FEED with the latest x posts from your forum with the id 9.
*
*   The paramter t=1 says only New topics are listed
*   The parameter chars=200 says only the first 200 charackters of post would display for preview
*
*   You can also mix the paramters:
*
*   http://www.domain.com/phpBB2/blog_rss.php?catid=5,6&count=3
*
*   will show you the latest 3 posts from forum 5 and 6.
*
*   THIS SCRIPT WILL ONLY SHOW POSTS FROM PUBLIC FORUMS
*
***************************************************************************/







/**
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/phpBBlog/blog_class.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);


//
// Start session management
//
$user->session_begin();
$user->setup();
$auth->acl($user->data);
//
// End session management
//


$user->add_lang('mods/phpBBlog/common');

$blog_config = array();
$phpbblog_c = new phpbblog();
$blog_config = $phpbblog_c->blog_config();


if ($blog_config['allow_rss']) {
	die('RSS-Feeds are not enabled on this blog.');
}



// Begin Configuration Section
$CFG['exclude_cats'] = '';
$CFG['default_entrys'] = '20';
$CFG['max_entrys']     = '50';
// End Configuration Section





// If not set, set the output count to max_topics
$count = request_var('count', 0);
$count = ( $count == 0 ) ? $CFG['default_entrys'] : $count;
$count = ( $count > $CFG['max_entrys'] ) ? $CFG['max_entrys'] : $count;



$catid = request_var('catid', '');
$blogid = request_var('blogid', '');
$user_only = request_var('userid', '');
// Zeichen
$chars = request_var('chars', 200);
if($chars<0 || $chars>500) $chars=500; //Maximum







// [+] define path 



// Create main board url (some code borrowed from functions_post.php)

//$script_path = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($config['script_path']));



// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information

$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');

if (!$script_name)
{
    $script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
}

$script_path = trim(dirname($script_name));
$script_path = preg_replace('#[\\\\/]{2,}#', '/', $script_path);

$server_name = trim($config['server_name']);
$server_protocol = ( $config['cookie_secure'] ) ? 'https://' : 'http://';
//$server_port = ( $config['server_port'] <> 80 ) ? ':' . trim($config['server_port']) . '/' : '/';
$server_port = ( $config['server_port'] <> 80 ) ? ':' . trim($config['server_port']) . '' : '';


$url = $server_protocol . $server_name . $server_port;
//$url .= ( $script_path != '' ) ? $script_path . '/' : '';
$url .= ( $script_path != '' ) ? $script_path . '' : '';

$viewblog = ( $script_path != '' ) ? $script_path . '/blog.' . $phpEx : 'blog.'. $phpEx;
$index = ( $script_path != '' ) ? $script_path . '/index.' . $phpEx : 'index.'. $phpEx;

$index_url = $server_protocol . $server_name . $server_port . $index;
$viewblog_url = $server_protocol . $server_name . $server_port . $viewblog;

// [-] define path 





//

// Strip all BBCodes and Smileys from the post

//

function strip_post($text, $uid, $bitfield)
{
	global $config, $base_url;


  //parse message for display
  $bbcode_bitfield = '';
  $bbcode_bitfield = $bbcode_bitfield | base64_decode($bitfield);
  if ($bbcode_bitfield !== '')
   {
	$bbcode = new bbcode(base64_encode($bbcode_bitfield));
   }
  

  if ($bitfield)
   {
	$bbcode->bbcode_second_pass($text, $uid, $bitfield);
   }



$text = make_clickable($text);

                if($config['allow_smilies'])
                {
                        $text = smiley_text($text);
                        $text = str_replace("./".$config['smilies_path'], $base_url.$config['smilies_path'], $text);
                }

    // for BBCode
    //$text = preg_replace("#\[\/?[a-z0-9\*\+\-]+(?:=.*?)?(?::[a-z])?(\:?$uid)\]#", '', $text);

    // for smileys
    $text = preg_replace('#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/.*? \/><!\-\- s(.*?) \-\->#', '', $text);

    // html format
    $text = str_replace('&amp;#', '&#', htmlspecialchars($text, ENT_QUOTES));





/*
                if($uid != '')
                {
                        $text = ( $config['allow_bbcode'] ) ? $text : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $text);
                }
                else
                {
                        $text = preg_replace('/\:[0-9a-z\:]+\]/si', ']', $text);
                }
                $text = make_clickable($text);
                if($config['allow_smilies'])
                {
                        $text = smiley_text($text);
                        $text = str_replace("./".$config['smilies_path'], $base_url.$config['smilies_path'], $text);
                }
        $text = nl2br($text);
        $text = str_replace('&pound', '&amp;#163;', $text);
        $text = str_replace('&copy;', '(c)', $text);

*/
    return $text;
}




// Exclude forums
$sql_where = '';
if ($CFG['exclude_cats'])
{
    $exclude_cats = explode(',', $CFG['exclude_cats']);
    foreach ($exclude_cats as $i => $id)
    {
        if ($id > 0)
        {
            $sql_where .= ' AND b.category_id != ' . trim($id);
        }
    }
}



if ($catid != '')
{
    $select_cats = explode(',', $catid);
    $sql_where .= ( sizeof($select_cats)>0 ) ? ' AND b.category_id IN (' . $catid . ')' : '';
}


if ( $user_only != '')
{
   $select_user = explode(',', $user_only);
   $sql_where .= ( sizeof($select_user)>0 ) ? ' AND b.user_id IN (' . $user_only . ')' : '';
}



$output  = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
$output .= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:annotate="http://purl.org/rss/1.0/modules/annotate/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";

$output .= '<channel>' . "\n";
$output .= '<title>' . strip_tags(utf8_encode($blog_name)) . '</title>' . "\n";
$output .= '<link>' . utf8_encode($blog_url) . '</link>' . "\n";
$output .= '<copyright>Copyright � 2008-present, yourdomain.com</copyright>' . "\n";
$output .= '<generator>phpBBlog RSS Builder 1.2.1</generator>' . "\n";
$output .= '<description>' . strip_tags(utf8_encode($config['site_desc'])) . '</description>' . "\n";
$output .= '<language>de</language>' . "\n";

/*
$output .= '<image>' . "\n";
$output .= '<title>infoMantis Logo</title>' . "\n";
$output .= '<url>http://www.infomantis.de/download/logo.gif</url>' . "\n";
$output .= '<link>http://www.infomantis.de/</link>' . "\n";
$output .= '<width>200</width>' . "\n";
$output .= '<height>55</height>' . "\n";
$output .= '<description>infoMantis GmbH</description>' . "\n";
$output .= '</image>' . "\n";
*/


if ($blogid != '')
{
// SQL posts table
$sql = 'SELECT b.id_blog, b.user_id, b.date, b.text, u.username 
        FROM ' . BLOG_COM_TABLE . ' as b, ' . USERS_TABLE . ' as u 
        WHERE (u.user_id = b.user_id)
        AND (b.id_blog = ' . $blogid . ')
        ORDER BY date DESC';
}
else
{
// SQL posts table
$sql = 'SELECT b.id, b.user_id, b.date, b.titel, b.text, b.bbcode_uid, b.bbcode_bitfield, b.flags, b.permit_com, b.category_id, c.cat_id, c.cat_titel, u.username 
        FROM ' . BLOG_TABLE . ' as b, ' . BLOG_CAT_TABLE . ' as c, ' . USERS_TABLE . ' as u 
        WHERE (u.user_id = b.user_id)
        AND (c.cat_id = b.category_id)
        ' . $sql_where . '
        ORDER BY date DESC';
}


$result = $db->sql_query_limit($sql, $count);





while( ($row = $db->sql_fetchrow($result)) )
{
     // if (!$auth->acl_get('f_list', $row['forum_id']))
     // {
         // if the user does not have permissions to list this forum, skip everything until next branch
     //    continue;
     // }



// [+] user_id to username
        $poster_u = append_sid("$url/memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $row['user_id']);
        $poster = '<a href='. $poster_u .'>' . $row['username'] . '</a>';
// [-] user_id to username



// [+] category title
        $cat_title_u = append_sid("$url/blog.$phpEx", 'cats=' . $row['category_id']);
        $cat_title   = '<a href='. $cat_title_u .'>' . $row['cat_titel'] . '</a>';
// [-] category title



    $entry_id = $row['id'];
    $cat_id = $row['category_id'];
    $entry_title = $row['titel'];
    $post_time = date('Y-m-d', $row['date']);
    $viewblog = append_sid("$url/blog.$phpEx", 'id=' . $row['id']);

    $output .= "<item>\n";
    $output .= "\t<title>" . utf8_encode($cat_title). "</title>\n";

    $entry_text = $row['text'];
    $entry_text = censor_text($entry_text);
    $entry_text = str_replace("\n", '<br />', $entry_text);
    $entry_text = strip_post($entry_text, $row['bbcode_uid'], $row['bbcode_bitfield']);
    if (strlen($entry_text) > $chars) $entry_text = substr($entry_text,0,$chars)."...";



        

    $output .= "\t<description> 
    Author: " . $poster . "<br /> 
    Forum: " . $entry_title . " <br />
    Date: " . $post_time . " <br /><br />" . $entry_text . "</description>\n";


    $output .= "\t<guid>$viewblog</guid>\n";
    $output .= "\t<link>$viewblog</link>\n";
    $output .= "</item>\n\n";
}

$output .= "</channel>\n</rss>";







header('Content-Type: text/xml; charset=utf-8');
echo $output;


?>