<?php
/*
Fetch the settings file.  This MUST appear before ANY other tags.
*/
if (!@include_once('settings.php')) {
	die('Unable to open settings.php.  Feeling helpful?  Send an email to the webmaster.');
}
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>

<rss version="2.0">
	<channel>
		<title><?php echo utf8_encode($blog_name); ?></title>
		<link><?php echo utf8_encode($blog_url); ?></link>
		<description><?php echo utf8_encode($blog_summary); ?></description>
		<language><?php echo $language; ?></language>
		<copyright><?php echo utf8_encode($copyright); ?></copyright>
		<ttl><?php echo $frequency; ?></ttl>
		<image>
			<title><?php echo utf8_encode($blog_name); ?></title>
			<url><?php echo $logo_url; ?></url>
			<link><?php echo $blog_url; ?></link>
			<width><?php echo $logo_width; ?></width>
			<height><?php echo $logo_height; ?></height>
			<description><?php echo utf8_encode($blog_summary); ?></description>
		</image>
<?php

$sql = 'SELECT t.topic_id, t.topic_time, p.post_subject, p.post_text ';
$sql .= 'FROM ' . $table_prefix . 'topics as t, ' . $table_prefix . 'users as u, ' . $table_prefix . 'posts_text as p ';
$sql .= 'WHERE t.forum_id=' . $forum . ' AND t.topic_poster=u.user_id AND t.topic_first_post_id=p.post_id ';
if ($hide_stickies) {
	$sql .= 'AND t.topic_type=0 ';
}
$sql .= 'ORDER BY t.topic_time DESC ';
$sql .= 'LIMIT ' . $max_rss_entries;

if (!$result = $db->sql_query($sql)) {
	message_die(GENERAL_ERROR, 'Querying the database didn\'t work.  Feeling helpful?  Email the webmaster.');
}

while ($row = $db->sql_fetchrow($result)) {
	$topic_id = $row['topic_id'];
	$topic_time = $row['topic_time'];
	$subject = $row['post_subject'];
	$text = $row['post_text'];
	$blog_time = ($show_dates) ? gmdate('D, d M Y H:i:s \G\M\T', $topic_time) : '';
	$topic_url = $phpbb_url . 'viewtopic.' . $phpEx . '?t=' . $topic_id;
	$blog_permalink = $blog_url . '?permalink=' . $topic_id;
	$text = cleanup($text);
	$text = shorten($text, $rss_length);
	$podcast_path = $phpbb_root_path . 'podcast/' . $topic_id . '.mp3';
	$podcast_tag = '';
	if (file_exists($podcast_path)) {
		$podcast_url = $phpbb_url . 'podcast/' . $topic_id . '.mp3';
		$podcast_tag = '<enclosure url="' . $podcast_url . '" length="';
		$podcast_tag .= filesize($podcast_path) . '" type="audio/mpeg" />';
	}
?>
		<item>
			<pubDate><?php echo utf8_encode($blog_time); ?></pubDate>
			<title><?php echo utf8_encode($subject); ?></title>
			<guid><?php echo $blog_permalink; ?></guid>
			<link><?php echo $blog_permalink; ?></link>
			<?php echo $podcast_tag; ?>
			<description><?php echo utf8_encode($text); ?></description>
		</item>
<?php
}
?>
	</channel>
</rss>
