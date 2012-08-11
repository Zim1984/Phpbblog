<?php
/**
*
* @package phpBB3
* @version $Id: trackback.php,v 1.2.0 2008/02/18 15:35:25 Skippy Exp $
* @copyright  (C) 2008 Sebastian Schmidt - Skippy
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . '/includes/phpBBlog/blog_class.'.$phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
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


if (!$blog_config['disable_trackbacks']) {
	die('Trackbacks are not enabled on this blog.');
}
$path_params = explode("/",$_SERVER['PATH_INFO']);
if (isset($path_params[1])) {
	$topic_id = preg_replace('/[^0-9]/', '', $path_params[1]);
	$trackback = $phpbb_root_path . 'trackback.' . $phpEx . '/' . $topic_id;
}
else {
	header('Content-type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n\n";
?>
<response>
<error>1</error>
<message>There was an error, your request cannot be processed without an id appended to the URL.  It is possible that the Web server in use is not compatible with the Apache PATH_INFO system.</message>
</response>
<?php
	exit();
}
/*
Process a trackback submission.
*/
if (isset($_POST['url'])) {
	$tb_url = $_POST['url'];
	$tb_blog_name = cleanup($_POST['blog_name']);
	$tb_blog_name = shorten($tb_blog_name, 127);
	$tb_title = cleanup($_POST['title']);
	$tb_title = shorten($tb_title, 127);
	$tb_excerpt = cleanup($_POST['excerpt']);
	$tb_excerpt = shorten($tb_excerpt, 255);
	
  $sql = 'INSERT INTO ' . BLOG_TRACKBACK_TABLE . ' (topic_id, ip_address, url, blog_name, title, excerpt) 
	VALUES("' . $topic_id . '","' . $REMOTE_ADDR . '","' . $tb_url . '","' . $tb_blog_name . '","' . $tb_title . '","' . $tb_excerpt . '")';


	header('Content-type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n\n";
	if ($result = $db->sql_query($sql)) {
?>
<response>
<error>0</error>
</response>
<?php
		exit();
	}
	else {
?>
<response>
<error>1</error>
<message>There was an error, your trackback was not processed.</message>
</response>
<?php
		exit();
	}
}

/*
List all trackbacks, in RSS format.
*/
else if (isset($_GET['__mode'])) {
	$sql = 'SELECT p.post_subject, p.post_text FROM ' . $table_prefix;
	$sql .= 'topics as t, ' . $table_prefix . 'posts_text as p ';
	$sql .= 'WHERE t.forum_id=' . $forum . ' AND t.topic_id=' . $topic_id . ' AND t.topic_first_post_id=p.post_id';
	header('Content-type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n\n";
	if ($result = $db->sql_query($sql)) {
		$topic_url = $blog_url . '?permalink=' . $topic_id;
		$topic_subject = '';
		while ($row = $db->sql_fetchrow($result)) {
			$topic_subject = $row['post_subject'];
			$topic_text = $row['post_text'];
			$topic_text = cleanup($topic_text);
			$topic_text = shorten($topic_text, 255);
		}
		$sql = 'SELECT url, title, excerpt FROM pb3_trackbacks WHERE topic_id=' . $topic_id;
		if ($result = $db->sql_query($sql)) {
?>
<response>
<error>0</error>
<rss version="0.91">
<channel>
<title><?php echo $topic_subject; ?></title>
<link><?php echo $topic_url; ?></link>
<description><?php echo $topic_text; ?></description>
<language><?php echo $language; ?></language>
<?php
			/*
			Loop through the results, clean 'em up, and print 'em out.
			*/
			while ($row = $db->sql_fetchrow($result)) {
				$tb_url = $row['url'];
				$tb_title = $row['title'];
				$tb_excerpt = $row['excerpt'];
?>
<item>
<title><?php echo $tb_title; ?></title>
<link><?php echo $tb_url; ?></link>
<description><?php echo $tb_excerpt; ?></description>
</item>
<?php
			}
?>
</channel>
</rss>
</response>
<?php
			exit();
		}
		else {
?>
<response>
<error>1</error>
<message>There was an error, no results.</message>
</response>
<?php
			exit();
		}
	}
	else {
?>
<response>
<error>1</error>
<message>There was an error, no results.</message>
</response>
<?php
		exit();
	}
}
$response_code = '';
if (isset($_POST['task'])) {
	if (($_POST['task'] == 'killbadstuff') && ($userdata['user_id'] > 1)) {
		$sql = 'DELETE FROM pb3_trackbacks WHERE trackback_id=';
		$baditems = $_POST['nottobe'];
		$tb_minicount = 0;
		for ($tb_index = 0; $tb_index < count($baditems); $tb_index++) {
			$minisql = 'SELECT t.topic_poster ';
			$minisql .= 'FROM pb3_trackbacks AS b, ' . $table_prefix . 'topics AS t ';
			$minisql .= 'WHERE b.topic_id=' . $topic_id . ' AND b.trackback_id=' . $baditems[$tb_index] . ' AND b.topic_id=t.topic_id';
			if ($result = $db->sql_query($minisql)) {
				while ($row = $db->sql_fetchrow($result)) {
					$mini_id = $row['topic_poster'];
				}
			}
			else {
				message_die(GENERAL_ERROR, 'Querying the database didn\'t work.  Feeling helpful?  Email the webmaster.');
			}
			if ($mini_id == $userdata['user_id']) {
				if ($tb_minicount > 0) {
					$sql .= ' OR trackback_id=';
				}
				$sql .= "'" . $baditems[$tb_index] . "'";
				$tb_minicount++;
			}
		}
		if ($tb_minicount) {
			if (!$result = $db->sql_query($sql)) {
				message_die(GENERAL_ERROR, 'Querying the database didn\'t work.  Feeling helpful?  Email the webmaster.');
			}
			else {
				$response_code = '<p>item(s) deleted</p>';
			}
		}
	}
}
/*
If we made it this far, show the trackbacks in HTML format.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">

<head>
<base target="_top" />
<link href="../stylesheets/<?php echo $stylesheet; ?>.css" rel="stylesheet" title="<?php echo ucfirst($stylesheet); ?>" type="text/css" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $trackback,'?__mode=rss'; ?>">
<meta name="developer" content="Anthony Boyd" />
<title><?php echo $blog_name; ?></title>
</head>

<body>

<div id="old_content" style="padding: 3pt;">
<?php echo $response_code; ?>
<div style="border: 1pt solid black; margin-bottom: 3pt;">
TrackBack URL for this entry:<br />
<small><?php echo $trackback; ?></small>
</div>
<form method="POST" action="<?php echo $trackback; ?>">
<input type="hidden" name="task" value="killbadstuff" />
<?php
/*
What's faster?  A JOIN or two SELECTS?  Going with JOIN to start.
*/
$sql = 'SELECT b.trackback_id, b.url, b.title, b.excerpt, t.topic_poster ';
$sql .= 'FROM pb3_trackbacks AS b, ' . $table_prefix . 'topics AS t ';
$sql .= 'WHERE b.topic_id=' . $topic_id . ' AND b.topic_id=t.topic_id';
$killbutton = '';
if ($result = $db->sql_query($sql)) {
	/*
	Loop through the results, clean 'em up, and print 'em out.
	*/
	$loop_count = 0;
	while ($row = $db->sql_fetchrow($result)) {
		$loop_count++;
		$tb_id = $row['trackback_id'];
		$tb_url = $row['url'];
		$tb_title = $row['title'];
		$tb_excerpt = $row['excerpt'];
		$tb_poster = $row['topic_poster'];
		if (($tb_poster == $userdata['user_id']) && ($userdata['user_id'] > 1)) {
			$killbutton = '<input type="checkbox" name="nottobe[]" value="' . $tb_id . '" />';
		}
?>
<hr />
<p><?php echo $killbutton; ?> <a href="<?php echo $tb_url; ?>" rel="nofollow"><?php echo $tb_title; ?></a><br />
<?php echo $tb_excerpt; ?></p>
<?php
	}
	if ($loop_count == 0) {
?>
<p>No trackbacks found.</p>
<?php
	}
	else if ($killbutton) {
?>
<input type="submit" value="delete selected items" />
<?php
	}
}
else {
?>
<p>Unknown error.  Sorry.  :(</p>
<?php
}
?>
</form>
<hr />
<p><small>Powered by <a href="http://www.outshine.com/phpbbblog/" target="_blank">phpBB Blog</a>.</small></p>
</div>

</body>
</html>
